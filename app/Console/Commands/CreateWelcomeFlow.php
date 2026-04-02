<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateWelcomeFlow extends Command
{
    protected $signature = 'whatsapp:create-flow';
    protected $description = 'Create and publish the Vanigan dynamic WhatsApp Flow';

    public function handle()
    {
        $token = config('whatsapp.access_token');
        $wabaId = config('whatsapp.waba_id');
        $appUrl = config('app.url');
        $api = 'v21.0';
        if (!$token || !$wabaId) { $this->error('Missing credentials'); return 1; }

        $endpointUri = rtrim($appUrl, '/') . '/webhook/flow';
        $flowName = 'Vanigan Flow ' . time();

        $this->info("Creating: {$flowName}");
        $res = Http::withToken($token)->post("https://graph.facebook.com/{$api}/{$wabaId}/flows", [
            'name' => $flowName, 'categories' => ['OTHER'], 'endpoint_uri' => $endpointUri,
        ]);
        if (!$res->successful()) { $this->error($res->body()); return 1; }
        $flowId = $res->json('id');
        $this->info("Created: {$flowId}");

        $this->info('Uploading JSON...');
        // Fetch banner image and embed as base64
        $bannerBase64 = null;
        try {
            $fi = app(\App\Services\FlowImageService::class);
            $bannerUrl = $fi->getImageUrl('flow_welcome_banner');
            if ($bannerUrl) {
                // Use Cloudinary transform for optimal size (1000x125)
                if (strpos($bannerUrl, 'cloudinary.com') !== false) {
                    $bannerUrl = str_replace('/upload/', '/upload/w_1000,h_125,c_fill,g_center,q_auto,f_jpg/', $bannerUrl);
                }
                $imgData = @file_get_contents($bannerUrl);
                if ($imgData) {
                    $bannerBase64 = base64_encode($imgData);
                    $this->info("Banner: " . strlen($imgData) . " bytes");
                }
            }
        } catch (\Exception $e) {
            $this->warn("No banner: " . $e->getMessage());
        }

        $flowJson = $this->buildFlowJSON($bannerBase64);
        $tmp = tempnam(sys_get_temp_dir(), 'flow_');
        file_put_contents($tmp, json_encode($flowJson));
        $res = Http::withToken($token)
            ->attach('file', file_get_contents($tmp), 'flow.json', ['Content-Type' => 'application/json'])
            ->post("https://graph.facebook.com/{$api}/{$flowId}/assets", ['name' => 'flow.json', 'asset_type' => 'FLOW_JSON']);
        unlink($tmp);
        $this->info('Warnings: ' . json_encode($res->json('validation_errors') ?? []));

        $this->info('Publishing...');
        $res = Http::withToken($token)->post("https://graph.facebook.com/{$api}/{$flowId}/publish");
        if ($res->successful() && $res->json('success')) {
            $this->info("PUBLISHED! WHATSAPP_WELCOME_FLOW_ID={$flowId}");
        } else {
            $this->warn('Publish: ' . $res->body());
            $this->info("DRAFT. WHATSAPP_WELCOME_FLOW_ID={$flowId}");
        }
        return 0;
    }

    protected function buildFlowJSON(?string $bannerBase64 = null): array
    {
        $ex = ['type' => 'string', '__example__' => 'x'];
        $exArr = fn($e) => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => ['id' => ['type' => 'string'], 'title' => ['type' => 'string'], 'description' => ['type' => 'string'], 'image' => ['type' => 'string']]], '__example__' => [$e]];

        return [
            'version' => '6.0',
            'data_api_version' => '3.0',
            'routing_model' => [
                'SERVICE_SELECT' => ['EPIC_ENTRY', 'WINGS_LIST', 'HOW_TO_REGISTER', 'HELP_SUPPORT', 'YOUR_MEMBERS', 'ORGANIZER'],
                'EPIC_ENTRY' => ['VOTER_CONFIRM'],
                'VOTER_CONFIRM' => ['DOB_SELECT'],
                'DOB_SELECT' => ['BLOOD_GROUP'],
                'BLOOD_GROUP' => ['ADDRESS_ENTRY'],
                'ADDRESS_ENTRY' => [],
                'WINGS_LIST' => [],
                'HOW_TO_REGISTER' => [],
                'HELP_SUPPORT' => [],
                'YOUR_MEMBERS' => ['MEMBER_DETAIL'],
                'MEMBER_DETAIL' => [],
                'ORGANIZER' => [],
            ],
            'screens' => [
                // 1. Service Selection
                ['id' => 'SERVICE_SELECT', 'title' => 'Choose Service',
                    'data' => ['services' => $exArr(['id' => 'register', 'title' => 'Register', 'description' => 'Register']), 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => array_filter([
                        $bannerBase64 ? ['type' => 'Image', 'src' => $bannerBase64, 'width' => 1000, 'height' => 125, 'scale-type' => 'cover', 'alt-text' => 'Vanigan Banner'] : null,
                        ['type' => 'TextBody', 'text' => 'Choose from one of the Services'],
                        ['type' => 'RadioButtonsGroup', 'name' => 'selected_service', 'label' => 'Select Service', 'required' => true, 'data-source' => '${data.services}'],
                        ['type' => 'Footer', 'label' => 'Confirm', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['selected_service' => '${form.selected_service}', 'flow_token' => '${data.flow_token}']]],
                    ])]],
                // 2. EPIC Entry
                ['id' => 'EPIC_ENTRY', 'title' => 'Enter EPIC Number',
                    'data' => ['flow_token' => $ex, 'error_msg' => $ex, 'has_error' => ['type' => 'boolean', '__example__' => false]],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Enter your EPIC Number'],
                        ['type' => 'TextBody', 'text' => '${data.error_msg}', 'visible' => '${data.has_error}'],
                        ['type' => 'TextInput', 'name' => 'epic_no', 'label' => 'EPIC Number', 'required' => true, 'input-type' => 'text', 'helper-text' => 'Found on your Voter ID card'],
                        ['type' => 'Footer', 'label' => 'Search', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'EPIC_ENTRY', 'epic_no' => '${form.epic_no}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 3. Voter Confirm
                ['id' => 'VOTER_CONFIRM', 'title' => 'Confirm Details',
                    'data' => ['voter_info' => $ex, 'epic_no' => $ex, 'voter_name' => $ex, 'assembly' => $ex, 'district' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Voter Details Found'],
                        ['type' => 'TextBody', 'text' => '${data.voter_info}'],
                        ['type' => 'Footer', 'label' => 'Confirm & Continue', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'VOTER_CONFIRM', 'epic_no' => '${data.epic_no}', 'voter_name' => '${data.voter_name}', 'assembly' => '${data.assembly}', 'district' => '${data.district}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 4. DOB
                ['id' => 'DOB_SELECT', 'title' => 'Date of Birth',
                    'data' => ['epic_no' => $ex, 'voter_name' => $ex, 'assembly' => $ex, 'district' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Select your Date of Birth'],
                        ['type' => 'DatePicker', 'name' => 'dob', 'label' => 'Date of Birth', 'required' => true, 'helper-text' => 'Tap to select'],
                        ['type' => 'Footer', 'label' => 'Continue', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'DOB_SELECT', 'dob' => '${form.dob}', 'epic_no' => '${data.epic_no}', 'voter_name' => '${data.voter_name}', 'assembly' => '${data.assembly}', 'district' => '${data.district}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 5. Blood Group
                ['id' => 'BLOOD_GROUP', 'title' => 'Blood Group',
                    'data' => ['epic_no' => $ex, 'voter_name' => $ex, 'assembly' => $ex, 'district' => $ex, 'dob' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Select your Blood Group'],
                        ['type' => 'Dropdown', 'name' => 'blood_group', 'label' => 'Blood Group', 'required' => true, 'data-source' => [['id'=>'A+','title'=>'A+'],['id'=>'A-','title'=>'A-'],['id'=>'B+','title'=>'B+'],['id'=>'B-','title'=>'B-'],['id'=>'O+','title'=>'O+'],['id'=>'O-','title'=>'O-'],['id'=>'AB+','title'=>'AB+'],['id'=>'AB-','title'=>'AB-']]],
                        ['type' => 'Footer', 'label' => 'Continue', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'BLOOD_GROUP', 'blood_group' => '${form.blood_group}', 'dob' => '${data.dob}', 'epic_no' => '${data.epic_no}', 'voter_name' => '${data.voter_name}', 'assembly' => '${data.assembly}', 'district' => '${data.district}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 6. Address
                ['id' => 'ADDRESS_ENTRY', 'title' => 'Enter Address', 'terminal' => true, 'success' => true,
                    'data' => ['epic_no' => $ex, 'voter_name' => $ex, 'assembly' => $ex, 'district' => $ex, 'dob' => $ex, 'blood_group' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Enter your Address'],
                        ['type' => 'TextArea', 'name' => 'address', 'label' => 'Full Address', 'required' => true, 'max-length' => 100],
                        ['type' => 'Footer', 'label' => 'Get Your Card', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'ADDRESS_ENTRY', 'address' => '${form.address}', 'blood_group' => '${data.blood_group}', 'dob' => '${data.dob}', 'epic_no' => '${data.epic_no}', 'voter_name' => '${data.voter_name}', 'assembly' => '${data.assembly}', 'district' => '${data.district}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 7. Wings List
                ['id' => 'WINGS_LIST', 'title' => 'Wings List', 'terminal' => true, 'success' => true,
                    'data' => ['wings_title' => $ex, 'wings_info' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => '${data.wings_title}'],
                        ['type' => 'TextBody', 'text' => '${data.wings_info}'],
                        ['type' => 'Footer', 'label' => 'Close', 'on-click-action' => ['name' => 'complete', 'payload' => ['selected_service' => 'wings_list', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 8. How to Register
                ['id' => 'HOW_TO_REGISTER', 'title' => 'How to Register', 'terminal' => true, 'success' => true,
                    'data' => ['how_to_info' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'How to Register'],
                        ['type' => 'TextBody', 'text' => '${data.how_to_info}'],
                        ['type' => 'Footer', 'label' => 'Close', 'on-click-action' => ['name' => 'complete', 'payload' => ['selected_service' => 'how_to_register', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 9. Help
                ['id' => 'HELP_SUPPORT', 'title' => 'Help & Support', 'terminal' => true, 'success' => true,
                    'data' => ['help_info' => $ex, 'flow_token' => $ex],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Help & Support'],
                        ['type' => 'TextBody', 'text' => '${data.help_info}'],
                        ['type' => 'Footer', 'label' => 'Close', 'on-click-action' => ['name' => 'complete', 'payload' => ['selected_service' => 'help_support', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 10. Your Members (list with photos like restaurant menu)
                ['id' => 'YOUR_MEMBERS', 'title' => 'Your Members',
                    'data' => [
                        'members' => $exArr(['id' => 'TNVS-001', 'title' => 'John', 'description' => 'Chennai', 'image' => 'base64...']),
                        'members_count' => $ex,
                        'flow_token' => $ex,
                    ],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Your Referred Members'],
                        ['type' => 'TextBody', 'text' => '${data.members_count}'],
                        ['type' => 'RadioButtonsGroup', 'name' => 'selected_member', 'label' => 'Select a Member', 'required' => true, 'data-source' => '${data.members}'],
                        ['type' => 'Footer', 'label' => 'View Details', 'on-click-action' => ['name' => 'data_exchange', 'payload' => ['screen' => 'YOUR_MEMBERS', 'selected_member' => '${form.selected_member}', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 11. Member Detail
                ['id' => 'MEMBER_DETAIL', 'title' => 'Member Details', 'terminal' => true, 'success' => true,
                    'data' => [
                        'member_photo' => $ex,
                        'member_info' => $ex,
                        'flow_token' => $ex,
                    ],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'Image', 'src' => '${data.member_photo}', 'width' => 200, 'height' => 200, 'scale-type' => 'contain', 'alt-text' => 'Member Photo'],
                        ['type' => 'TextBody', 'text' => '${data.member_info}'],
                        ['type' => 'Footer', 'label' => 'Close', 'on-click-action' => ['name' => 'complete', 'payload' => ['selected_service' => 'member_viewed', 'flow_token' => '${data.flow_token}']]],
                    ]]],
                // 12. Become Organizer (progress bar + referral link)
                ['id' => 'ORGANIZER', 'title' => 'Become an Organizer', 'terminal' => true, 'success' => true,
                    'data' => [
                        'org_status' => $ex,
                        'progress_bar' => $ex,
                        'referral_info' => $ex,
                        'referral_link' => $ex,
                        'flow_token' => $ex,
                    ],
                    'layout' => ['type' => 'SingleColumnLayout', 'children' => [
                        ['type' => 'TextSubheading', 'text' => 'Become an Organizer'],
                        ['type' => 'TextBody', 'text' => '${data.org_status}'],
                        ['type' => 'TextBody', 'text' => '${data.progress_bar}'],
                        ['type' => 'TextBody', 'text' => '${data.referral_info}'],
                        ['type' => 'TextInput', 'name' => 'ref_link', 'label' => 'Your Referral Link (long press to copy)', 'required' => false, 'input-type' => 'text', 'init-value' => '${data.referral_link}', 'helper-text' => 'Long press to select & copy'],
                        ['type' => 'Footer', 'label' => 'Close', 'on-click-action' => ['name' => 'complete', 'payload' => ['selected_service' => 'organizer_viewed', 'flow_token' => '${data.flow_token}']]],
                    ]]],
            ],
        ];
    }
}
