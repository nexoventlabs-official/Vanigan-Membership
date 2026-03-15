<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\GeneratedVoter;
use App\Models\GenerationStat;
use App\Models\VolunteerRequest;
use App\Models\BoothAgentRequest;

class AdminController extends Controller
{
    /**
     * GET /admin/dashboard
     * Admin dashboard with statistics
     */
    public function dashboard()
    {
        try {
            if (!session('admin_logged_in')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $stats = \App\Helpers\StatisticsHelper::getDashboardStats();
            $assemblyStats = \App\Helpers\StatisticsHelper::getStatsByAssembly(10);
            $districtStats = \App\Helpers\StatisticsHelper::getStatsByDistrict(10);
            $timeline = \App\Helpers\StatisticsHelper::getGenerationTimeline(30);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'assembly_stats' => $assemblyStats,
                'district_stats' => $districtStats,
                'timeline' => $timeline,
                'success_rate' => \App\Helpers\StatisticsHelper::getSuccessRate(),
                'avg_cards_per_user' => \App\Helpers\StatisticsHelper::getAverageCardsPerUser(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard'
            ], 500);
        }
    }


    /**
     * GET /admin/voters
     * List generated voters with search/filter
     */
    public function voters(Request $request)
    {
        try {
            if (!session('admin_logged_in')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $page = $request->input('page', 1);
            $limit = $request->input('limit', 20);
            $search = $request->input('search', '');
            $assembly = $request->input('assembly', '');
            $district = $request->input('district', '');

            $query = \App\Models\GeneratedVoter::query();

            // Search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('epic_no', 'like', "%{$search}%")
                      ->orWhere('voter_name', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            }

            // Assembly filter
            if ($assembly) {
                $query->where('assembly_name', $assembly);
            }

            // District filter
            if ($district) {
                $query->where('district', $district);
            }

            $total = $query->count();
            $voters = $query->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => $voters->lastPage(),
                'voters' => $voters->items(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Voters List Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load voters'
            ], 500);
        }
    }


    /**
     * POST /admin/login
     * Admin login endpoint
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $username = $request->input('username');
            $password = $request->input('password');

            $adminUsername = config('services.admin.username');
            $adminPassword = config('services.admin.password_hash');

            if ($username === $adminUsername && password_verify($password, $adminPassword)) {
                session(['admin_logged_in' => true, 'admin_username' => $username]);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);

        } catch (\Exception $e) {
            \Log::error('Admin Login Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed'
            ], 500);
        }
    }

    /**
     * GET /admin/voter/{epicNo}
     * Get detailed voter information
     */
    public function voterDetail($epicNo)
    {
        try {
            if (!session('admin_logged_in')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $voterService = new \App\Services\VoterLookupService();
            $result = $voterService->findVoterWithCardStatus($epicNo);

            return response()->json([
                'success' => $result['found'],
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            \Log::error('Voter Detail Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load voter details'
            ], 500);
        }
    }

    /**
     * GET /admin/dropdowns
     * Get assembly and district dropdown data
     */
    public function dropdowns()
    {
        try {
            if (!session('admin_logged_in')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get assemblies and districts from generated_voters table (much faster)
            $assemblies = \App\Models\GeneratedVoter::distinct()
                ->whereNotNull('ASSEMBLY_NAME')
                ->pluck('ASSEMBLY_NAME')
                ->sort()
                ->values()
                ->toArray();
                
            $districts = \App\Models\GeneratedVoter::distinct()
                ->whereNotNull('DISTRICT_NAME')
                ->pluck('DISTRICT_NAME')
                ->sort()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'assemblies' => $assemblies,
                'districts' => $districts,
            ]);

        } catch (\Exception $e) {
            \Log::error('Dropdowns Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dropdowns'
            ], 500);
        }
    }

