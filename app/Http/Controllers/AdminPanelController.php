<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MongoService;
use App\Models\AssemblyConstituency;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminPanelController extends Controller
{
    protected MongoService $mongo;

    public function __construct(MongoService $mongo)
    {
        $this->mongo = $mongo;
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
                $tables = AssemblyConstituency::getTablesByDistrict($districtFilter);
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
                        $allResults[] = (array) $row;
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
                        $voters = array_map(fn($r) => (array) $r, $rows);
                    }
                } elseif ($districtFilter) {
                    $total = AssemblyConstituency::where('district_name', 'LIKE', $districtFilter)->sum('total_voters');
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
                        foreach ($rows as $r) { $voters[] = (array) $r; $remaining--; }
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
                            foreach ($rows as $r) { $voters[] = (array) $r; $remaining--; }
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
                'error' => 'Database connection failed: ' . $e->getMessage(),
            ]);
        }
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

            return view('admin.voter-detail', ['voter' => (object) $voter]);
        } catch (\Exception $e) {
            abort(500, 'Database error: ' . $e->getMessage());
        }
    }
}
