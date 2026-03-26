<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MongoService;
use App\Services\TrackingMongoService;
use App\Services\CloudinaryService;
use App\Models\AssemblyConstituency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminPanelController extends Controller
{
    protected MongoService $mongo;
    protected TrackingMongoService $tracking;
    protected $cloudinary;

    public function __construct(MongoService $mongo, TrackingMongoService $tracking)
    {
        $this->mongo = $mongo;
        $this->tracking = $tracking;
        $this->cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
    }

    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $adminUsername = config('services.admin.username');
        $adminPasswordHash = config('services.admin.password_hash');

        if ($request->username === $adminUsername &&
            password_verify($request->password, $adminPasswordHash)) {
            $request->session()->regenerate();
            session([
                'admin_logged_in' => true,
                'admin_username' => $request->username,
            ]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['error' => 'Invalid username or password.']);
    }

    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_username']);
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $stats = $this->mongo->getStats();
        return view('admin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $search = $request->input('search', '');
        $assembly = $request->input('assembly', '');
        $district = $request->input('district', '');

        $result = $this->mongo->getAllMembers($page, 20, $search ?: null, $assembly ?: null, $district ?: null);

        $assemblies = $this->mongo->getDistinctValues('assembly');
        $districts = $this->mongo->getDistinctValues('district');

        return view('admin.users', [
            'members' => $result['members'],
            'total' => $result['total'],
            'page' => $result['page'],
            'pages' => $result['pages'],
            'search' => $search,
            'assembly' => $assembly,
            'district' => $district,
            'assemblies' => $assemblies,
            'districts' => $districts,
        ]);
    }

    public function userDetail(string $uniqueId)
    {
        $member = $this->mongo->findMemberByUniqueId($uniqueId);

        if (!$member) {
            abort(404, 'Member not found.');
        }

        unset($member['pin_hash']);

        $referredMembers = $this->mongo->getMembersReferredBy($uniqueId);

        // Fetch referred_by person details
        $referredByMember = null;
        if (!empty($member['referred_by'])) {
            $referredByMember = $this->mongo->findMemberByUniqueId($member['referred_by']);
            if ($referredByMember) {
                unset($referredByMember['pin_hash']);
            }
        }

        // Fetch loan request data
        $loanRequest = null;
        try {
            $loanRequest = $this->mongo->getLoanRequestByUniqueId($uniqueId);
        } catch (Exception $e) {
            \Log::error("Error fetching loan request: " . $e->getMessage());
        }

        return view('admin.user-detail', [
            'member' => (object) $member,
            'referred_members' => $referredMembers,
            'referred_by_member' => $referredByMember ? (object) $referredByMember : null,
            'loan_request' => $loanRequest ? (object) $loanRequest : null,
        ]);
    }

    /**
     * Extract Cloudinary public_id from a full URL.
     */
    private function extractPublicId($url)
    {
        if (!$url) return null;
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * POST /admin/users/{uniqueId}/update
     * Admin edit member details + delete old card images from Cloudinary.
     */
    public function updateMember(Request $request, string $uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);
            if (!$member) {
                return back()->with('error', 'Member not found.');
            }

            $fields = [];
            $editableKeys = ['name', 'epic_no', 'assembly', 'district', 'zone', 'dob', 'age', 'blood_group', 'address'];
            foreach ($editableKeys as $key) {
                if ($request->has($key)) {
                    $fields[$key] = trim($request->input($key));
                }
            }

            // Recalculate district & zone from assembly via zone_data config
            if (!empty($fields['assembly'])) {
                $zoneData = config('zone_data.assembly_map') ?? [];
                $asmUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $fields['assembly'])));
                $matched = $zoneData[$asmUpper] ?? null;
                if (!$matched) {
                    $norm = preg_replace('/[\.\-\(\)]/', '', $asmUpper);
                    $norm = preg_replace('/\s+/', ' ', trim($norm));
                    foreach ($zoneData as $k => $v) {
                        $nk = preg_replace('/[\.\-\(\)]/', '', $k);
                        $nk = preg_replace('/\s+/', ' ', trim($nk));
                        if ($nk === $norm) { $matched = $v; break; }
                    }
                }
                if ($matched) {
                    $fields['district'] = ucwords(strtolower($matched['d']));
                    $fields['zone'] = ucwords(strtolower($matched['z']));
                }
            }

            // Recalculate age from DOB
            if (!empty($fields['dob'])) {
                try {
                    $dobDate = \DateTime::createFromFormat('d/m/Y', $fields['dob'])
                        ?: \DateTime::createFromFormat('Y-m-d', $fields['dob'])
                        ?: new \DateTime($fields['dob']);
                    $fields['age'] = (string) $dobDate->diff(new \DateTime())->y;
                } catch (\Exception $e) {}
            }

            // Update in MongoDB
            $this->mongo->updateMemberFieldsByUniqueId($uniqueId, $fields);

            // Delete old card images from Cloudinary using direct SDK
            try {
                $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/front');
                $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/back');
                Log::info("Deleted old card images for: {$uniqueId}");
            } catch (\Exception $e) {
                Log::warning("Failed to delete old cards from Cloudinary: " . $e->getMessage());
            }

            // Clear card URLs so the detail page knows to regenerate
            $this->mongo->updateMemberFieldsByUniqueId($uniqueId, [
                'card_front_url' => '',
                'card_back_url' => '',
            ]);

            Log::info("Admin updated member: {$uniqueId}");

            return redirect()->route('admin.user.detail', $uniqueId)->with('success', 'Member updated successfully. Card will be regenerated.');
        } catch (Exception $e) {
            Log::error("AdminPanelController::updateMember Error: " . $e->getMessage());
            return back()->with('error', 'Failed to update member: ' . $e->getMessage());
        }
    }

    /**
     * POST /admin/users/{uniqueId}/regenerate-card
     * Server-side card image regeneration: accepts base64 front/back from admin page JS,
     * uploads to Cloudinary, updates MongoDB.
     */
    public function regenerateCard(Request $request, string $uniqueId)
    {
        try {
            $request->validate([
                'front_image' => 'required|string',
                'back_image'  => 'required|string',
            ]);

            $urls = [];
            foreach (['front' => $request->input('front_image'), 'back' => $request->input('back_image')] as $side => $dataUrl) {
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $dataUrl);
                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
                $tempPath = $tempDir . '/card_' . $uniqueId . '_' . $side . '.png';
                file_put_contents($tempPath, base64_decode($base64));

                $result = $this->cloudinary->uploadApi()->upload($tempPath, [
                    'folder'        => 'vanigan/cards/' . $uniqueId,
                    'public_id'     => $side,
                    'overwrite'     => true,
                    'resource_type' => 'image',
                ]);

                $urls[$side . '_url'] = $result['secure_url'] ?? '';
                @unlink($tempPath);
            }

            $this->mongo->updateCardUrls($uniqueId, $urls['front_url'], $urls['back_url']);

            Log::info("Admin regenerated card for: {$uniqueId}");

            return response()->json(['success' => true, 'front_url' => $urls['front_url'], 'back_url' => $urls['back_url']]);
        } catch (Exception $e) {
            Log::error("AdminPanelController::regenerateCard Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/users/{uniqueId}/delete
     * Remove member from MongoDB + Cloudinary (photo, cards, folder) + loan requests.
     */
    public function deleteMember(string $uniqueId)
    {
        try {
            $member = $this->mongo->findMemberByUniqueId($uniqueId);
            if (!$member) {
                return redirect()->route('admin.users')->with('error', 'Member not found.');
            }

            // Delete photo from Cloudinary
            $photoPublicId = $this->extractPublicId($member['photo_url'] ?? '');
            if ($photoPublicId) {
                try { $this->cloudinary->uploadApi()->destroy($photoPublicId); } catch (\Exception $e) {}
            }

            // Delete card images and folder from Cloudinary
            try {
                $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/front');
                $this->cloudinary->uploadApi()->destroy('vanigan/cards/' . $uniqueId . '/back');
                // Delete all remaining resources under this folder prefix
                $this->cloudinary->adminApi()->deleteAssetsByPrefix('vanigan/cards/' . $uniqueId);
                // Delete the empty folder itself
                $this->cloudinary->adminApi()->deleteFolder('vanigan/cards/' . $uniqueId);
                Log::info("Deleted Cloudinary folder: vanigan/cards/{$uniqueId}");
            } catch (\Exception $e) {
                Log::warning("Failed to delete cards/folder: " . $e->getMessage());
            }

            // Delete from MongoDB: members, manual_entries, loan_requests
            $this->mongo->deleteMemberByUniqueId($uniqueId);
            $this->mongo->deleteManualEntryByUniqueId($uniqueId);
            $this->mongo->deleteLoanRequestByUniqueId($uniqueId);

            $memberName = $member['name'] ?? 'N/A';
            Log::info("Admin deleted member: {$uniqueId} ({$memberName})");

            return redirect()->route('admin.users')->with('success', "Member {$uniqueId} ({$memberName}) deleted successfully.");
        } catch (Exception $e) {
            Log::error("AdminPanelController::deleteMember Error: " . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    public function voters(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $limit = 25;
        $search = trim($request->input('search', ''));
        $assemblyFilter = trim($request->input('assembly', ''));
        $districtFilter = trim($request->input('district', ''));

        try {
            $assemblies = AssemblyConstituency::getDistinctAssemblyNames();
            $districts = AssemblyConstituency::getDistinctDistrictNames();

            // Determine which tables to search
            if ($assemblyFilter) {
                $tableName = AssemblyConstituency::getTableByAssembly($assemblyFilter);
                $tables = $tableName ? [$tableName] : [];
            } elseif ($districtFilter) {
                // District filter may come from zone_data config (correct spelling)
                // which differs from MySQL district names, so resolve via assembly_map
                $zoneAsmMap = config('zone_data.assembly_map') ?? [];
                $matchedAssemblies = [];
                $distUpper = strtoupper(trim($districtFilter));
                foreach ($zoneAsmMap as $asmKey => $asmInfo) {
                    if (strtoupper($asmInfo['d']) === $distUpper) {
                        $matchedAssemblies[] = $asmKey;
                    }
                }
                if (!empty($matchedAssemblies)) {
                    $tables = [];
                    foreach ($matchedAssemblies as $asmName) {
                        $t = AssemblyConstituency::getTableByAssembly($asmName);
                        if ($t) $tables[] = $t;
                    }
                } else {
                    $tables = AssemblyConstituency::getTablesByDistrict($districtFilter);
                }
            } else {
                $tables = AssemblyConstituency::getAllVoterTables();
            }

            $voters = [];
            $total = 0;

            if (!empty($tables) && $search) {
                // Search with UNION across tables
                $searchUpper = strtoupper($search);
                $batchSize = 20;
                $allResults = [];

                for ($i = 0; $i < count($tables); $i += $batchSize) {
                    $batch = array_slice($tables, $i, $batchSize);
                    $unions = [];
                    $bindings = [];

                    foreach ($batch as $table) {
                        $unions[] = "(SELECT `EPIC_NO`, `FM_NAME_EN`, `LASTNAME_EN`, `AC_NO`, `ASSEMBLY_NAME`, `DISTRICT_NAME`, `AGE`, `GENDER`, `MOBILE_NO`, `PART_NO`, `SECTION_NO` FROM `{$table}` WHERE `EPIC_NO` LIKE ? OR `FM_NAME_EN` LIKE ? OR `MOBILE_NO` LIKE ?)";
                        $bindings[] = "%{$searchUpper}%";
                        $bindings[] = "%{$searchUpper}%";
                        $bindings[] = "%{$search}%";
                    }

                    $sql = implode(' UNION ALL ', $unions);
                    $rows = DB::connection('voters')->select($sql, $bindings);
                    foreach ($rows as $row) {
                        $v = (array) $row;
                        if (!empty($v['AC_NO']) && !empty($v['ASSEMBLY_NAME']) && !is_numeric($v['AC_NO']) && is_numeric($v['ASSEMBLY_NAME'])) {
                            [$v['AC_NO'], $v['ASSEMBLY_NAME']] = [$v['ASSEMBLY_NAME'], $v['AC_NO']];
                        }
                        $allResults[] = $v;
                    }
                }

                $total = count($allResults);
                $offset = ($page - 1) * $limit;
                $voters = array_slice($allResults, $offset, $limit);

            } elseif (!empty($tables) && !$search) {
                // No search — count total and paginate from first matching table(s)
                // Count totals from assembly_constituency table for speed
                if ($assemblyFilter) {
                    $ac = AssemblyConstituency::where('assembly_name', 'LIKE', $assemblyFilter)->first();
                    $total = $ac ? $ac->total_voters : 0;
                    if (!empty($tables)) {
                        $offset = ($page - 1) * $limit;
                        $rows = DB::connection('voters')->select(
                            "SELECT `EPIC_NO`, `FM_NAME_EN`, `LASTNAME_EN`, `AC_NO`, `ASSEMBLY_NAME`, `DISTRICT_NAME`, `AGE`, `GENDER`, `MOBILE_NO`, `PART_NO`, `SECTION_NO` FROM `{$tables[0]}` ORDER BY `SLNOINPART` ASC LIMIT ? OFFSET ?",
                            [$limit, $offset]
                        );
                        $voters = array_map(function($r) {
                            $v = (array) $r;
                            if (!empty($v['AC_NO']) && !empty($v['ASSEMBLY_NAME']) && !is_numeric($v['AC_NO']) && is_numeric($v['ASSEMBLY_NAME'])) {
                                [$v['AC_NO'], $v['ASSEMBLY_NAME']] = [$v['ASSEMBLY_NAME'], $v['AC_NO']];
                            }
                            return $v;
                        }, $rows);
                    }
                } elseif ($districtFilter) {
                    // Count total: sum voters from matched tables (works for both zone_data and MySQL district names)
                    $total = 0;
                    foreach ($tables as $t) {
                        $total += DB::connection('voters')->selectOne("SELECT COUNT(*) as cnt FROM `{$t}`")->cnt;
                    }
                    // Paginate across district tables
                    $offset = ($page - 1) * $limit;
                    $remaining = $limit;
                    $skip = $offset;
                    foreach ($tables as $table) {
                        if ($remaining <= 0) break;
                        $count = DB::connection('voters')->selectOne("SELECT COUNT(*) as cnt FROM `{$table}`")->cnt;
                        if ($skip >= $count) { $skip -= $count; continue; }
                        $rows = DB::connection('voters')->select(
                            "SELECT `EPIC_NO`, `FM_NAME_EN`, `LASTNAME_EN`, `AC_NO`, `ASSEMBLY_NAME`, `DISTRICT_NAME`, `AGE`, `GENDER`, `MOBILE_NO`, `PART_NO`, `SECTION_NO` FROM `{$table}` ORDER BY `SLNOINPART` ASC LIMIT ? OFFSET ?",
                            [$remaining, $skip]
                        );
                        foreach ($rows as $r) {
                                $v = (array) $r;
                                if (!empty($v['AC_NO']) && !empty($v['ASSEMBLY_NAME']) && !is_numeric($v['AC_NO']) && is_numeric($v['ASSEMBLY_NAME'])) {
                                    [$v['AC_NO'], $v['ASSEMBLY_NAME']] = [$v['ASSEMBLY_NAME'], $v['AC_NO']];
                                }
                                $voters[] = $v;
                                $remaining--;
                            }
                        $skip = 0;
                    }
                } else {
                    $total = AssemblyConstituency::getTotalVotersCount();
                    // Without filter, show first assembly's data
                    if (!empty($tables)) {
                        $offset = ($page - 1) * $limit;
                        // Paginate across all tables
                        $remaining = $limit;
                        $skip = $offset;
                        foreach ($tables as $table) {
                            if ($remaining <= 0) break;
                            $count = DB::connection('voters')->selectOne("SELECT COUNT(*) as cnt FROM `{$table}`")->cnt;
                            if ($skip >= $count) { $skip -= $count; continue; }
                            $rows = DB::connection('voters')->select(
                                "SELECT `EPIC_NO`, `FM_NAME_EN`, `LASTNAME_EN`, `AC_NO`, `ASSEMBLY_NAME`, `DISTRICT_NAME`, `AGE`, `GENDER`, `MOBILE_NO`, `PART_NO`, `SECTION_NO` FROM `{$table}` ORDER BY `SLNOINPART` ASC LIMIT ? OFFSET ?",
                                [$remaining, $skip]
                            );
                            foreach ($rows as $r) {
                                $v = (array) $r;
                                if (!empty($v['AC_NO']) && !empty($v['ASSEMBLY_NAME']) && !is_numeric($v['AC_NO']) && is_numeric($v['ASSEMBLY_NAME'])) {
                                    [$v['AC_NO'], $v['ASSEMBLY_NAME']] = [$v['ASSEMBLY_NAME'], $v['AC_NO']];
                                }
                                $voters[] = $v;
                                $remaining--;
                            }
                            $skip = 0;
                        }
                    }
                }
            }

            $pages = (int) ceil($total / max($limit, 1));

            return view('admin.voters', [
                'voters' => $voters,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
                'search' => $search,
                'assembly' => $assemblyFilter,
                'district' => $districtFilter,
                'assemblies' => $assemblies,
                'districts' => $districts,
            ]);
        } catch (\Exception $e) {
            return view('admin.voters', [
                'voters' => [],
                'total' => 0,
                'page' => 1,
                'pages' => 0,
                'search' => $search,
                'assembly' => $assemblyFilter,
                'district' => $districtFilter,
                'assemblies' => [],
                'districts' => [],
                'error' => 'Database connection failed. Please try again later.',
            ]);
        }
    }

    public function reports(Request $request)
    {
        $filter = $request->input('filter', 'today');
        $fromDate = $request->input('from', '');
        $toDate = $request->input('to', '');

        // Calculate date range based on filter
        switch ($filter) {
            case 'weekly':
                $from = now()->startOfWeek()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->startOfMonth()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
            case 'custom':
                $from = $fromDate ?: now()->format('Y-m-d');
                $to = $toDate ?: now()->format('Y-m-d');
                break;
            default: // today
                $filter = 'today';
                $from = now()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
        }

        $assembly = $request->input('assembly', '');
        $district = $request->input('district', '');
        $zone = $request->input('zone', '');

        $reportData = $this->mongo->getReportMembers($from, $to, $assembly ?: null, $district ?: null, $zone ?: null);

        // Get distinct assembly and district values for filter dropdowns
        $assemblies = $this->mongo->getDistinctValues('assembly');
        $districts = $this->mongo->getDistinctValues('district');
        $zones = $this->mongo->getDistinctValues('zone');

        return view('admin.reports', [
            'members' => $reportData['members'],
            'total' => $reportData['total'],
            'referral_count' => $reportData['referral_count'],
            'total_referral_count' => $reportData['total_referral_count'] ?? 0,
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
            'assembly' => $assembly,
            'district' => $district,
            'zone' => $zone,
            'assemblies' => $assemblies,
            'districts' => $districts,
            'zones' => $zones,
        ]);
    }

    public function notRegistered(Request $request)
    {
        $page = max(1, (int) $request->input('page', 1));
        $limit = 20;
        $search = $request->input('search', '');
        $step = $request->input('step', '');
        $filter = $request->input('filter', 'all');

        $from = '';
        $to = '';

        switch ($filter) {
            case 'today':
                $from = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                $to = $from;
                break;
            case 'weekly':
                $from = now()->setTimezone('Asia/Kolkata')->subDays(6)->format('Y-m-d');
                $to = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->setTimezone('Asia/Kolkata')->subDays(29)->format('Y-m-d');
                $to = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                break;
            case 'custom':
                $from = $request->input('from', '');
                $to = $request->input('to', '');
                break;
            default:
                // 'all' — no date filter
                break;
        }

        $data = $this->tracking->getIncompleteRegistrations($page, $limit, $search ?: null, $step ?: null, $from ?: null, $to ?: null);
        $stats = $this->tracking->getStepStats();

        $pages = (int) ceil(($data['total'] ?? 0) / max($limit, 1));

        return view('admin.not-registered', [
            'users' => $data['users'],
            'total' => $data['total'],
            'page' => $page,
            'pages' => $pages,
            'search' => $search,
            'step' => $step,
            'stats' => $stats,
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function loanRequests(Request $request)
    {
        $filter = $request->input('filter', 'today');
        $fromDate = $request->input('from', '');
        $toDate = $request->input('to', '');

        switch ($filter) {
            case 'weekly':
                $from = now()->startOfWeek()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->startOfMonth()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
            case 'custom':
                $from = $fromDate ?: now()->format('Y-m-d');
                $to = $toDate ?: now()->format('Y-m-d');
                break;
            case 'all':
                $from = '';
                $to = '';
                break;
            default: // today
                $filter = 'today';
                $from = now()->format('Y-m-d');
                $to = now()->format('Y-m-d');
                break;
        }

        $data = $this->mongo->getAllLoanRequests($from ?: null, $to ?: null);

        return view('admin.loan-requests', [
            'requests' => $data['requests'],
            'total' => $data['total'],
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function voterDetail(string $epicNo)
    {
        try {
            $epicNo = strtoupper(trim($epicNo));
            $tables = AssemblyConstituency::getAllVoterTables();
            $voter = null;

            // Search across tables in batches
            $batchSize = 30;
            for ($i = 0; $i < count($tables); $i += $batchSize) {
                $batch = array_slice($tables, $i, $batchSize);
                $unions = [];
                $bindings = [];

                foreach ($batch as $table) {
                    $unions[] = "(SELECT * FROM `{$table}` WHERE `EPIC_NO` = ? LIMIT 1)";
                    $bindings[] = $epicNo;
                }

                $sql = implode(' UNION ALL ', $unions) . ' LIMIT 1';
                $row = DB::connection('voters')->selectOne($sql, $bindings);

                if ($row) {
                    $voter = (array) $row;
                    break;
                }
            }

            if (!$voter) {
                abort(404, 'Voter not found.');
            }

            // Fix swapped AC_NO / ASSEMBLY_NAME in some MySQL tables:
            // If AC_NO contains text and ASSEMBLY_NAME contains a number, swap them.
            $acNo = $voter['AC_NO'] ?? '';
            $assemblyName = $voter['ASSEMBLY_NAME'] ?? '';
            if (!empty($acNo) && !empty($assemblyName) && !is_numeric($acNo) && is_numeric($assemblyName)) {
                $voter['AC_NO'] = $assemblyName;
                $voter['ASSEMBLY_NAME'] = $acNo;
            }

            return view('admin.voter-detail', ['voter' => (object) $voter]);
        } catch (\Exception $e) {
            abort(500, 'Database error: ' . $e->getMessage());
        }
    }
}