    /**
     * Show admin login page
     */
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Handle admin login (web form)
     */
    public function loginWeb(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $username = $request->input('username');
            $password = $request->input('password');

            $adminUsername = config('services.admin.username', env('ADMIN_USERNAME'));
            $adminPassword = config('services.admin.password_hash', env('ADMIN_PASSWORD_HASH'));

            if ($username === $adminUsername && password_verify($password, $adminPassword)) {
                session(['admin_logged_in' => true, 'admin_username' => $username]);
                return redirect()->route('admin.dashboard')->with('success', 'Login successful');
            }

            return back()->withErrors(['error' => 'Invalid credentials']);

        } catch (\Exception $e) {
            \Log::error('Admin Login Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Login failed']);
        }
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_username']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    /**
     * Show admin dashboard (web)
     */
    public function dashboardWeb()
    {
        $stats = \App\Helpers\StatisticsHelper::getDashboardStats();
        $assemblyStats = \App\Helpers\StatisticsHelper::getStatsByAssembly(10);
        $districtStats = \App\Helpers\StatisticsHelper::getStatsByDistrict(10);
        $recentCards = \App\Models\GeneratedVoter::latest()->take(10)->get();
        
        // Add referral-specific data (matches Python Flask admin)
        $topReferrers = \App\Models\GeneratedVoter::where('referred_members_count', '>', 0)
            ->orderBy('referred_members_count', 'desc')
            ->limit(5)
            ->get([
                'EPIC_NO as epic_no',
                'FM_NAME_EN as first_name', 
                'LASTNAME_EN as last_name',
                'ptc_code',
                'referred_members_count'
            ])
            ->map(function ($referrer) {
                return [
                    'epic_no' => $referrer->epic_no,
                    'name' => trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')),
                    'ptc_code' => $referrer->ptc_code,
                    'referral_count' => $referrer->referred_members_count,
                ];
            });

        $recentReferrals = \Illuminate\Support\Facades\DB::table('generated_voters as gv')
            ->leftJoin('generated_voters as referrer', 'gv.referred_by_ptc', '=', 'referrer.ptc_code')
            ->whereNotNull('gv.referred_by_ptc')
            ->orderBy('gv.generated_at', 'desc')
            ->limit(5)
            ->get([
                'gv.EPIC_NO as epic_no',
                'gv.FM_NAME_EN as first_name',
                'gv.LASTNAME_EN as last_name',
                'gv.generated_at',
                'referrer.EPIC_NO as referrer_epic',
                'referrer.FM_NAME_EN as referrer_first_name',
                'referrer.LASTNAME_EN as referrer_last_name',
            ])
            ->map(function ($item) {
                return [
                    'epic_no' => $item->epic_no,
                    'name' => trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')),
                    'generated_at' => $item->generated_at,
                    'referrer' => [
                        'epic_no' => $item->referrer_epic,
                        'name' => trim(($item->referrer_first_name ?? '') . ' ' . ($item->referrer_last_name ?? '')),
                    ]
                ];
            });

        return view('admin.dashboard', [
            'stats' => $stats,
            'assembly_stats' => $assemblyStats,
            'district_stats' => $districtStats,
            'recent_cards' => $recentCards,
            'top_referrers' => $topReferrers,
            'recent_referrals' => $recentReferrals,
        ]);
    }

    /**
     * Show voters list (web)
     */
    public function votersWeb(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        $search = $request->input('search', '');
        $assembly = $request->input('assembly', '');
        $district = $request->input('district', '');

        $query = \App\Models\GeneratedVoter::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('EPIC_NO', 'like', "%{$search}%")
                  ->orWhere('FM_NAME_EN', 'like', "%{$search}%")
                  ->orWhere('LASTNAME_EN', 'like', "%{$search}%")
                  ->orWhere('MOBILE_NO', 'like', "%{$search}%");
            });
        }

        if ($assembly) {
            $query->where('ASSEMBLY_NAME', $assembly);
        }

        if ($district) {
            $query->where('DISTRICT_NAME', $district);
        }

        $voters = $query->orderBy('generated_at', 'desc')->paginate($limit);
        
        // Get assemblies and districts from generated_voters table (much faster)
        $assemblies = \App\Models\GeneratedVoter::distinct()
            ->whereNotNull('ASSEMBLY_NAME')
            ->pluck('ASSEMBLY_NAME')
            ->sort()
            ->values()
            ->toArray();
            
        $districts = \App\Models\GeneratedVoter::distinct()
            ->whereNotNull('DISTRICT_NAME')
            ->pluck('DISTRICT_NAME')
            ->sort()
            ->values()
            ->toArray();

        return view('admin.voters', [
            'voters' => $voters,
            'assemblies' => $assemblies,
            'districts' => $districts,
        ]);
    }

    // Volunteer Management
    public function volunteerRequests(Request $request)
    {
        $query = VolunteerRequest::query()->with('generatedVoter');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('epic_no', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%")
                  ->orWhere('assembly', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Assembly filter
        if ($request->filled('assembly') && $request->assembly !== 'all') {
            $query->where('assembly', $request->assembly);
        }

        $volunteers = $query->orderBy('requested_at', 'desc')->paginate(20);

        // Get unique assemblies for filter dropdown
        $assemblies = VolunteerRequest::distinct()->pluck('assembly')->filter()->sort();

        return response()->json([
            'volunteers' => $volunteers->items(),
            'pagination' => [
                'current_page' => $volunteers->currentPage(),
                'last_page' => $volunteers->lastPage(),
                'per_page' => $volunteers->perPage(),
                'total' => $volunteers->total(),
            ],
            'assemblies' => $assemblies->values(),
        ]);
    }

    public function volunteerRequestsWeb(Request $request)
    {
        return view('admin.volunteer-requests');
    }

    public function confirmVolunteerRequest($ptcCode)
    {
        try {
            $volunteer = VolunteerRequest::where('ptc_code', $ptcCode)->firstOrFail();
            
            $volunteer->update([
                'status' => 'confirmed',
                'reviewed_at' => now(),
                'reviewed_by' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Volunteer request confirmed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error confirming volunteer request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectVolunteerRequest($ptcCode)
    {
        try {
            $volunteer = VolunteerRequest::where('ptc_code', $ptcCode)->firstOrFail();
            
            $volunteer->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Volunteer request rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting volunteer request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkVolunteerAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,reject',
            'ptc_codes' => 'required|array',
            'ptc_codes.*' => 'required|string',
        ]);

        try {
            $status = $request->action === 'confirm' ? 'confirmed' : 'rejected';
            
            VolunteerRequest::whereIn('ptc_code', $request->ptc_codes)
                ->update([
                    'status' => $status,
                    'reviewed_at' => now(),
                    'reviewed_by' => 'admin',
                ]);

            $count = count($request->ptc_codes);
            $action = $request->action === 'confirm' ? 'confirmed' : 'rejected';

            return response()->json([
                'success' => true,
                'message' => "{$count} volunteer requests {$action} successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk action: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Booth Agent Management
    public function boothAgentRequests(Request $request)
    {
        $query = BoothAgentRequest::query()->with('generatedVoter');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('epic_no', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%")
                  ->orWhere('assembly', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Assembly filter
        if ($request->filled('assembly') && $request->assembly !== 'all') {
            $query->where('assembly', $request->assembly);
        }

        // Source filter
        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('source', $request->source);
        }

        $boothAgents = $query->orderBy('requested_at', 'desc')->paginate(20);

        // Get unique assemblies for filter dropdown
        $assemblies = BoothAgentRequest::distinct()->pluck('assembly')->filter()->sort();

        return response()->json([
            'booth_agents' => $boothAgents->items(),
            'pagination' => [
                'current_page' => $boothAgents->currentPage(),
                'last_page' => $boothAgents->lastPage(),
                'per_page' => $boothAgents->perPage(),
                'total' => $boothAgents->total(),
            ],
            'assemblies' => $assemblies->values(),
        ]);
    }

    public function boothAgentRequestsWeb(Request $request)
    {
        return view('admin.booth-agent-requests');
    }

    public function confirmBoothAgentRequest($ptcCode)
    {
        try {
            $boothAgent = BoothAgentRequest::where('ptc_code', $ptcCode)->firstOrFail();
            
            $boothAgent->update([
                'status' => 'confirmed',
                'reviewed_at' => now(),
                'reviewed_by' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booth agent request confirmed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error confirming booth agent request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectBoothAgentRequest($ptcCode)
    {
        try {
            $boothAgent = BoothAgentRequest::where('ptc_code', $ptcCode)->firstOrFail();
            
            $boothAgent->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => 'admin',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booth agent request rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting booth agent request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkBoothAgentAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,reject',
            'ptc_codes' => 'required|array',
            'ptc_codes.*' => 'required|string',
        ]);

        try {
            $status = $request->action === 'confirm' ? 'confirmed' : 'rejected';
            
            BoothAgentRequest::whereIn('ptc_code', $request->ptc_codes)
                ->update([
                    'status' => $status,
                    'reviewed_at' => now(),
                    'reviewed_by' => 'admin',
                ]);

            $count = count($request->ptc_codes);
            $action = $request->action === 'confirm' ? 'confirmed' : 'rejected';

            return response()->json([
                'success' => true,
                'message' => "{$count} booth agent requests {$action} successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk action: ' . $e->getMessage(),
            ], 500);
        }
    }

}
