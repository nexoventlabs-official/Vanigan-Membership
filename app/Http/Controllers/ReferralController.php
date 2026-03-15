<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GeneratedVoter;
use App\Helpers\VoterHelper;

class ReferralController extends Controller
{
    /**
     * Referral landing page - matches Python Flask referral_landing()
     * Route: /refer/{ptc_code}/{referral_id}
     */
    public function referralLanding($ptcCode, $referralId)
    {
        try {
            // Validate referral link
            $voter = GeneratedVoter::where('ptc_code', $ptcCode)
                ->where('referral_id', $referralId)
                ->first(['FM_NAME_EN', 'LASTNAME_EN']);

            if (!$voter) {
                return redirect()->route('user.home')->withErrors(['error' => 'Invalid referral link.']);
            }

            $referrerName = trim(($voter->FM_NAME_EN ?? '') . ' ' . ($voter->LASTNAME_EN ?? ''));
            if (empty($referrerName)) {
                $referrerName = 'A PuratchiThaai Member';
            }

            $redirectUrl = route('user.home') . "?ref={$ptcCode}&rid={$referralId}";
            $bannerUrl = asset('static/banner.jpg');
            $baseUrl = config('app.url');

            // Return OG-tagged HTML for WhatsApp preview (matches Python implementation)
            $html = "<!DOCTYPE html>
<html><head>
<meta charset=\"UTF-8\">
<meta property=\"og:title\" content=\"PuratchiThaai — Become a Member!\">
<meta property=\"og:description\" content=\"{$referrerName} invites you to join PuratchiThaai! Generate your free Digital Member ID Card now and become a proud member.\">
<meta property=\"og:image\" content=\"{$bannerUrl}\">
<meta property=\"og:image:width\" content=\"1200\">
<meta property=\"og:image:height\" content=\"630\">
<meta property=\"og:type\" content=\"website\">
<meta property=\"og:url\" content=\"{$baseUrl}/refer/{$ptcCode}/{$referralId}\">
<meta name=\"twitter:card\" content=\"summary_large_image\">
<meta name=\"twitter:title\" content=\"PuratchiThaai — Become a Member!\">
<meta name=\"twitter:description\" content=\"{$referrerName} invites you to join PuratchiThaai! Generate your free Digital Member ID Card now.\">
<meta name=\"twitter:image\" content=\"{$bannerUrl}\">
<meta http-equiv=\"refresh\" content=\"0;url={$redirectUrl}\">
<title>PuratchiThaai — Join Now!</title>
</head><body style=\"font-family:sans-serif;text-align:center;padding:40px;\">
<p>Redirecting to PuratchiThaai...</p>
<script>window.location.href=\"{$redirectUrl}\";</script>
</body></html>";

            return response($html)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Referral landing error: ' . $e->getMessage());
            return redirect()->route('user.home')->withErrors(['error' => 'Invalid referral link.']);
        }
    }

    /**
     * API: Get or create referral link for a mobile number
     * Matches Python: /api/chat/get-referral-link
     */
    public function getReferralLink(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10'
            ]);

            $mobile = $request->input('mobile');

            // Find voter by mobile
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->whereNotNull('ptc_code')
                ->first(['ptc_code']);

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found.'
                ], 404);
            }

            // Get or create referral
            $referral = VoterHelper::getOrCreateReferral($voter->ptc_code);

            if (!$referral) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not generate referral link.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'referral_id' => $referral['referral_id'],
                'referral_link' => $referral['referral_link'],
                'ptc_code' => $voter->ptc_code
            ]);

        } catch (\Exception $e) {
            Log::error('Get referral link error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    /**
     * API: Get referred members for a mobile number
     * Matches Python WhatsApp bot _menu_members functionality
     */
    public function getReferredMembers(Request $request)
    {
        try {
            $request->validate([
                'mobile' => 'required|digits:10'
            ]);

            $mobile = $request->input('mobile');

            // Find voter's PTC code
            $voter = GeneratedVoter::where('MOBILE_NO', $mobile)
                ->whereNotNull('ptc_code')
                ->first(['ptc_code']);

            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found.'
                ], 404);
            }

            // Get referred members
            $members = GeneratedVoter::where('referred_by_ptc', $voter->ptc_code)
                ->orderBy('generated_at', 'desc')
                ->limit(20)
                ->get([
                    'EPIC_NO as epic_no',
                    'FM_NAME_EN',
                    'LASTNAME_EN', 
                    'AC_NO as assembly',
                    'ASSEMBLY_NAME as assembly_name',
                    'generated_at'
                ])
                ->map(function ($member) {
                    return [
                        'epic_no' => $member->epic_no,
                        'name' => trim(($member->FM_NAME_EN ?? '') . ' ' . ($member->LASTNAME_EN ?? '')),
                        'assembly' => $member->assembly_name ?: $member->assembly,
                        'generated_at' => $member->generated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'members' => $members,
                'count' => $members->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Get referred members error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.'
            ], 500);
        }
    }
}