<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MongoService;
use App\Services\FlowImageService;
use App\Helpers\VoterHelper;
use phpseclib3\Crypt\RSA;
use Exception;

class FlowEndpointController extends Controller
{
    protected MongoService $mongo;
    protected FlowImageService $flowImages;

    public function __construct(MongoService $mongo, FlowImageService $flowImages)
    {
        $this->mongo = $mongo;
        $this->flowImages = $flowImages;
    }

    public function handle(Request $request)
    {
        $dbg = \base_path('public/flow_debug.txt');
        try {
            $allInput = $request->all();
            $rawContent = $request->getContent();

            $decrypted = $this->decryptRequest($allInput);
            if (!$decrypted) {
                $raw = json_decode($rawContent, true);
                if ($raw && isset($raw['encrypted_aes_key'])) {
                    $decrypted = $this->decryptRequest($raw);
                }
            }
            if (!$decrypted) return response('', 421);

            $body = $decrypted['body'];
            $aesKey = $decrypted['aesKey'];
            $iv = $decrypted['iv'];

            $action = $body['action'] ?? '';
            $screen = $body['screen'] ?? '';
            $data = $body['data'] ?? [];
            $flowToken = $body['flow_token'] ?? '';

            @\file_put_contents($dbg, \date('H:i:s') . " {$action} screen={$screen} data=" . \json_encode($data) . "\n", FILE_APPEND);

            if ($action === 'ping') return $this->respond(['data' => ['status' => 'active']], $aesKey, $iv);
            if (!empty($data['error'])) return $this->respond(['data' => ['acknowledged' => true]], $aesKey, $iv);

            $response = match ($action) {
                'INIT' => ['screen' => 'SERVICE_SELECT', 'data' => $this->serviceData($flowToken)],
                'data_exchange' => $this->exchange($screen, $data, $flowToken),
                'BACK' => ['screen' => 'SERVICE_SELECT', 'data' => $this->serviceData($flowToken)],
                default => ['screen' => 'SERVICE_SELECT', 'data' => $this->serviceData($flowToken)],
            };

            @\file_put_contents($dbg, \date('H:i:s') . " RESP screen=" . ($response['screen'] ?? '') . "\n", FILE_APPEND);
            return $this->respond($response, $aesKey, $iv);
        } catch (Exception $e) {
            @\file_put_contents($dbg, \date('H:i:s') . " ERROR " . $e->getMessage() . "\n", FILE_APPEND);
            Log::error('[Flow] ' . $e->getMessage());
            return response('', 500);
        }
    }

