<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * WhatsApp Flow Service — creates, publishes, and manages WhatsApp Flows via Meta API.
 * Also builds the Flow JSON for the Vanigan service selection flow.
 */
class WhatsAppFlowService
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $wabaId;
    protected string $apiVersion;
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken = config('whatsapp.access_token');
        $this->wabaId = config('whatsapp.waba_id');
        $this->apiVersion = config('whatsapp.api_version', 'v18.0');
        $this->apiBaseUrl = config('whatsapp.api_base_url', 'https://graph.facebook.com');
    }

    /**
     * Create a new WhatsApp Flow.
     */
    public function createFlow(string $name, array $categories = ['OTHER']): array
    {
        $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->wabaId}/flows";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->post($url, [
            'name' => $name,
            'categories' => $categories,
        ]);

        if ($response->successful()) {
            Log::info("WhatsApp Flow created", ['data' => $response->json()]);
            return $response->json();
        }

        Log::error("WhatsApp Flow creation failed", ['response' => $response->json()]);
        throw new Exception('Flow creation failed: ' . json_encode($response->json()));
    }

    /**
     * Upload Flow JSON to a flow.
     */
    public function updateFlowJSON(string $flowId, array $flowJson): array
    {
        $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$flowId}/assets";

        $jsonStr = json_encode($flowJson);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->attach(
            'file', $jsonStr, 'flow.json', ['Content-Type' => 'application/json']
        )->post($url, [
            'name' => 'flow.json',
            'asset_type' => 'FLOW_JSON',
        ]);

        if ($response->successful()) {
            Log::info("Flow JSON uploaded", ['flowId' => $flowId]);
            return $response->json();
        }

        Log::error("Flow JSON upload failed", ['response' => $response->json()]);
        throw new Exception('Flow JSON upload failed: ' . json_encode($response->json()));
    }

    /**
     * Publish a flow.
     */
    public function publishFlow(string $flowId): array
    {
        $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$flowId}/publish";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->post($url);

        if ($response->successful()) {
            Log::info("Flow published", ['flowId' => $flowId]);
            return $response->json();
        }

        Log::error("Flow publish failed", ['response' => $response->json()]);
        throw new Exception('Flow publish failed: ' . json_encode($response->json()));
    }

    /**
     * Build the Vanigan Welcome Service Flow JSON.
     * This flow uses Endpoint (Data API) mode — WhatsApp calls our endpoint for data.
     */
    public function buildWelcomeFlowJSON(?string $bannerBase64 = null): array
    {
        // ─── Screen 1: Service Selection ───
        $screen1Children = [];

        if ($bannerBase64) {
            $screen1Children[] = [
                'type' => 'Image',
                'src' => $bannerBase64,
                'width' => 1000,
                'height' => 125,
                'scale-type' => 'cover',
                'alt-text' => 'Vanigan Welcome Banner',
            ];
        }

        $screen1Children[] = [
            'type' => 'TextBody',
            'text' => 'Choose from one of the Services',
        ];
        $screen1Children[] = [
            'type' => 'RadioButtonsGroup',
            'name' => 'selected_service',
            'label' => 'Select Service',
            'required' => true,
            'data-source' => '${data.services}',
        ];
        $screen1Children[] = [
            'type' => 'Footer',
            'label' => 'Confirm',
            'on-click-action' => [
                'name' => 'data_exchange',
                'payload' => [
                    'selected_service' => '${form.selected_service}',
                    'flow_token' => '${data.flow_token}',
                ],
            ],
        ];

        // ─── Screen 2: EPIC Entry (Register flow) ───
        $screenEpicChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'Enter your EPIC Number to find your voter details',
            ],
            [
                'type' => 'TextInput',
                'name' => 'epic_no',
                'label' => 'EPIC Number',
                'required' => true,
                'input-type' => 'text',
                'helper-text' => 'Found on your Voter ID card',
            ],
            [
                'type' => 'Footer',
                'label' => 'Continue',
                'on-click-action' => [
                    'name' => 'data_exchange',
                    'payload' => [
                        'selected_service' => 'register',
                        'epic_no' => '${form.epic_no}',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 3: Voter Details Confirmation ───
        $screenVoterConfirmChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'Voter Details Found',
            ],
            [
                'type' => 'TextBody',
                'text' => '${data.voter_info}',
            ],
            [
                'type' => 'Footer',
                'label' => 'Confirm Details',
                'on-click-action' => [
                    'name' => 'data_exchange',
                    'payload' => [
                        'selected_service' => 'register',
                        'action' => 'confirm_voter',
                        'epic_no' => '${data.epic_no}',
                        'voter_name' => '${data.voter_name}',
                        'assembly' => '${data.assembly}',
                        'district' => '${data.district}',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 4: PIN Setup ───
        $screenPinChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'Set a 4-digit PIN for your card',
            ],
            [
                'type' => 'TextInput',
                'name' => 'pin',
                'label' => 'Enter PIN',
                'required' => true,
                'input-type' => 'password',
                'min-chars' => 4,
                'max-chars' => 4,
            ],
            [
                'type' => 'TextInput',
                'name' => 'confirm_pin',
                'label' => 'Confirm PIN',
                'required' => true,
                'input-type' => 'password',
                'min-chars' => 4,
                'max-chars' => 4,
            ],
            [
                'type' => 'Footer',
                'label' => 'Set PIN',
                'on-click-action' => [
                    'name' => 'data_exchange',
                    'payload' => [
                        'selected_service' => 'register',
                        'action' => 'set_pin',
                        'pin' => '${form.pin}',
                        'confirm_pin' => '${form.confirm_pin}',
                        'epic_no' => '${data.epic_no}',
                        'voter_name' => '${data.voter_name}',
                        'assembly' => '${data.assembly}',
                        'district' => '${data.district}',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 5: Additional Details (DOB, Blood Group, Address) ───
        $screenDetailsChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'Fill Additional Details',
            ],
            [
                'type' => 'DatePicker',
                'name' => 'dob',
                'label' => 'Date of Birth',
                'required' => false,
                'helper-text' => 'Select your date of birth',
            ],
            [
                'type' => 'Dropdown',
                'name' => 'blood_group',
                'label' => 'Blood Group',
                'required' => false,
                'data-source' => [
                    ['id' => 'A+', 'title' => 'A+'],
                    ['id' => 'A-', 'title' => 'A-'],
                    ['id' => 'B+', 'title' => 'B+'],
                    ['id' => 'B-', 'title' => 'B-'],
                    ['id' => 'O+', 'title' => 'O+'],
                    ['id' => 'O-', 'title' => 'O-'],
                    ['id' => 'AB+', 'title' => 'AB+'],
                    ['id' => 'AB-', 'title' => 'AB-'],
                ],
            ],
            [
                'type' => 'TextArea',
                'name' => 'address',
                'label' => 'Address',
                'required' => false,
                'helper-text' => 'Enter your full address',
            ],
            [
                'type' => 'Footer',
                'label' => 'Generate Card',
                'on-click-action' => [
                    'name' => 'complete',
                    'payload' => [
                        'selected_service' => 'register',
                        'action' => 'generate_card',
                        'dob' => '${form.dob}',
                        'blood_group' => '${form.blood_group}',
                        'address' => '${form.address}',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 6: Wings List ───
        $screenWingsChildren = [
            [
                'type' => 'TextSubheading',
                'text' => '${data.wings_title}',
            ],
            [
                'type' => 'TextBody',
                'text' => '${data.wings_info}',
            ],
            [
                'type' => 'Footer',
                'label' => 'Close',
                'on-click-action' => [
                    'name' => 'complete',
                    'payload' => [
                        'selected_service' => 'wings_list',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 7: How to Register ───
        $screenHowToChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'How to Register',
            ],
            [
                'type' => 'TextBody',
                'text' => '${data.how_to_info}',
            ],
            [
                'type' => 'Footer',
                'label' => 'Close',
                'on-click-action' => [
                    'name' => 'complete',
                    'payload' => [
                        'selected_service' => 'how_to_register',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        // ─── Screen 8: Help & Support ───
        $screenHelpChildren = [
            [
                'type' => 'TextSubheading',
                'text' => 'Help & Support',
            ],
            [
                'type' => 'TextBody',
                'text' => '${data.help_info}',
            ],
            [
                'type' => 'Footer',
                'label' => 'Close',
                'on-click-action' => [
                    'name' => 'complete',
                    'payload' => [
                        'selected_service' => 'help_support',
                        'flow_token' => '${data.flow_token}',
                    ],
                ],
            ],
        ];

        return [
            'version' => '6.0',
            'data_api_version' => '3.0',
            'routing_model' => [
                'SERVICE_SELECT' => ['EPIC_ENTRY', 'WINGS_LIST', 'HOW_TO_REGISTER', 'HELP_SUPPORT'],
                'EPIC_ENTRY' => ['VOTER_CONFIRM'],
                'VOTER_CONFIRM' => ['PIN_SETUP'],
                'PIN_SETUP' => [],
                'ADDITIONAL_DETAILS' => [],
                'WINGS_LIST' => [],
                'HOW_TO_REGISTER' => [],
                'HELP_SUPPORT' => [],
            ],
            'screens' => [
                [
                    'id' => 'SERVICE_SELECT',
                    'title' => 'Service Selection',
                    'data' => [
                        'services' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'string'],
                                    'title' => ['type' => 'string'],
                                    'description' => ['type' => 'string'],
                                    'image' => ['type' => 'string'],
                                ],
                            ],
                            '__example__' => [
                                ['id' => 'register', 'title' => 'Register', 'description' => 'Register for membership'],
                            ],
                        ],
                        'flow_token' => [
                            'type' => 'string',
                            '__example__' => 'vanigan_919999999999',
                        ],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screen1Children,
                    ],
                ],
                [
                    'id' => 'EPIC_ENTRY',
                    'title' => 'Enter EPIC Number',
                    'data' => [
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenEpicChildren,
                    ],
                ],
                [
                    'id' => 'VOTER_CONFIRM',
                    'title' => 'Confirm Details',
                    'data' => [
                        'voter_info' => ['type' => 'string', '__example__' => 'Name: John\nEPIC: ABC123'],
                        'voter_name' => ['type' => 'string', '__example__' => 'John'],
                        'epic_no' => ['type' => 'string', '__example__' => 'ABC123'],
                        'assembly' => ['type' => 'string', '__example__' => 'Chennai'],
                        'district' => ['type' => 'string', '__example__' => 'Chennai'],
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenVoterConfirmChildren,
                    ],
                ],
                [
                    'id' => 'PIN_SETUP',
                    'title' => 'Set PIN',
                    'terminal' => true,
                    'success' => true,
                    'data' => [
                        'epic_no' => ['type' => 'string', '__example__' => 'ABC123'],
                        'voter_name' => ['type' => 'string', '__example__' => 'John'],
                        'assembly' => ['type' => 'string', '__example__' => 'Chennai'],
                        'district' => ['type' => 'string', '__example__' => 'Chennai'],
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenPinChildren,
                    ],
                ],
                [
                    'id' => 'ADDITIONAL_DETAILS',
                    'title' => 'Additional Details',
                    'terminal' => true,
                    'success' => true,
                    'data' => [
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenDetailsChildren,
                    ],
                ],
                [
                    'id' => 'WINGS_LIST',
                    'title' => 'Wings List',
                    'terminal' => true,
                    'success' => true,
                    'data' => [
                        'wings_title' => ['type' => 'string', '__example__' => 'Our Wings'],
                        'wings_info' => ['type' => 'string', '__example__' => 'Wing details...'],
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenWingsChildren,
                    ],
                ],
                [
                    'id' => 'HOW_TO_REGISTER',
                    'title' => 'How to Register',
                    'terminal' => true,
                    'success' => true,
                    'data' => [
                        'how_to_info' => ['type' => 'string', '__example__' => 'Steps to register...'],
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenHowToChildren,
                    ],
                ],
                [
                    'id' => 'HELP_SUPPORT',
                    'title' => 'Help & Support',
                    'terminal' => true,
                    'success' => true,
                    'data' => [
                        'help_info' => ['type' => 'string', '__example__' => 'Contact us...'],
                        'flow_token' => ['type' => 'string', '__example__' => 'vanigan_919999999999'],
                    ],
                    'layout' => [
                        'type' => 'SingleColumnLayout',
                        'children' => $screenHelpChildren,
                    ],
                ],
            ],
        ];
    }

    /**
     * Convert an image URL to raw base64 (no data: prefix) for flow JSON embedding.
     */
    public function imageUrlToBase64(string $url): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);
            if ($response->successful()) {
                return base64_encode($response->body());
            }
            return null;
        } catch (Exception $e) {
            Log::error("imageUrlToBase64 failed: " . $e->getMessage());
            return null;
        }
    }
}
