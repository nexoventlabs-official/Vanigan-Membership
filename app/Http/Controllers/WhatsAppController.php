<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use App\Services\WhatsAppMongoService;
use App\Services\MongoService;
use App\Services\FlowImageService;
use Exception;

class WhatsAppController extends Controller
{
    protected WhatsAppService $whatsApp;
    protected WhatsAppMongoService $whatsAppMongo;
    protected MongoService $mongo;
    protected FlowImageService $flowImages;

    public function __construct(WhatsAppService $whatsApp, WhatsAppMongoService $whatsAppMongo, MongoService $mongo, FlowImageService $flowImages)
    {
        $this->whatsApp = $whatsApp;
        $this->whatsAppMongo = $whatsAppMongo;
        $this->mongo = $mongo;
        $this->flowImages = $flowImages;
    }

    public function webhook(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request);
        }

        return $this->handleIncomingMessage($request);
    }

    protected function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $result = $this->whatsApp->verifyWebhook($mode, $token, $challenge);

        if ($result !== null) {
            return response($result, 200);
        }

        return response('Forbidden', 403);
    }

    protected function handleIncomingMessage(Request $request)
    {
        try {
            $payload = json_decode($request->getContent(), true) ?? [];
            
            // Debug to public file
            $dbg = \base_path('public/wa_debug.txt');
            $msgType = $payload['entry'][0]['changes'][0]['value']['messages'][0]['type'] ?? 'no_msg';
            $interType = $payload['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['type'] ?? 'no_inter';
            @\file_put_contents($dbg, \date('H:i:s') . " WEBHOOK msg_type={$msgType} inter_type={$interType}\n", FILE_APPEND);

            // Check for flow completion (nfm_reply)
            $flowResponse = $this->extractFlowResponse($payload);
            @\file_put_contents($dbg, \date('H:i:s') . " FLOW_CHECK found=" . ($flowResponse ? 'YES' : 'NO') . "\n", FILE_APPEND);
            if ($flowResponse) {
                @\file_put_contents($dbg, \date('H:i:s') . " FLOW_COMPLETE service=" . ($flowResponse['params']['selected_service'] ?? '?') . " params=" . \json_encode($flowResponse['params']) . "\n", FILE_APPEND);
                return $this->handleFlowCompletion($flowResponse, $payload);
            }

            // Check for image messages (photo upload)
            $imageMessage = $this->extractImageMessage($payload);
            if ($imageMessage) {
                return $this->handleImageMessage($imageMessage);
            }

            $parsed = $this->whatsApp->parseWebhookPayload($payload);

            if (!$parsed || !$parsed['from']) {
                return response()->json(['status' => 'ignored']);
            }

            $phone = $parsed['from'];
            $text = strtolower(trim($parsed['text'] ?? ''));
            $contactName = $parsed['contact_name'];
            $messageId = $parsed['message_id'];

            $this->whatsAppMongo->trackWhatsAppUser($phone, $contactName, $parsed['text']);

            if ($messageId) {
                $this->whatsApp->markAsRead($messageId);
            }

            // Check for button replies (loan Yes/No)
            if ($text === 'loan_yes') {
                $this->sendLoanFlow($phone);
                return response()->json(['status' => 'processed']);
            }
            if ($text === 'loan_no') {
                $this->whatsApp->sendTextMessage($phone, "No problem! If you change your mind, you can apply anytime by sending *Hi* and selecting *Request Loan*.");
                return response()->json(['status' => 'processed']);
            }

            // Check for greeting variations
            $isGreeting = empty($text) || 
                preg_match('/^h+[aei]+y*$/i', $text) ||
                preg_match('/^hel+o+$/i', $text) ||
                preg_match('/^(vanakkam|வணக்கம்|namaste|start|register|menu)$/i', $text);
            
            if ($isGreeting) {
                $this->sendWelcomeResponse($phone, $contactName);
            }

            return response()->json(['status' => 'processed']);

        } catch (Exception $e) {
            Log::error('WhatsApp webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Send welcome response — flow button for both new and existing users.
     */
    protected function sendWelcomeResponse(string $phone, ?string $contactName)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $existingMember = $this->mongo->findMemberByMobile($formattedPhone);
            $appUrl = config('app.url');
            $flowId = config('whatsapp.welcome_flow_id', '');
            $welcomeImageUrl = $this->flowImages->getImageUrl('welcome_image');

            if ($existingMember && !empty($flowId)) {
                // ─── Existing member: Welcome back + Choose Service flow ───
                $name = $existingMember['name'] ?? $contactName ?? '';
                $services = $this->buildServicesWithIcons([
                    ['id' => 'view_card', 'title' => 'View Your Card', 'description' => 'View your digital member ID card', 'icon' => 'flow_view_card'],
                    ['id' => 'refer_friend', 'title' => 'Refer a Friend', 'description' => 'Share your referral link', 'icon' => 'flow_refer_friend'],
                    ['id' => 'your_members', 'title' => 'Your Members', 'description' => 'View your referred members', 'icon' => 'flow_your_members'],
                    ['id' => 'become_organizer', 'title' => 'Become an Organizer', 'description' => 'Apply for organizer role', 'icon' => 'flow_become_organizer'],
                    ['id' => 'request_loan', 'title' => 'Request Loan', 'description' => 'Apply for 25L interest-free loan', 'icon' => 'flow_request_loan'],
                    ['id' => 'wings_list', 'title' => 'Wings List', 'description' => 'View organization wings', 'icon' => 'flow_wings_list'],
                    ['id' => 'website', 'title' => 'Website', 'description' => 'Visit our website', 'icon' => 'flow_website'],
                    ['id' => 'download', 'title' => 'Download Vanigan', 'description' => 'Download the app', 'icon' => 'flow_download'],
                    ['id' => 'help_support', 'title' => 'Help & Support', 'description' => 'Get assistance', 'icon' => 'flow_help_support'],
                ]);
                $result = $this->whatsApp->sendFlowMessage($phone, [
                    'flowId' => $flowId,
                    'flowCta' => 'Choose Service',
                    'headerImageUrl' => $welcomeImageUrl,
                    'headerText' => 'Tamil Nadu Vanigargalin Sangamam',
                    'bodyText' => "🙏 *Welcome Back, {$name}!*\n\n*Tamil Nadu Vanigargalin Sangamam*\n\n🎫 Your membership is active.\nChoose a service below!",
                    'footerText' => '© Tamil Nadu Vanigargalin Sangamam',
                    'screenName' => 'SERVICE_SELECT',
                    'screenData' => ['services' => $services],
                    'flowToken' => "vanigan_member_{$phone}",
                    'mode' => 'published',
                ]);
                $this->whatsAppMongo->updateStatus($phone, 'existing_member');

            } elseif (!empty($flowId)) {
                // ─── New user: Welcome + Choose Service flow ───
                $services = $this->buildServicesWithIcons([
                    ['id' => 'register', 'title' => 'Register', 'description' => 'Register for membership card', 'icon' => 'flow_register'],
                    ['id' => 'wings_list', 'title' => 'Wings List', 'description' => 'View organization wings', 'icon' => 'flow_wings_list'],
                    ['id' => 'how_to_register', 'title' => 'How to Register', 'description' => 'Step-by-step guide', 'icon' => 'flow_how_to_register'],
                    ['id' => 'website', 'title' => 'Website', 'description' => 'Visit our website', 'icon' => 'flow_website'],
                    ['id' => 'download', 'title' => 'Download Vanigan', 'description' => 'Download the app', 'icon' => 'flow_download'],
                    ['id' => 'help_support', 'title' => 'Help & Support', 'description' => 'Get assistance', 'icon' => 'flow_help_support'],
                ]);
                $result = $this->whatsApp->sendFlowMessage($phone, [
                    'flowId' => $flowId,
                    'flowCta' => 'Choose Service',
                    'headerImageUrl' => $welcomeImageUrl,
                    'headerText' => 'Tamil Nadu Vanigargalin Sangamam',
                    'bodyText' => "👋 *Welcome!*\n\n*Tamil Nadu Vanigargalin Sangamam*\n\n🎫 Get Your Digital Member ID Card\n\nChoose a service from the menu below!",
                    'footerText' => '© Tamil Nadu Vanigargalin Sangamam',
                    'screenName' => 'SERVICE_SELECT',
                    'screenData' => ['services' => $services],
                    'flowToken' => "vanigan_new_{$phone}",
                    'mode' => 'published',
                ]);
                $this->whatsAppMongo->updateStatus($phone, 'welcome_flow_sent');

            } else {
                $result = $this->whatsApp->sendWelcomeMessage($phone, $appUrl);
                $this->whatsAppMongo->updateStatus($phone, 'welcome_sent');
            }

            if (isset($result) && ($result['success'] ?? false)) {
                $this->whatsAppMongo->trackOutgoingMessage($phone, 'Welcome message sent');
            }
        } catch (Exception $e) {
            Log::error('WhatsApp sendWelcomeResponse error: ' . $e->getMessage());
        }
    }

    /**
     * Extract flow response from webhook payload (nfm_reply type).
     */
    protected function extractFlowResponse(array $payload): ?array
    {
        try {
            $value = $payload['entry'][0]['changes'][0]['value'] ?? null;
            if (!$value || !isset($value['messages'][0])) return null;

            $message = $value['messages'][0];
            
            // Check for interactive nfm_reply (flow completion)
            if (($message['type'] ?? '') === 'interactive') {
                $interactive = $message['interactive'] ?? [];
                if (($interactive['type'] ?? '') === 'nfm_reply') {
                    $responseJson = $interactive['nfm_reply']['response_json'] ?? '{}';
                    $params = json_decode($responseJson, true) ?? [];
                    
                    return [
                        'from' => $message['from'] ?? '',
                        'message_id' => $message['id'] ?? '',
                        'contact_name' => $value['contacts'][0]['profile']['name'] ?? '',
                        'params' => $params,
                    ];
                }
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Handle flow completion — process the response from completed flow.
     */
    protected function handleFlowCompletion(array $flowResponse, array $payload)
    {
        try {
            $phone = $flowResponse['from'];
            $params = $flowResponse['params'];
            $selectedService = $params['selected_service'] ?? '';
            $action = $params['action'] ?? '';

            @\file_put_contents(\base_path('public/wa_debug.txt'), \date('H:i:s') . " FLOW_DONE service={$selectedService} params=" . \json_encode($params) . "\n", FILE_APPEND);

            if ($flowResponse['message_id']) {
                $this->whatsApp->markAsRead($flowResponse['message_id']);
            }

            $appUrl = config('app.url');
            $formattedPhone = $this->formatPhoneNumber($phone);
            $member = $this->mongo->findMemberByMobile($formattedPhone);

            // ─── Register complete (DOB, blood, address collected) → website for photo+PIN ───
            if ($selectedService === 'register_complete') {
                // Check if already registered
                if ($member && !empty($member['card_front_url'])) {
                    $cardUrl = rtrim($appUrl, '/') . '/member/card/' . $member['unique_id'];
                    $this->whatsApp->sendCtaUrlMessage($phone, "🎉 *Card Already Generated!*\n\nYour membership card is ready.", 'View Your Card 🪪', $cardUrl, null, '© Tamil Nadu Vanigargalin Sangamam');
                } else {
                    $this->handleRegisterComplete($phone, $params);
                }
            }
            // ─── Register with PIN set ───
            elseif ($selectedService === 'register' && $action === 'pin_set') {
                $this->handleRegistrationPinSet($phone, $params);
            }
            // ─── EPIC not found ───
            elseif ($selectedService === 'epic_not_found') {
                $this->whatsApp->sendTextMessage($phone, "❌ EPIC Number *" . ($params['epic_no'] ?? '') . "* not found.\n\nPlease check your Voter ID card and try again by sending *Hi*.");
            }
            // ─── View Card → CTA to website with mobile (no need to ask mobile again) ───
            elseif ($selectedService === 'view_card') {
                if ($member && !empty($member['unique_id'])) {
                    // Route to website with mobile — website asks PIN, then shows card
                    $viewUrl = rtrim($appUrl, '/') . '/member/verify/' . $member['unique_id'];
                    $this->whatsApp->sendCtaUrlMessage($phone, "🪪 *View Your Card*\n\nClick below to verify your PIN and view your card.", 'View Card 🪪', $viewUrl, null, '© Tamil Nadu Vanigargalin Sangamam');
                } else {
                    $this->whatsApp->sendTextMessage($phone, "❌ No card found. Register first by sending *Hi*.");
                }
            }
            // ─── Refer a Friend ───
            elseif ($selectedService === 'refer_friend') {
                if ($member && !empty($member['unique_id'])) {
                    $referralId = $this->mongo->getOrCreateReferralId($member['unique_id']);
                    $referralUrl = rtrim($appUrl, '/') . '/refer/' . $member['unique_id'] . '/' . $referralId;
                    $this->whatsApp->sendCtaUrlMessage($phone, "🤝 *Refer a Friend*\n\nShare this link to invite friends!\n\nYour referrals: " . ($member['referral_count'] ?? 0) . " / 25", 'Share Link 🔗', $referralUrl);
                }
            }
            // ─── Your Members (with details) ───
            elseif ($selectedService === 'your_members') {
                $membersText = $params['members_text'] ?? '';
                $count = $params['count'] ?? '0';
                $this->whatsApp->sendTextMessage($phone, "👥 *Your Referred Members*\n\n{$membersText}");
                // If they have members, also send referral link
                if ((int)$count > 0 && $member && !empty($member['unique_id'])) {
                    $referralId = $this->mongo->getOrCreateReferralId($member['unique_id']);
                    $referralUrl = rtrim($appUrl, '/') . '/refer/' . $member['unique_id'] . '/' . $referralId;
                    $this->whatsApp->sendCtaUrlMessage($phone, "Keep sharing to grow your network!", 'Share Link 🔗', $referralUrl);
                }
            }
            // ─── Become Organizer (referral count + CTA) ───
            elseif ($selectedService === 'become_organizer') {
                $refCount = (int)($params['referral_count'] ?? 0);
                $remaining = max(0, 25 - $refCount);
                $name = $params['name'] ?? 'Member';

                $msg = "🏅 *Become an Organizer*\n\n";
                $msg .= "👤 {$name}\n";
                $msg .= "📊 Referrals: {$refCount} / 25\n";
                if ($remaining > 0) {
                    $msg .= "⏳ {$remaining} more needed\n\n";
                    $msg .= "Share your referral link to invite more members!";
                } else {
                    $msg .= "✅ You have qualified!\n\nContact admin for organizer role.";
                }
                $this->whatsApp->sendTextMessage($phone, $msg);

                // Send referral CTA
                if ($member && !empty($member['unique_id'])) {
                    $referralId = $this->mongo->getOrCreateReferralId($member['unique_id']);
                    $referralUrl = rtrim($appUrl, '/') . '/refer/' . $member['unique_id'] . '/' . $referralId;
                    $this->whatsApp->sendCtaUrlMessage($phone, "Share your referral link:", 'Share Link 🔗', $referralUrl);
                }
            }
            // ─── Website ───
            elseif ($selectedService === 'website') {
                $this->whatsApp->sendCtaUrlMessage($phone, "🌐 *Visit Our Website*\n\nExplore Tamil Nadu Vanigargalin Sangamam.", 'Visit Website 🌐', $appUrl, null, '© Tamil Nadu Vanigargalin Sangamam');
            }
            // ─── Download App (Play Store link, NOT card download) ───
            elseif ($selectedService === 'download') {
                $playStoreUrl = 'https://play.google.com/store/apps/details?id=io.vanigan.ai';
                $this->whatsApp->sendCtaUrlMessage($phone, "📱 *Download Vanigan App*\n\nGet the official app from Google Play Store.", 'Download App 📱', $playStoreUrl, null, '© Tamil Nadu Vanigargalin Sangamam');
            }
            // ─── How to Register (video tutorial CTA) ───
            elseif ($selectedService === 'how_to_register') {
                $videoUrl = 'https://drive.google.com/file/d/1teKfmWwyzYTZmuP9PHgmyx1LxixkZ_Ej/view';
                $this->whatsApp->sendCtaUrlMessage($phone, "📝 *How to Register*\n\nWatch this video tutorial to learn how to register step by step.", 'Watch Video 🎬', $videoUrl, null, '© Tamil Nadu Vanigargalin Sangamam');
            }
            // ─── Request Loan (send intro + Yes/No buttons) ───
            elseif ($selectedService === 'request_loan') {
                $this->handleLoanRequest($phone, $formattedPhone);
            }
            // ─── Loan: user said Yes → send loan flow ───
            elseif ($selectedService === 'loan_yes') {
                $this->sendLoanFlow($phone);
            }
            // ─── Loan flow completed (business type + name submitted) ───
            elseif ($selectedService === 'loan_submitted') {
                $this->handleLoanSubmission($phone, $formattedPhone, $params);
            }
            // ─── Details saved ───
            elseif ($selectedService === 'details_saved') {
                $this->handleAdditionalDetails($phone, $params);
            }

            return response()->json(['status' => 'processed']);

        } catch (Exception $e) {
            Log::error('Flow completion error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle register_complete from flow — DOB, blood group, address collected.
     * Send CTA to website for photo upload + PIN setup + card generation.
     */
    protected function handleRegisterComplete(string $phone, array $params)
    {
        $dbg = \base_path('public/wa_debug.txt');
        @\file_put_contents($dbg, \date('H:i:s') . " handleRegisterComplete START phone={$phone}\n", FILE_APPEND);
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $appUrl = config('app.url');
            @\file_put_contents($dbg, \date('H:i:s') . " appUrl={$appUrl}\n", FILE_APPEND);

            $queryParams = http_build_query([
                'wa' => $formattedPhone,
                'epic' => $params['epic_no'] ?? '',
                'name' => $params['voter_name'] ?? '',
                'assembly' => $params['assembly'] ?? '',
                'district' => $params['district'] ?? '',
                'dob' => $params['dob'] ?? '',
                'blood' => $params['blood_group'] ?? '',
                'address' => $params['address'] ?? '',
                'from' => 'flow',
            ]);

            $registrationUrl = rtrim($appUrl, '/') . '?' . $queryParams;
            @\file_put_contents($dbg, \date('H:i:s') . " URL={$registrationUrl}\n", FILE_APPEND);

            $result = $this->whatsApp->sendCtaUrlMessage(
                $phone,
                "✅ *Details Saved!*\n\n" .
                "Name: " . ($params['voter_name'] ?? '') . "\n" .
                "EPIC: " . ($params['epic_no'] ?? '') . "\n" .
                "DOB: " . ($params['dob'] ?? '') . "\n" .
                "Blood Group: " . ($params['blood_group'] ?? '') . "\n\n" .
                "Now upload your photo and set a PIN to generate your card!",
                'Get Your Card 🪪',
                $registrationUrl,
                null,
                '© Tamil Nadu Vanigargalin Sangamam'
            );
            @\file_put_contents($dbg, \date('H:i:s') . " CTA_RESULT=" . \json_encode($result) . "\n", FILE_APPEND);

            $this->whatsAppMongo->updateStatus($phone, 'flow_details_collected');

        } catch (Exception $e) {
            Log::error('handleRegisterComplete error: ' . $e->getMessage());
            $this->whatsApp->sendTextMessage($phone, '❌ Something went wrong. Please try again by sending *Hi*.');
        }
    }

    /**
     * Handle loan request — send intro message with Yes/No reply buttons.
     */
    protected function handleLoanRequest(string $phone, string $formattedPhone)
    {
        try {
            $member = $this->mongo->findMemberByMobile($formattedPhone);
            if (!$member) $member = $this->mongo->findMemberByMobile($phone);
            if ($member && !empty($member['unique_id'])) {
                $existing = $this->mongo->getLoanRequestByUniqueId($member['unique_id']);
                if ($existing) {
                    $this->whatsApp->sendButtons($phone,
                        "📋 *Loan Already Applied!*\n\n" .
                        "You have already submitted a loan request.\n" .
                        "Business: " . ($existing['business_name'] ?? '') . " (" . ($existing['business_type'] ?? '') . ")\n\n" .
                        "Our team will contact you soon.",
                        [
                            ['id' => 'loan_call', 'text' => '📞 Call Us'],
                            ['id' => 'loan_email', 'text' => '📧 Email Us'],
                        ],
                        '© Tamil Nadu Vanigargalin Sangamam'
                    );
                    return;
                }
            }

            $this->whatsApp->sendButtons($phone,
                "💰 *Interest-Free Loan — ₹25 Lakhs*\n\n" .
                "We provide up to ₹25 Lakhs interest-free loan for:\n\n" .
                "🏢 Pvt Ltd Companies\n" .
                "🤝 Partnership Businesses\n" .
                "🌐 Import & Export Businesses\n\n" .
                "📌 Note: Available for all business types except Proprietorship.\n\n" .
                "Would you like to apply?",
                [
                    ['id' => 'loan_yes', 'text' => 'Yes, Apply'],
                    ['id' => 'loan_no', 'text' => 'No, Thanks'],
                ],
                '© Tamil Nadu Vanigargalin Sangamam'
            );
        } catch (Exception $e) {
            Log::error('handleLoanRequest error: ' . $e->getMessage());
        }
    }

    /**
     * Send the loan application flow.
     */
    protected function sendLoanFlow(string $phone)
    {
        try {
            $loanFlowId = config('whatsapp.loan_flow_id', '');
            if (empty($loanFlowId)) {
                $this->whatsApp->sendTextMessage($phone, "❌ Loan application is not available right now. Please try again later.");
                return;
            }

            $businessTypes = $this->buildServicesWithIcons([
                ['id' => 'pvt_ltd', 'title' => 'Pvt Ltd Company', 'description' => 'Private Limited company', 'icon' => 'flow_loan_pvt_ltd'],
                ['id' => 'partnership', 'title' => 'Partnership Business', 'description' => 'Partnership Deed company', 'icon' => 'flow_loan_partnership'],
                ['id' => 'import_export', 'title' => 'Import Export Business', 'description' => 'Import & Export company', 'icon' => 'flow_loan_import_export'],
            ]);

            $this->whatsApp->sendFlowMessage($phone, [
                'flowId' => $loanFlowId,
                'flowCta' => 'Apply for Loan',
                'headerText' => 'Loan Application',
                'bodyText' => "💰 *Apply for ₹25 Lakhs Interest-Free Loan*\n\nSelect your business type and enter your business name to apply.",
                'footerText' => '© Tamil Nadu Vanigargalin Sangamam',
                'screenName' => 'BUSINESS_TYPE',
                'screenData' => ['business_types' => $businessTypes],
                'flowToken' => "vanigan_loan_{$phone}",
                'mode' => 'published',
            ]);
        } catch (Exception $e) {
            Log::error('sendLoanFlow error: ' . $e->getMessage());
            $this->whatsApp->sendTextMessage($phone, "❌ Something went wrong. Please try again.");
        }
    }

    /**
     * Handle loan flow completion — save to MongoDB + send thank you.
     */
    protected function handleLoanSubmission(string $phone, string $formattedPhone, array $params)
    {
        try {
            $member = $this->mongo->findMemberByMobile($formattedPhone);
            // Also try with full WhatsApp number if not found
            if (!$member) {
                $member = $this->mongo->findMemberByMobile($phone);
            }
            // Try stripping 91 prefix differently
            if (!$member && strlen($phone) > 10) {
                $alt = preg_replace('/^91/', '', preg_replace('/\D/', '', $phone));
                $member = $this->mongo->findMemberByMobile($alt);
            }
            $businessType = $params['business_type'] ?? '';
            $businessName = $params['business_name'] ?? '';

            // Map IDs to display names
            $typeMap = ['pvt_ltd' => 'Pvt Ltd Company', 'partnership' => 'Partnership Business', 'import_export' => 'Import Export Business'];
            $displayType = $typeMap[$businessType] ?? $businessType;

            if ($member && !empty($member['unique_id'])) {
                $this->mongo->storeLoanRequest([
                    'unique_id' => $member['unique_id'],
                    'name' => $member['name'] ?? '',
                    'mobile' => $formattedPhone,
                    'epic_no' => $member['epic_no'] ?? '',
                    'assembly' => $member['assembly'] ?? '',
                    'district' => $member['district'] ?? '',
                    'business_type' => $displayType,
                    'business_name' => $businessName,
                    'status' => 'pending',
                    'created_at' => now()->toISOString(),
                ]);
            }

            // Send thank you message with Call Us / Email Us buttons
            $this->whatsApp->sendButtons($phone,
                "✅ *Thank You for Applying!*\n\n" .
                "Your ₹25 Lakhs interest-free loan request has been submitted.\n\n" .
                "📋 Business: {$businessName}\n" .
                "🏢 Type: {$displayType}\n\n" .
                "Our team will review and contact you soon.",
                [
                    ['id' => 'loan_call', 'text' => '📞 Call Us'],
                    ['id' => 'loan_email', 'text' => '📧 Email Us'],
                ],
                '© Tamil Nadu Vanigargalin Sangamam'
            );

            $this->whatsAppMongo->updateStatus($phone, 'loan_applied');

        } catch (Exception $e) {
            Log::error('handleLoanSubmission error: ' . $e->getMessage());
            $this->whatsApp->sendTextMessage($phone, "❌ Something went wrong. Please try again.");
        }
    }

    /**
     * Handle registration after PIN is set in flow.
     * Store partial registration and ask for photo upload.
     */
    protected function handleRegistrationPinSet(string $phone, array $params)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);

            // Store registration data in tracking MongoDB for later completion
            $registrationData = [
                'phone' => $formattedPhone,
                'whatsapp_id' => $phone,
                'epic_no' => $params['epic_no'] ?? '',
                'voter_name' => $params['voter_name'] ?? '',
                'assembly' => $params['assembly'] ?? '',
                'district' => $params['district'] ?? '',
                'pin' => $params['pin'] ?? '',
                'status' => 'awaiting_photo',
                'created_at' => now()->toISOString(),
            ];

            // Store in whatsapp tracking
            $this->whatsAppMongo->updateStatus($phone, 'awaiting_photo');

            // Store registration data in a separate collection for flow state
            $this->storeFlowState($formattedPhone, $registrationData);

            // Send photo upload request message
            $this->whatsApp->sendTextMessage(
                $phone,
                "✅ *Details Confirmed & PIN Set!*\n\n" .
                "📸 Now please upload your photo.\n\n" .
                "Send a clear passport-size photo of yourself. This will be used on your membership card.\n\n" .
                "📌 Tips:\n" .
                "• Use a recent photo\n" .
                "• Face should be clearly visible\n" .
                "• JPG or PNG format\n" .
                "• Good lighting"
            );

        } catch (Exception $e) {
            Log::error('handleRegistrationPinSet error: ' . $e->getMessage());
            $this->whatsApp->sendTextMessage($phone, '❌ Something went wrong. Please try again by sending *Hi*.');
        }
    }

    /**
     * Handle additional details from flow (DOB, Blood Group, Address).
     */
    protected function handleAdditionalDetails(string $phone, array $params)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $member = $this->mongo->findMemberByMobile($formattedPhone);

            if (!$member) {
                $this->whatsApp->sendTextMessage($phone, '❌ Member not found. Please register first.');
                return;
            }

            $dob = $params['dob'] ?? '';
            $bloodGroup = $params['blood_group'] ?? '';
            $address = $params['address'] ?? '';

            // Calculate age
            $age = '';
            if ($dob) {
                try {
                    $dobDate = new \DateTime($dob);
                    $age = (string) $dobDate->diff(new \DateTime())->y;
                } catch (Exception $e) {
                    $age = '';
                }
            }

            $details = [
                'dob' => $dob,
                'age' => $age,
                'blood_group' => $bloodGroup,
                'address' => $address,
                'details_completed' => true,
            ];

            $this->mongo->updateMemberDetailsByUniqueId($member['unique_id'], $details);

            // Send card generated message with View Card CTA
            $appUrl = config('app.url');
            $cardUrl = rtrim($appUrl, '/') . '/member/card/' . $member['unique_id'];

            $this->whatsApp->sendCtaUrlMessage(
                $phone,
                "🎉 *Your Card Has Been Generated!*\n\n" .
                "*Tamil Nadu Vanigargalin Sangamam*\n\n" .
                "Your membership card is ready. Click below to view it!",
                'View Your Card 🪪',
                $cardUrl,
                null,
                '© Tamil Nadu Vanigargalin Sangamam'
            );

            $this->whatsAppMongo->updateStatus($phone, 'card_generated');

        } catch (Exception $e) {
            Log::error('handleAdditionalDetails error: ' . $e->getMessage());
        }
    }

    /**
     * Extract image message from webhook payload.
     */
    protected function extractImageMessage(array $payload): ?array
    {
        try {
            $value = $payload['entry'][0]['changes'][0]['value'] ?? null;
            if (!$value || !isset($value['messages'][0])) return null;

            $message = $value['messages'][0];
            if (($message['type'] ?? '') === 'image') {
                return [
                    'from' => $message['from'] ?? '',
                    'message_id' => $message['id'] ?? '',
                    'contact_name' => $value['contacts'][0]['profile']['name'] ?? '',
                    'image_id' => $message['image']['id'] ?? '',
                    'mime_type' => $message['image']['mime_type'] ?? 'image/jpeg',
                    'caption' => $message['image']['caption'] ?? '',
                ];
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Handle image message — check if user is in photo upload state.
     */
    protected function handleImageMessage(array $imageMessage)
    {
        try {
            $phone = $imageMessage['from'];
            $formattedPhone = $this->formatPhoneNumber($phone);

            if ($imageMessage['message_id']) {
                $this->whatsApp->markAsRead($imageMessage['message_id']);
            }

            // Check if user is awaiting photo upload
            $flowState = $this->getFlowState($formattedPhone);

            if ($flowState && ($flowState['status'] ?? '') === 'awaiting_photo') {
                // Download the image from WhatsApp
                $imageUrl = $this->downloadAndUploadPhoto($imageMessage['image_id'], $flowState['epic_no'] ?? 'unknown');

                if ($imageUrl) {
                    // Update flow state
                    $flowState['photo_url'] = $imageUrl;
                    $flowState['status'] = 'photo_uploaded';
                    $this->storeFlowState($formattedPhone, $flowState);

                    // Now generate the card with the data we have
                    $this->generateCardFromFlowData($phone, $flowState, $imageUrl);
                } else {
                    $this->whatsApp->sendTextMessage($phone, '❌ Photo upload failed. Please try sending the photo again.');
                }
            } else {
                $this->whatsApp->sendTextMessage($phone, '📸 Photo received! To register, please send *Hi* first.');
            }

            return response()->json(['status' => 'processed']);

        } catch (Exception $e) {
            Log::error('handleImageMessage error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Generate card from flow data after photo upload.
     */
    protected function generateCardFromFlowData(string $phone, array $flowState, string $photoUrl)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $appUrl = config('app.url');

            // Check if already registered
            $existingMember = $this->mongo->findMemberByMobile($formattedPhone);
            $uniqueId = $existingMember ? ($existingMember['unique_id'] ?? '') : $this->mongo->generateUniqueId();

            $qrUrl = $appUrl . '/member/complete/' . $uniqueId;
            $cardUrl = $appUrl . '/member/card/' . $uniqueId;

            $memberData = [
                'unique_id' => $uniqueId,
                'epic_no' => $flowState['epic_no'] ?? '',
                'mobile' => $formattedPhone,
                'name' => $flowState['voter_name'] ?? '',
                'assembly' => $flowState['assembly'] ?? '',
                'district' => $flowState['district'] ?? '',
                'zone' => '',
                'photo_url' => $photoUrl,
                'qr_url' => $qrUrl,
                'card_url' => $cardUrl,
                'dob' => '',
                'age' => '',
                'blood_group' => '',
                'address' => '',
                'contact_number' => '+91 ' . $formattedPhone,
                'details_completed' => false,
                'created_at' => now()->toISOString(),
            ];

            // Hash PIN
            $pin = $flowState['pin'] ?? '';
            if ($pin) {
                $memberData['pin_hash'] = password_hash($pin, PASSWORD_BCRYPT);
            }

            // Correct district & zone from config
            $zoneData = config('zone_data');
            $assembly = $flowState['assembly'] ?? '';
            if ($zoneData && !empty($zoneData['assembly_map']) && !empty($assembly)) {
                $asmMap = $zoneData['assembly_map'];
                $assemblyUpper = strtoupper(trim(preg_replace('/\s+/', ' ', $assembly)));
                $matched = $asmMap[$assemblyUpper] ?? null;
                if (!$matched) {
                    $normalizedInput = preg_replace('/\s*\((SC|ST)\)\s*$/i', '', $assemblyUpper);
                    foreach ($asmMap as $key => $val) {
                        $normalizedKey = preg_replace('/\s*\((SC|ST)\)\s*$/i', '', $key);
                        if (strtoupper(trim($normalizedKey)) === strtoupper(trim($normalizedInput))) {
                            $matched = $val;
                            break;
                        }
                    }
                }
                if ($matched) {
                    $memberData['district'] = ucwords(strtolower($matched['d']));
                    $memberData['zone'] = ucwords(strtolower($matched['z']));
                }
            }

            // Save to MongoDB
            $this->mongo->upsertMember($memberData['epic_no'], $memberData);

            // Send success message with "Fill Additional Details" flow button
            $flowId = config('whatsapp.welcome_flow_id', '');

            $this->whatsApp->sendTextMessage(
                $phone,
                "✅ *Photo Uploaded Successfully!*\n\n" .
                "📸 Your photo has been saved.\n\n" .
                "Now let's fill in your additional details to complete your card."
            );

            // Send additional details via CTA URL (complete details on website)
            if (!empty($uniqueId)) {
                $detailsUrl = rtrim($appUrl, '/') . '/member/complete/' . $uniqueId;
                $this->whatsApp->sendCtaUrlMessage(
                    $phone,
                    "📝 Fill in your Date of Birth, Blood Group, and Address to complete your card.",
                    'Fill Details ✏️',
                    $detailsUrl,
                    null,
                    '© Tamil Nadu Vanigargalin Sangamam'
                );
            } else {
                // Fallback: send card URL directly
                $this->whatsApp->sendCtaUrlMessage(
                    $phone,
                    "🎉 *Your Card Has Been Generated!*\n\nClick below to view and complete your details.",
                    'View Your Card 🪪',
                    $cardUrl
                );
            }

            // Clean up flow state
            $this->deleteFlowState($formattedPhone);
            $this->whatsAppMongo->updateStatus($phone, 'photo_uploaded');

        } catch (Exception $e) {
            Log::error('generateCardFromFlowData error: ' . $e->getMessage());
            $this->whatsApp->sendTextMessage($phone, '❌ Card generation failed. Please try again by sending *Hi*.');
        }
    }

    /**
     * Download image from WhatsApp and upload to Cloudinary.
     */
    protected function downloadAndUploadPhoto(string $mediaId, string $epicNo): ?string
    {
        try {
            $accessToken = config('whatsapp.access_token');
            $apiVersion = config('whatsapp.api_version', 'v18.0');

            // Step 1: Get media URL
            $mediaResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://graph.facebook.com/{$apiVersion}/{$mediaId}");

            if (!$mediaResponse->successful()) {
                Log::error('Media URL fetch failed', ['response' => $mediaResponse->json()]);
                return null;
            }

            $mediaUrl = $mediaResponse->json()['url'] ?? '';
            if (empty($mediaUrl)) return null;

            // Step 2: Download the image
            $imageResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($mediaUrl);

            if (!$imageResponse->successful()) {
                Log::error('Media download failed');
                return null;
            }

            // Step 3: Save temporarily and upload to Cloudinary
            $tempPath = storage_path('app/temp_' . $epicNo . '_' . time() . '.jpg');
            file_put_contents($tempPath, $imageResponse->body());

            $cloudinary = new \Cloudinary\Cloudinary(config('cloudinary.cloud_url'));
            $result = $cloudinary->uploadApi()->upload($tempPath, [
                'folder' => 'vanigan/member_photos',
                'public_id' => $epicNo . '_' . time(),
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            // Clean up temp file
            @unlink($tempPath);

            return $result['secure_url'] ?? null;

        } catch (Exception $e) {
            Log::error('downloadAndUploadPhoto error: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Flow State Management (MongoDB tracking collection) ───

    protected function storeFlowState(string $phone, array $data): void
    {
        try {
            $url = config('mongodb.tracking_url');
            $database = config('mongodb.tracking_database', 'vanigan_tracking');
            if (empty($url)) return;

            $client = new \MongoDB\Client($url);
            $collection = $client->selectDatabase($database)->selectCollection('flow_states');

            $collection->updateOne(
                ['phone' => $phone],
                ['$set' => array_merge($data, ['updated_at' => now()->toISOString()])],
                ['upsert' => true]
            );
        } catch (Exception $e) {
            Log::error('storeFlowState error: ' . $e->getMessage());
        }
    }

    protected function getFlowState(string $phone): ?array
    {
        try {
            $url = config('mongodb.tracking_url');
            $database = config('mongodb.tracking_database', 'vanigan_tracking');
            if (empty($url)) return null;

            $client = new \MongoDB\Client($url);
            $collection = $client->selectDatabase($database)->selectCollection('flow_states');

            $doc = $collection->findOne(['phone' => $phone]);
            if ($doc) {
                return json_decode(json_encode($doc), true);
            }
            return null;
        } catch (Exception $e) {
            Log::error('getFlowState error: ' . $e->getMessage());
            return null;
        }
    }

    protected function deleteFlowState(string $phone): void
    {
        try {
            $url = config('mongodb.tracking_url');
            $database = config('mongodb.tracking_database', 'vanigan_tracking');
            if (empty($url)) return;

            $client = new \MongoDB\Client($url);
            $collection = $client->selectDatabase($database)->selectCollection('flow_states');
            $collection->deleteOne(['phone' => $phone]);
        } catch (Exception $e) {
            Log::error('deleteFlowState error: ' . $e->getMessage());
        }
    }

    /**
     * Build services array with icons from FlowImageService (base64 for flow).
     */
    protected function buildServicesWithIcons(array $services): array
    {
        $result = [];
        foreach ($services as $s) {
            $item = [
                'id' => $s['id'],
                'title' => $s['title'],
                'description' => $s['description'],
            ];
            $iconKey = $s['icon'] ?? '';
            if ($iconKey) {
                $url = $this->flowImages->getImageUrl($iconKey);
                if ($url) {
                    try {
                        // Use Cloudinary transformation for icon (120x120, high quality)
                        $thumbUrl = $url;
                        if (strpos($url, 'cloudinary.com') !== false && strpos($url, '/upload/') !== false) {
                            $thumbUrl = str_replace('/upload/', '/upload/w_120,h_120,c_fill,q_auto:best,f_png/', $url);
                        }
                        $imgData = @\file_get_contents($thumbUrl);
                        if ($imgData && strlen($imgData) < 100000) {
                            $item['image'] = base64_encode($imgData);
                        }
                    } catch (\Exception $e) {}
                }
            }
            $result[] = $item;
        }
        return $result;
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return substr($phone, 2);
        }
        
        return $phone;
    }
}
