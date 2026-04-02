<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected string $phoneNumberId;
    protected string $accessToken;
    protected string $apiVersion;
    protected string $apiBaseUrl;

    public function __construct()
    {
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken = config('whatsapp.access_token');
        $this->apiVersion = config('whatsapp.api_version', 'v18.0');
        $this->apiBaseUrl = config('whatsapp.api_base_url', 'https://graph.facebook.com');
    }

    public function sendTextMessage(string $to, string $message): array
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'preview_url' => true,
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$to}", ['response' => $response->json()]);
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("WhatsApp send failed to {$to}", ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error("WhatsAppService::sendTextMessage Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendWelcomeMessage(string $to, string $appUrl): array
    {
        $registrationLink = rtrim($appUrl, '/') . '?wa=' . $this->formatPhoneNumber($to);
        file_put_contents('/tmp/wa_debug.log', "CTA URL: $registrationLink\n", FILE_APPEND);
        
        return $this->sendInteractiveMessage($to, $registrationLink, 'welcome');
    }

    public function sendInteractiveMessage(string $to, string $link, string $type = 'welcome'): array
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            if ($type === 'welcome') {
                $headerText = "👋 Hello! Welcome!";
                $bodyText = "*Tamil Nadu Vanigargalin Sangamam*\n\n";
                $bodyText .= "🎫 Get Your Digital Member ID Card\n\n";
                $bodyText .= "Generate your official membership card instantly. Click the button below to start!";
                $buttonText = "Generate ID Card 🪪";
            } else {
                $headerText = "� Welcome Back!";
                $bodyText = "*Tamil Nadu Vanigargalin Sangamam*\n\n";
                $bodyText .= "🎫 View Your Digital Member ID Card\n\n";
                $bodyText .= "Your membership card is ready. Click below to view it!";
                $buttonText = "View My Card 🪪";
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'cta_url',
                    'header' => [
                        'type' => 'text',
                        'text' => $headerText,
                    ],
                    'body' => [
                        'text' => $bodyText,
                    ],
                    'footer' => [
                        'text' => '© Tamil Nadu Vanigargalin Sangamam',
                    ],
                    'action' => [
                        'name' => 'cta_url',
                        'parameters' => [
                            'display_text' => $buttonText,
                            'url' => $link,
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp interactive message sent to {$to}", ['response' => $response->json()]);
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("WhatsApp interactive send failed to {$to}", ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error("WhatsAppService::sendInteractiveMessage Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendExistingMemberMessage(string $to, string $appUrl, string $name = ''): array
    {
        $registrationLink = rtrim($appUrl, '/') . '?wa=' . $this->formatPhoneNumber($to);
        
        return $this->sendInteractiveMessage($to, $registrationLink, 'existing');
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return substr($phone, 2);
        }
        
        if (strlen($phone) === 10) {
            return $phone;
        }
        
        return $phone;
    }

    public function verifyWebhook(string $mode, string $token, string $challenge): ?string
    {
        $verifyToken = config('whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully');
            return $challenge;
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token_match' => $token === $verifyToken,
        ]);

        return null;
    }

    public function parseWebhookPayload(array $payload): ?array
    {
        try {
            if (!isset($payload['entry'][0]['changes'][0]['value'])) {
                return null;
            }

            $value = $payload['entry'][0]['changes'][0]['value'];

            if (!isset($value['messages'][0])) {
                return null;
            }

            $message = $value['messages'][0];
            $contact = $value['contacts'][0] ?? [];
            $type = $message['type'] ?? 'unknown';

            // Extract text from different message types
            $text = null;
            if ($type === 'text') {
                $text = $message['text']['body'] ?? null;
            } elseif ($type === 'interactive') {
                // Button reply or list reply
                $interactive = $message['interactive'] ?? [];
                $interactiveType = $interactive['type'] ?? '';
                if ($interactiveType === 'button_reply') {
                    $text = $interactive['button_reply']['id'] ?? $interactive['button_reply']['title'] ?? null;
                } elseif ($interactiveType === 'list_reply') {
                    $text = $interactive['list_reply']['id'] ?? $interactive['list_reply']['title'] ?? null;
                }
                // nfm_reply is handled separately in WhatsAppController
            }

            return [
                'from' => $message['from'] ?? null,
                'message_id' => $message['id'] ?? null,
                'timestamp' => $message['timestamp'] ?? null,
                'type' => $type,
                'text' => $text,
                'contact_name' => $contact['profile']['name'] ?? null,
            ];

        } catch (Exception $e) {
            Log::error("WhatsAppService::parseWebhookPayload Error: " . $e->getMessage());
            return null;
        }
    }

    public function markAsRead(string $messageId): bool
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'messaging_product' => 'whatsapp',
                'status' => 'read',
                'message_id' => $messageId,
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error("WhatsAppService::markAsRead Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send an image message with optional caption.
     */
    public function sendImageMessage(string $to, string $imageUrl, string $caption = ''): array
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'image',
                'image' => [
                    'link' => $imageUrl,
                    'caption' => $caption,
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp image sent to {$to}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("WhatsApp image send failed to {$to}", ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error("WhatsAppService::sendImageMessage Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a WhatsApp Flow message with image header and CTA flow button.
     * This is the native WhatsApp Flows integration (like the restaurant project).
     */
    public function sendFlowMessage(string $to, array $options): array
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $flowId = $options['flowId'] ?? '';
            $flowCta = $options['flowCta'] ?? 'Choose Service';
            $headerText = $options['headerText'] ?? 'Welcome';
            $headerImageUrl = $options['headerImageUrl'] ?? null;
            $bodyText = $options['bodyText'] ?? 'Select a service';
            $footerText = $options['footerText'] ?? null;
            $screenName = $options['screenName'] ?? 'SERVICE_SELECT';
            $screenData = $options['screenData'] ?? [];
            $flowToken = $options['flowToken'] ?? 'unused';
            $mode = $options['mode'] ?? 'published';
            $flowAction = $options['flowAction'] ?? 'navigate';

            // Build header
            $header = $headerImageUrl
                ? ['type' => 'image', 'image' => ['link' => $headerImageUrl]]
                : ['type' => 'text', 'text' => $headerText];

            // Build action parameters
            $actionParams = [
                'flow_message_version' => '3',
                'flow_token' => $flowToken,
                'flow_id' => $flowId,
                'flow_cta' => $flowCta,
                'mode' => $mode,
                'flow_action' => $flowAction,
            ];

            if ($flowAction === 'navigate') {
                $actionParams['flow_action_payload'] = [
                    'screen' => $screenName,
                    'data' => array_merge($screenData, ['flow_token' => $flowToken]),
                ];
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'flow',
                    'header' => $header,
                    'body' => ['text' => $bodyText],
                    'action' => [
                        'name' => 'flow',
                        'parameters' => $actionParams,
                    ],
                ],
            ];

            if ($footerText) {
                $payload['interactive']['footer'] = ['text' => $footerText];
            }

            Log::info('Sending WhatsApp Flow message', ['to' => $to, 'flowId' => $flowId, 'screen' => $screenName]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Flow message sent to {$to}", ['messageId' => $response->json()['messages'][0]['id'] ?? '']);
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("WhatsApp Flow send failed to {$to}", ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error("WhatsAppService::sendFlowMessage Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send interactive reply buttons (max 3 buttons).
     */
    public function sendButtons(string $to, string $bodyText, array $buttons, string $footerText = ''): array
    {
        try {
            $url = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'button',
                    'body' => ['text' => $bodyText],
                    'action' => [
                        'buttons' => array_map(fn($btn, $i) => [
                            'type' => 'reply',
                            'reply' => [
                                'id' => $btn['id'] ?? (string)($i + 1),
                                'title' => substr($btn['text'] ?? $btn['title'] ?? '', 0, 20),
                            ],
                        ], array_slice($buttons, 0, 3), array_keys(array_slice($buttons, 0, 3))),
                    ],
                ],
            ];

            if ($footerText) {
                $payload['interactive']['footer'] = ['text' => $footerText];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            Log::error("sendButtons failed", ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json()];
        } catch (Exception $e) {
            Log::error("sendButtons error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a CTA URL interactive message with optional image header.
     */
    public function sendCtaUrlMessage(string $to, string $bodyText, string $buttonText, string $url, ?string $imageUrl = null, ?string $footerText = null): array
    {
        try {
            $apiUrl = "{$this->apiBaseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $header = $imageUrl
                ? ['type' => 'image', 'image' => ['link' => $imageUrl]]
                : ['type' => 'text', 'text' => 'Tamil Nadu Vanigargalin Sangamam'];

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'cta_url',
                    'header' => $header,
                    'body' => ['text' => $bodyText],
                    'action' => [
                        'name' => 'cta_url',
                        'parameters' => [
                            'display_text' => $buttonText,
                            'url' => $url,
                        ],
                    ],
                ],
            ];

            if ($footerText) {
                $payload['interactive']['footer'] = ['text' => $footerText];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $payload);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error("WhatsAppService::sendCtaUrlMessage Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