    protected function exchange(string $screen, array $data, string $ft): array
    {
        $service = $data['selected_service'] ?? '';
        $fromScreen = $data['screen'] ?? $screen;

        // Extract phone from flow_token: vanigan_new_91xxx or vanigan_member_91xxx
        $phone = '';
        if (preg_match('/vanigan_(?:new|member)_(\d+)/', $ft, $m)) {
            $phone = $m[1];
        }
        $formattedPhone = $phone;
        if (strlen($formattedPhone) === 12 && str_starts_with($formattedPhone, '91')) {
            $formattedPhone = substr($formattedPhone, 2);
        }

        // SERVICE_SELECT
        if ($fromScreen === 'SERVICE_SELECT') {
            return match ($service) {
                'register' => ['screen' => 'EPIC_ENTRY', 'data' => ['flow_token' => $ft, 'error_msg' => '', 'has_error' => false]],
                'wings_list' => ['screen' => 'WINGS_LIST', 'data' => $this->buildWingsData($ft)],
                'how_to_register' => ['screen' => 'HOW_TO_REGISTER', 'data' => ['how_to_info' => "How to Register:\n\n1. Click Register\n2. Enter EPIC number\n3. Confirm voter details\n4. Select DOB\n5. Select Blood Group\n6. Enter Address\n7. Upload photo on website\n8. Set PIN\n9. Card generated!", 'flow_token' => $ft]],
                'help_support' => ['screen' => 'HELP_SUPPORT', 'data' => ['help_info' => "Help & Support\n\nWebsite: www.vanigan.digital\n\nCommon Issues:\n- EPIC not found: Check Voter ID card\n- Photo failed: Use JPG/PNG under 15MB\n- Card issue: Contact support", 'flow_token' => $ft]],
                'become_organizer' => $this->buildOrganizerScreen($formattedPhone, $ft),
                'your_members' => $this->buildYourMembersScreen($formattedPhone, $ft),
                default => $this->complete($service, $ft),
            };
        }

        // EPIC_ENTRY
        if ($fromScreen === 'EPIC_ENTRY') {
            $epicNo = strtoupper(trim($data['epic_no'] ?? ''));
            if (empty($epicNo)) return ['screen' => 'EPIC_ENTRY', 'data' => ['flow_token' => $ft]];
            $voter = VoterHelper::findByEpicNo($epicNo);
            if (!$voter) {
                return ['screen' => 'EPIC_ENTRY', 'data' => [
                    'flow_token' => $ft,
                    'error_msg' => "EPIC \"{$epicNo}\" not found. Please check your Voter ID card and enter the correct EPIC number.",
                    'has_error' => true,
                ]];
            }
            $name = $voter['name'] ?? '';
            $asm = $voter['assembly_name'] ?? '';
            $dist = $voter['district'] ?? '';
            $zd = config('zone_data');
            if ($zd && !empty($zd['assembly_map']) && $asm) {
                $key = strtoupper(trim(preg_replace('/\s+/', ' ', $asm)));
                $matched = $zd['assembly_map'][$key] ?? null;
                if ($matched) $dist = $matched['d'];
            }
            $dist = ucwords(strtolower($dist));
            return ['screen' => 'VOTER_CONFIRM', 'data' => [
                'voter_info' => "Name: {$name}\nEPIC: {$epicNo}\nAssembly: {$asm}\nDistrict: {$dist}",
                'epic_no' => $epicNo, 'voter_name' => $name, 'assembly' => $asm, 'district' => $dist, 'flow_token' => $ft,
            ]];
        }

        // VOTER_CONFIRM → DOB
        if ($fromScreen === 'VOTER_CONFIRM') {
            return ['screen' => 'DOB_SELECT', 'data' => [
                'epic_no' => $data['epic_no'] ?? '', 'voter_name' => $data['voter_name'] ?? '',
                'assembly' => $data['assembly'] ?? '', 'district' => $data['district'] ?? '', 'flow_token' => $ft,
            ]];
        }

        // DOB → BLOOD
        if ($fromScreen === 'DOB_SELECT') {
            return ['screen' => 'BLOOD_GROUP', 'data' => [
                'dob' => $data['dob'] ?? '', 'epic_no' => $data['epic_no'] ?? '', 'voter_name' => $data['voter_name'] ?? '',
                'assembly' => $data['assembly'] ?? '', 'district' => $data['district'] ?? '', 'flow_token' => $ft,
            ]];
        }

        // BLOOD → ADDRESS
        if ($fromScreen === 'BLOOD_GROUP') {
            return ['screen' => 'ADDRESS_ENTRY', 'data' => [
                'blood_group' => $data['blood_group'] ?? '', 'dob' => $data['dob'] ?? '',
                'epic_no' => $data['epic_no'] ?? '', 'voter_name' => $data['voter_name'] ?? '',
                'assembly' => $data['assembly'] ?? '', 'district' => $data['district'] ?? '', 'flow_token' => $ft,
            ]];
        }

        // ADDRESS → complete
        if ($fromScreen === 'ADDRESS_ENTRY') {
            return $this->complete('register_complete', $ft, [
                'address' => $data['address'] ?? '', 'blood_group' => $data['blood_group'] ?? '',
                'dob' => $data['dob'] ?? '', 'epic_no' => $data['epic_no'] ?? '',
                'voter_name' => $data['voter_name'] ?? '', 'assembly' => $data['assembly'] ?? '',
                'district' => $data['district'] ?? '',
            ]);
        }

        // YOUR_MEMBERS → select member → MEMBER_DETAIL
        if ($fromScreen === 'YOUR_MEMBERS') {
            $selectedMemberId = $data['selected_member'] ?? '';
            if (!empty($selectedMemberId)) {
                return $this->buildMemberDetail($selectedMemberId, $ft);
            }
        }

        // LOAN FLOW: BUSINESS_TYPE → BUSINESS_NAME
        if ($fromScreen === 'BUSINESS_TYPE') {
            return [
                'screen' => 'BUSINESS_NAME',
                'data' => [
                    'business_type' => $data['business_type'] ?? '',
                    'flow_token' => $ft,
                ],
            ];
        }

        // LOAN FLOW: BUSINESS_NAME → complete (submit loan)
        if ($fromScreen === 'BUSINESS_NAME') {
            return $this->complete('loan_submitted', $ft, [
                'business_type' => $data['business_type'] ?? '',
                'business_name' => $data['business_name'] ?? '',
            ]);
        }

        return ['screen' => 'SERVICE_SELECT', 'data' => $this->serviceData($ft)];
    }

    // ═══ DYNAMIC DATA BUILDERS ═══

