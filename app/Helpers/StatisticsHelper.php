<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GeneratedVoter;
use App\Models\GenerationStat;
use App\Models\OtpSession;
use App\Models\VolunteerRequest;
use App\Models\BoothAgentRequest;
use Exception;

class StatisticsHelper
{
    /**
     * Get comprehensive dashboard statistics
     * Cached for 60 seconds
     */
    /**
     * Get comprehensive dashboard statistics
     * Cached for 60 seconds
     */
    public static function getDashboardStats()
    {
        try {
            return app(\App\Services\CacheService::class)->remember('stats:dashboard', 60, function () {
                $stats = [
                    'total_generated' => 0,
                    'total_generations' => 0,
                    'today_cards' => 0,
                    'this_week_cards' => 0,
                    'this_month_cards' => 0,
                    'unique_voters' => 0,
                    'unique_mobile' => 0,

                    // Referral statistics
                    'total_referrals' => 0,
                    'total_referrers' => 0,
                    'referrals_today' => 0,
                    'top_referrer_count' => 0,

                    'volunteer_requests' => [
                        'pending' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                    ],
                    'booth_agent_requests' => [
                        'pending' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                    ],
                    'otp_sent_today' => 0,
                    'otp_verified_today' => 0,
                ];

                try {
                    $stats['total_generated'] = GeneratedVoter::count();
                    $stats['unique_mobile'] = GeneratedVoter::distinct('MOBILE_NO')->count();
                    $stats['today_cards'] = GeneratedVoter::where('created_at', '>=', now()->startOfDay())->count();
                    $stats['this_week_cards'] = GeneratedVoter::where('created_at', '>=', now()->startOfWeek())->count();
                    $stats['this_month_cards'] = GeneratedVoter::where('created_at', '>=', now()->startOfMonth())->count();

                    // Referral statistics
                    $stats['total_referrals'] = GeneratedVoter::whereNotNull('referred_by_ptc')->count();
                    $stats['total_referrers'] = GeneratedVoter::where('referred_members_count', '>', 0)->count();
                    $stats['referrals_today'] = GeneratedVoter::whereNotNull('referred_by_ptc')
                        ->where('created_at', '>=', now()->startOfDay())->count();
                    $stats['top_referrer_count'] = GeneratedVoter::max('referred_members_count') ?? 0;
                } catch (Exception $e) {
                    Log::warning("Error getting basic stats: " . $e->getMessage());
                }

                try {
                    $stats['total_generations'] = GenerationStat::sum('generation_count') ?? 0;
                } catch (Exception $e) {
                    Log::warning("Error getting generation stats: " . $e->getMessage());
                }

                try {
                    $stats['volunteer_requests'] = [
                        'pending' => VolunteerRequest::where('status', 'pending')->count(),
                        'approved' => VolunteerRequest::where('status', 'approved')->count(),
                        'rejected' => VolunteerRequest::where('status', 'rejected')->count(),
                    ];
                } catch (Exception $e) {
                    Log::warning("Error getting volunteer stats: " . $e->getMessage());
                }

                try {
                    $stats['booth_agent_requests'] = [
                        'pending' => BoothAgentRequest::where('status', 'pending')->count(),
                        'approved' => BoothAgentRequest::where('status', 'approved')->count(),
                        'rejected' => BoothAgentRequest::where('status', 'rejected')->count(),
                    ];
                } catch (Exception $e) {
                    Log::warning("Error getting booth agent stats: " . $e->getMessage());
                }

                try {
                    $stats['otp_sent_today'] = OtpSession::where('created_at', '>=', now()->startOfDay())->count();
                    $stats['otp_verified_today'] = OtpSession::where('verified', 1)
                        ->where('verified_at', '>=', now()->startOfDay())
                        ->count();
                } catch (Exception $e) {
                    Log::warning("Error getting OTP stats: " . $e->getMessage());
                }

                return $stats;
            });

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getDashboardStats Error: " . $e->getMessage());
            return [
                'total_generated' => 0,
                'total_generations' => 0,
                'today_cards' => 0,
                'this_week_cards' => 0,
                'this_month_cards' => 0,
                'unique_voters' => 0,
                'unique_mobile' => 0,
                'total_referrals' => 0,
                'total_referrers' => 0,
                'referrals_today' => 0,
                'top_referrer_count' => 0,
                'volunteer_requests' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
                'booth_agent_requests' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
                'otp_sent_today' => 0,
                'otp_verified_today' => 0,
            ];
        }
    }


    /**
     * Get generation statistics by assembly
     */
    public static function getStatsByAssembly($limit = 10)
    {
        try {
            return GeneratedVoter::select('assembly_name', DB::raw('count(*) as total'))
                ->groupBy('assembly_name')
                ->orderBy('total', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getStatsByAssembly Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get generation statistics by district
     */
    public static function getStatsByDistrict($limit = 10)
    {
        try {
            return GeneratedVoter::select('DISTRICT_NAME as district', DB::raw('count(*) as total'))
                ->groupBy('DISTRICT_NAME')
                ->orderBy('total', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getStatsByDistrict Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get generation timeline (last 30 days)
     */
    public static function getGenerationTimeline($days = 30)
    {
        try {
            return GeneratedVoter::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->toArray();

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getGenerationTimeline Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top voter records (most generations)
     */
    public static function getTopVoters($limit = 10)
    {
        try {
            return GeneratedVoter::select('epic_no', 'voter_name', 'mobile', DB::raw('count(*) as generations'))
                ->groupBy('epic_no', 'voter_name', 'mobile')
                ->orderBy('generations', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getTopVoters Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get generation success rate
     */
    public static function getSuccessRate()
    {
        try {
            $total = GeneratedVoter::count();
            if ($total === 0) {
                return 100;
            }

            $successCount = GeneratedVoter::where('status', 'generated')->count();
            return round(($successCount / $total) * 100, 2);

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getSuccessRate Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get average cards per user
     */
    public static function getAverageCardsPerUser()
    {
        try {
            $totalCards = GeneratedVoter::count();
            $totalUsers = GeneratedVoter::distinct('mobile')->count();

            if ($totalUsers === 0) {
                return 0;
            }

            return round($totalCards / $totalUsers, 2);

        } catch (Exception $e) {
            Log::error("StatisticsHelper::getAverageCardsPerUser Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clear statistics cache
     */
    public static function clearCache()
    {
        try {
            app(\App\Services\CacheService::class)->forget('stats:dashboard');
            app(\App\Services\CacheService::class)->forget('stats:assembly');
            app(\App\Services\CacheService::class)->forget('stats:district');
            Log::info("Statistics cache cleared");
        } catch (Exception $e) {
            Log::error("StatisticsHelper::clearCache Error: " . $e->getMessage());
        }
    }
}
