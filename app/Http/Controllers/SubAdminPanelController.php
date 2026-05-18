<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MongoService;
use App\Services\TrackingMongoService;
use App\Services\WhatsAppMongoService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Sub-Admin Panel Controller
 *
 * Read-only / restricted version of the Admin panel.
 * Exposes only the pages that sub-admins are allowed to view:
 *   - Dashboard
 *   - Members
 *   - Reports
 *   - Loan Requests
 *   - Not Registered
 *   - WhatsApp
 *
 * Sub-admins do NOT have permission to create / edit / delete records,
 * regenerate cards, manage flow images, or access voter rolls.
 */
class SubAdminPanelController extends Controller
{
    protected MongoService $mongo;
    protected TrackingMongoService $tracking;

    public function __construct(MongoService $mongo, TrackingMongoService $tracking)
    {
        $this->mongo = $mongo;
        $this->tracking = $tracking;
    }

    // ──────────────────────────────────────────────────────────────────
    // Auth
    // ──────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (session('sub_admin_logged_in')) {
            return redirect()->route('sub_admin.dashboard');
        }
        return view('sub_admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $expectedUsername = config('services.sub_admin.username');
        $expectedHash     = config('services.sub_admin.password_hash');
        $expectedPlain    = config('services.sub_admin.password');

        $usernameOk = !empty($expectedUsername)
            && hash_equals((string) $expectedUsername, (string) $request->username);

        $passwordOk = false;
        if ($usernameOk) {
            if (!empty($expectedHash) && password_verify($request->password, $expectedHash)) {
                $passwordOk = true;
            } elseif (!empty($expectedPlain) && hash_equals((string) $expectedPlain, (string) $request->password)) {
                $passwordOk = true;
            }
        }

        if ($usernameOk && $passwordOk) {
            $request->session()->regenerate();
            session([
                'sub_admin_logged_in' => true,
                'sub_admin_username'  => $request->username,
            ]);
            return redirect()->route('sub_admin.dashboard');
        }

        return back()->withErrors(['error' => 'Invalid username or password.']);
    }

    public function logout()
    {
        session()->forget(['sub_admin_logged_in', 'sub_admin_username']);
        return redirect()->route('sub_admin.login');
    }

    // ──────────────────────────────────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = $this->mongo->getStats();
        return view('sub_admin.dashboard', compact('stats'));
    }

    // ──────────────────────────────────────────────────────────────────
    // Members (read-only listing)
    // ──────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $page     = max(1, (int) $request->input('page', 1));
        $search   = $request->input('search', '');
        $assembly = $request->input('assembly', '');
        $district = $request->input('district', '');

        $result = $this->mongo->getAllMembers(
            $page,
            20,
            $search ?: null,
            $assembly ?: null,
            $district ?: null
        );

        $assemblies = $this->mongo->getDistinctValues('assembly');
        $districts  = $this->mongo->getDistinctValues('district');

        return view('sub_admin.users', [
            'members'    => $result['members'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'search'     => $search,
            'assembly'   => $assembly,
            'district'   => $district,
            'assemblies' => $assemblies,
            'districts'  => $districts,
        ]);
    }

    /**
     * Read-only member detail page for sub-admins.
     *
     * Mirrors AdminPanelController::userDetail but renders the
     * sub_admin.user-detail view (which has no Edit / Delete / Regenerate
     * controls) so sub-admins can view member info without the ability to
     * mutate it.
     */
    public function userDetail(string $uniqueId)
    {
        $member = $this->mongo->findMemberByUniqueId($uniqueId);

        if (!$member) {
            abort(404, 'Member not found.');
        }

        unset($member['pin_hash']);

        $referredMembers = $this->mongo->getMembersReferredBy($uniqueId);

        $referredByMember = null;
        if (!empty($member['referred_by'])) {
            $referredByMember = $this->mongo->findMemberByUniqueId($member['referred_by']);
            if ($referredByMember) {
                unset($referredByMember['pin_hash']);
            }
        }

        $loanRequest = null;
        try {
            $loanRequest = $this->mongo->getLoanRequestByUniqueId($uniqueId);
        } catch (Exception $e) {
            Log::error("SubAdminPanelController::userDetail loan fetch error: " . $e->getMessage());
        }

        return view('sub_admin.user-detail', [
            'member'             => (object) $member,
            'referred_members'   => $referredMembers,
            'referred_by_member' => $referredByMember ? (object) $referredByMember : null,
            'loan_request'       => $loanRequest ? (object) $loanRequest : null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Reports
    // ──────────────────────────────────────────────────────────────────

    public function reports(Request $request)
    {
        $filter   = $request->input('filter', 'today');
        $fromDate = $request->input('from', '');
        $toDate   = $request->input('to', '');

        switch ($filter) {
            case 'weekly':
                $from = now()->startOfWeek()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->startOfMonth()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                break;
            case 'custom':
                $from = $fromDate ?: now()->format('Y-m-d');
                $to   = $toDate   ?: now()->format('Y-m-d');
                break;
            default:
                $filter = 'today';
                $from   = now()->format('Y-m-d');
                $to     = now()->format('Y-m-d');
                break;
        }

        $assembly = $request->input('assembly', '');
        $district = $request->input('district', '');
        $zone     = $request->input('zone', '');

        $reportData = $this->mongo->getReportMembers(
            $from,
            $to,
            $assembly ?: null,
            $district ?: null,
            $zone     ?: null
        );

        $assemblies = $this->mongo->getDistinctValues('assembly');
        $districts  = $this->mongo->getDistinctValues('district');
        $zones      = $this->mongo->getDistinctValues('zone');

        return view('sub_admin.reports', [
            'members'              => $reportData['members'],
            'total'                => $reportData['total'],
            'referral_count'       => $reportData['referral_count'],
            'total_referral_count' => $reportData['total_referral_count'] ?? 0,
            'filter'               => $filter,
            'from'                 => $from,
            'to'                   => $to,
            'assembly'             => $assembly,
            'district'             => $district,
            'zone'                 => $zone,
            'assemblies'           => $assemblies,
            'districts'            => $districts,
            'zones'                => $zones,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Loan Requests
    // ──────────────────────────────────────────────────────────────────

    public function loanRequests(Request $request)
    {
        $filter   = $request->input('filter', 'today');
        $fromDate = $request->input('from', '');
        $toDate   = $request->input('to', '');

        switch ($filter) {
            case 'weekly':
                $from = now()->startOfWeek()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->startOfMonth()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                break;
            case 'custom':
                $from = $fromDate ?: now()->format('Y-m-d');
                $to   = $toDate   ?: now()->format('Y-m-d');
                break;
            case 'all':
                $from = '';
                $to   = '';
                break;
            default:
                $filter = 'today';
                $from   = now()->format('Y-m-d');
                $to     = now()->format('Y-m-d');
                break;
        }

        $data = $this->mongo->getAllLoanRequests($from ?: null, $to ?: null);

        return view('sub_admin.loan-requests', [
            'requests' => $data['requests'],
            'total'    => $data['total'],
            'filter'   => $filter,
            'from'     => $from,
            'to'       => $to,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Not Registered (incomplete registrations from tracking DB)
    // ──────────────────────────────────────────────────────────────────

    public function notRegistered(Request $request)
    {
        $export = $request->input('export') === '1';
        $page   = max(1, (int) $request->input('page', 1));
        $limit  = 20;
        $search = $request->input('search', '');
        $step   = $request->input('step', '');
        $filter = $request->input('filter', 'all');

        $from = '';
        $to   = '';

        switch ($filter) {
            case 'today':
                $from = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                $to   = $from;
                break;
            case 'weekly':
                $from = now()->setTimezone('Asia/Kolkata')->subDays(6)->format('Y-m-d');
                $to   = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                break;
            case 'monthly':
                $from = now()->setTimezone('Asia/Kolkata')->subDays(29)->format('Y-m-d');
                $to   = now()->setTimezone('Asia/Kolkata')->format('Y-m-d');
                break;
            case 'custom':
                $from = $request->input('from', '');
                $to   = $request->input('to', '');
                break;
            default:
                // 'all' - no date filter
                break;
        }

        // Export mode: return ALL filtered records as JSON for PDF/Excel download.
        if ($export) {
            $all = $this->tracking->getIncompleteRegistrations(
                1,
                1000000,
                $search ?: null,
                $step   ?: null,
                $from   ?: null,
                $to     ?: null
            );

            $zoneConfig = config('zone_data');
            $asmMap     = $zoneConfig['assembly_map']  ?? [];
            $distZone   = $zoneConfig['district_zone'] ?? [];

            $resolveDz = function (?string $assemblyName) use ($asmMap, $distZone): array {
                $asmUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $assemblyName ?? '')));
                $matched  = $asmMap[$asmUpper] ?? null;
                if (!$matched) {
                    $norm = preg_replace('/[\. \-\(\)]/', '', $asmUpper);
                    foreach ($asmMap as $k => $v) {
                        if (preg_replace('/[\. \-\(\)]/', '', $k) === $norm) { $matched = $v; break; }
                    }
                }
                // Sub-admin map uses keys 'd' / 'z' (see sub_admin.not-registered.blade.php)
                $district = $matched['district'] ?? ($matched['d'] ?? null);
                $zone     = $district ? ($distZone[$district] ?? null) : ($matched['z'] ?? null);
                return ['d' => $district, 'z' => $zone];
            };

            $rows = [];
            foreach (($all['users'] ?? []) as $i => $u) {
                $dz = $resolveDz($u['assembly'] ?? '');
                $startedAt = !empty($u['started_at'])
                    ? \Carbon\Carbon::parse($u['started_at'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')
                    : '—';
                $lastActivity = !empty($u['last_activity'])
                    ? \Carbon\Carbon::parse($u['last_activity'])->setTimezone('Asia/Kolkata')->format('d M Y, h:i A')
                    : '—';
                $stepLabel   = $u['last_step'] ?? 'unknown';
                $stepDisplay = ucwords(str_replace('_', ' ', $stepLabel));
                $refName     = $u['referrer_name'] ?? '';
                $refId       = $u['referrer_unique_id'] ?? '';
                $referredBy  = $refId ? trim(($refName ?: '—') . ' (' . $refId . ')') : '—';

                $rows[] = [
                    $i + 1,
                    $u['mobile'] ?? '',
                    $u['name'] ?? '—',
                    $u['epic_no'] ?? '—',
                    $u['assembly'] ?? '—',
                    $dz['d'] ?? ucwords(strtolower($u['district'] ?? '—')),
                    $dz['z'] ?? '—',
                    $stepDisplay,
                    $referredBy,
                    $startedAt,
                    $lastActivity,
                ];
            }

            return response()->json([
                'rows'   => $rows,
                'total'  => $all['total'] ?? count($rows),
                'filter' => $filter,
                'from'   => $from,
                'to'     => $to,
                'step'   => $step,
            ]);
        }

        $data  = $this->tracking->getIncompleteRegistrations(
            $page,
            $limit,
            $search ?: null,
            $step   ?: null,
            $from   ?: null,
            $to     ?: null
        );
        $stats = $this->tracking->getStepStats();

        $pages = (int) ceil(($data['total'] ?? 0) / max($limit, 1));

        return view('sub_admin.not-registered', [
            'users'  => $data['users'],
            'total'  => $data['total'],
            'page'   => $page,
            'pages'  => $pages,
            'search' => $search,
            'step'   => $step,
            'stats'  => $stats,
            'filter' => $filter,
            'from'   => $from,
            'to'     => $to,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // WhatsApp users
    // ──────────────────────────────────────────────────────────────────

    public function whatsapp(Request $request)
    {
        $whatsAppMongo = app(WhatsAppMongoService::class);

        $page   = max(1, (int) $request->input('page', 1));
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $result = $whatsAppMongo->getWhatsAppUsers($page, 20, $search ?: null, $status ?: null);
        $stats  = $whatsAppMongo->getStats();

        $total = $result['total'];
        $pages = (int) ceil($total / 20);

        return view('sub_admin.whatsapp', [
            'users'  => $result['users'],
            'total'  => $total,
            'page'   => $page,
            'pages'  => $pages,
            'search' => $search,
            'status' => $status,
            'stats'  => $stats,
        ]);
    }
}