    protected function buildWingsData(string $ft): array
    {
        $wings = [
            "1. Women's Entrepreneur Wing",
            "2. Auditor's Wing",
            "3. Doctor's Wing",
            "4. Advocate Wing",
            "5. Agriculture Wing",
            "6. Information Technology Wing",
            "7. Engineer Wing",
            "8. Labor Wing",
            "9. Differently Abled Wing",
            "10. Young Entrepreneur Wing",
            "11. Spokesperson's Wing",
            "12. Distributor's Wing",
            "13. Manufacturer's Wing",
            "14. Real Estate Industry Wing",
            "15. Pharmacist & Pharma Wing",
            "16. Educator's Wing",
            "17. Import & Export Business Wing",
            "18. Third Gender Entrepreneur Wing",
            "19. Shop Owner's Wing",
            "20. Central Govt Relationship Wing",
            "21. State Govt Relationship Wing",
            "22. Restaurant Owner's Wing",
            "23. Tourism & Transport Wing",
            "24. Sports & Sports Business Wing",
            "25. Marine Based Business Wing",
            "26. Tribe's Entrepreneur Wing",
            "27. Digital Promoter's Wing",
            "28. Printing & Press Business Wing",
            "29. Computer & Mobile Business Wing",
            "30. Weaver Business Wing",
            "31. Finance, Insurance & Chit Fund Wing",
            "32. Roadside Vendor's Wing",
            "33. Lodging Business Wing",
            "34. Beautician & Fitness Wing",
        ];

        return [
            'wings_title' => 'Tamil Nadu Vanigargalin Sangamam - All 34 Wings',
            'wings_info' => implode("\n", $wings),
            'flow_token' => $ft,
        ];
    }

    protected function buildOrganizerScreen(string $phone, string $ft): array
    {
        $member = $phone ? $this->mongo->findMemberByMobile($phone) : null;
        $referralCount = (int)($member['referral_count'] ?? 0);
        $uniqueId = $member['unique_id'] ?? '';
        $name = $member['name'] ?? 'Member';
        $remaining = max(0, 25 - $referralCount);
        $appUrl = config('app.url');

        // Build text progress bar: █████░░░░░ 8/25
        $filled = min(25, $referralCount);
        $barFilled = str_repeat('█', $filled);
        $barEmpty = str_repeat('░', 25 - $filled);
        $progressBar = $barFilled . $barEmpty . "  " . $referralCount . " / 25";

        // Status
        if ($referralCount >= 25) {
            $status = "✅ Congratulations, {$name}!\n\nYou have completed 25 referrals and qualified to become an Organizer!\n\nContact admin to activate your organizer role.";
        } else {
            $status = "👤 {$name}\n\n📊 Referral Progress: {$referralCount} / 25\n⏳ {$remaining} more referrals needed to become an Organizer.";
        }

        $referralInfo = "Share your referral link below to invite new members. Each registration counts towards your organizer goal!";

        // Build referral link
        $referralId = '';
        if ($member && !empty($uniqueId)) {
            $referralId = $this->mongo->getOrCreateReferralId($uniqueId);
        }
        $referralLink = $referralId ? rtrim($appUrl, '/') . '/refer/' . $uniqueId . '/' . $referralId : rtrim($appUrl, '/');

        return [
            'screen' => 'ORGANIZER',
            'data' => [
                'org_status' => $status,
                'progress_bar' => $progressBar,
                'referral_info' => $referralInfo,
                'referral_link' => $referralLink,
                'flow_token' => $ft,
            ],
        ];
    }

    protected function buildYourMembersScreen(string $phone, string $ft): array
    {
        $member = $phone ? $this->mongo->findMemberByMobile($phone) : null;
        if (!$member || empty($member['unique_id'])) {
            return $this->complete('your_members', $ft, ['count' => '0', 'members_text' => 'No members found.']);
        }

        $referred = $this->mongo->getMembersReferredBy($member['unique_id']);
        $count = count($referred);

        if ($count === 0) {
            return $this->complete('your_members', $ft, ['count' => '0', 'members_text' => 'No referrals yet. Share your link!', 'unique_id' => $member['unique_id']]);
        }

        // Build members list with photos (like restaurant menu items)
        $membersList = [];
        foreach (array_slice($referred, 0, 10) as $r) {
            $item = [
                'id' => $r['unique_id'] ?? '',
                'title' => $r['name'] ?? 'Unknown',
                'description' => ($r['unique_id'] ?? '') . ' - ' . ($r['assembly'] ?? ''),
            ];
            // Convert photo to base64 for flow image
            if (!empty($r['photo_url'])) {
                try {
                    $imgData = @\file_get_contents($r['photo_url']);
                    if ($imgData && strlen($imgData) < 200000) {
                        $item['image'] = base64_encode($imgData);
                    }
                } catch (Exception $e) {}
            }
            $membersList[] = $item;
        }

        return [
            'screen' => 'YOUR_MEMBERS',
            'data' => [
                'members' => $membersList,
                'members_count' => "Total: {$count} member(s)",
                'flow_token' => $ft,
            ],
        ];
    }

    protected function buildMemberDetail(string $uniqueId, string $ft): array
    {
        $member = $this->mongo->findMemberByUniqueId($uniqueId);
        if (!$member) {
            return $this->complete('member_viewed', $ft);
        }

        // Build member info text
        $info = "Name: " . ($member['name'] ?? '') . "\n";
        $info .= "TNVS ID: " . ($member['unique_id'] ?? '') . "\n";
        $info .= "EPIC: " . ($member['epic_no'] ?? '') . "\n";
        $info .= "Mobile: " . ($member['contact_number'] ?? '+91 ' . ($member['mobile'] ?? '')) . "\n";
        $info .= "Assembly: " . ($member['assembly'] ?? '') . "\n";
        $info .= "District: " . ($member['district'] ?? '') . "\n";
        $info .= "Zone: " . ($member['zone'] ?? '') . "\n";
        if (!empty($member['dob'])) $info .= "DOB: " . $member['dob'] . "\n";
        if (!empty($member['blood_group'])) $info .= "Blood Group: " . $member['blood_group'];

        // Get photo as base64
        $photoB64 = '';
        if (!empty($member['photo_url'])) {
            try {
                $imgData = @\file_get_contents($member['photo_url']);
                if ($imgData) {
                    $photoB64 = base64_encode($imgData);
                }
            } catch (Exception $e) {}
        }

        return [
            'screen' => 'MEMBER_DETAIL',
            'data' => [
                'member_photo' => $photoB64 ?: '',
                'member_info' => $info,
                'flow_token' => $ft,
            ],
        ];
    }

    // ═══ HELPERS ═══

    protected function complete(string $service, string $ft, array $extra = []): array
    {
        return ['screen' => 'SUCCESS', 'data' => ['extension_message_response' => ['params' => array_merge(['flow_token' => $ft, 'selected_service' => $service], $extra)]]];
    }

    protected function serviceData(string $ft): array
    {
        return [
            'services' => [
                ['id' => 'register', 'title' => 'Register', 'description' => 'Register for membership card'],
                ['id' => 'wings_list', 'title' => 'Wings List', 'description' => 'View organization wings'],
                ['id' => 'how_to_register', 'title' => 'How to Register', 'description' => 'Step-by-step guide'],
                ['id' => 'website', 'title' => 'Website', 'description' => 'Visit our website'],
                ['id' => 'download', 'title' => 'Download Vanigan', 'description' => 'Download the app'],
                ['id' => 'help_support', 'title' => 'Help & Support', 'description' => 'Get assistance'],
            ],
            'flow_token' => $ft,
        ];
    }

    // ═══ ENCRYPTION ═══

    protected function decryptRequest(array $body): ?array
    {
        $keyPem = config('whatsapp.flow_private_key', '');
        if (empty($keyPem)) return ['body' => $body, 'aesKey' => null, 'iv' => null];
        $keyPem = str_replace('\\n', "\n", $keyPem);
        if (strpos($keyPem, '-----BEGIN') === false) return null;
        try {
            $encAesKey = base64_decode($body['encrypted_aes_key'] ?? '');
            $encFlowData = base64_decode($body['encrypted_flow_data'] ?? '');
            $initVector = base64_decode($body['initial_vector'] ?? '');
            if (empty($encAesKey) || empty($encFlowData) || empty($initVector)) return null;
            $rsa = RSA::loadPrivateKey($keyPem);
            $rsa = $rsa->withPadding(RSA::ENCRYPTION_OAEP)->withHash('sha256')->withMGFHash('sha256');
            $aesKey = $rsa->decrypt($encAesKey);
            if (!$aesKey) return null;
            $tag = substr($encFlowData, -16);
            $cipher = substr($encFlowData, 0, -16);
            $plain = \openssl_decrypt($cipher, 'aes-128-gcm', $aesKey, OPENSSL_RAW_DATA, $initVector, $tag);
            if ($plain === false) return null;
            return ['body' => json_decode($plain, true), 'aesKey' => $aesKey, 'iv' => $initVector];
        } catch (Exception $e) {
            Log::error('[Flow] decrypt: ' . $e->getMessage());
            return null;
        }
    }

    protected function respond(array $obj, ?string $aesKey, ?string $iv)
    {
        if (!$aesKey || !$iv) return response()->json($obj);
        try {
            $flipped = '';
            for ($i = 0; $i < strlen($iv); $i++) $flipped .= chr(~ord($iv[$i]) & 0xff);
            $tag = '';
            $enc = \openssl_encrypt(json_encode($obj), 'aes-128-gcm', $aesKey, OPENSSL_RAW_DATA, $flipped, $tag);
            return response(base64_encode($enc . $tag), 200)->header('Content-Type', 'text/plain');
        } catch (Exception $e) {
            return response('', 500);
        }
    }
}
