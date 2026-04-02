<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppMongoService
{
    protected ?Client $client = null;
    protected ?Collection $collection = null;
    protected string $database;
    protected bool $enabled = false;

    public function __construct()
    {
        $url = config('mongodb.tracking_url');
        $this->database = config('mongodb.tracking_database', 'vanigan_tracking');

        if (empty($url)) {
            Log::warning('WhatsAppMongoService: No tracking MongoDB URL configured.');
            return;
        }

        try {
            $this->client = new Client($url);
            $this->collection = $this->client->selectDatabase($this->database)->selectCollection('whatsapp_users');
            $this->enabled = true;
        } catch (Exception $e) {
            Log::error('WhatsAppMongoService: Connection failed - ' . $e->getMessage());
        }
    }

    public function trackWhatsAppUser(string $phone, string $contactName = null, string $message = null): bool
    {
        if (!$this->enabled) return false;

        try {
            $now = now()->setTimezone('Asia/Kolkata')->toISOString();
            $formattedPhone = $this->formatPhoneNumber($phone);

            $messageEntry = [
                'text' => $message,
                'direction' => 'incoming',
                'timestamp' => $now,
            ];

            $this->collection->updateOne(
                ['phone' => $formattedPhone],
                [
                    '$set' => [
                        'phone' => $formattedPhone,
                        'whatsapp_id' => $phone,
                        'contact_name' => $contactName,
                        'last_message' => $message,
                        'last_activity' => $now,
                    ],
                    '$setOnInsert' => [
                        'first_contact' => $now,
                        'status' => 'pending',
                    ],
                    '$push' => ['messages' => $messageEntry],
                    '$inc' => ['message_count' => 1],
                ],
                ['upsert' => true]
            );

            return true;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::trackWhatsAppUser Error: " . $e->getMessage());
            return false;
        }
    }

    public function trackOutgoingMessage(string $phone, string $message): bool
    {
        if (!$this->enabled) return false;

        try {
            $now = now()->setTimezone('Asia/Kolkata')->toISOString();
            $formattedPhone = $this->formatPhoneNumber($phone);

            $messageEntry = [
                'text' => $message,
                'direction' => 'outgoing',
                'timestamp' => $now,
            ];

            $this->collection->updateOne(
                ['phone' => $formattedPhone],
                [
                    '$set' => [
                        'last_activity' => $now,
                        'welcome_sent' => true,
                    ],
                    '$push' => ['messages' => $messageEntry],
                ]
            );

            return true;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::trackOutgoingMessage Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus(string $phone, string $status): bool
    {
        if (!$this->enabled) return false;

        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $now = now()->setTimezone('Asia/Kolkata')->toISOString();

            $this->collection->updateOne(
                ['phone' => $formattedPhone],
                [
                    '$set' => [
                        'status' => $status,
                        'status_updated_at' => $now,
                    ],
                ]
            );

            return true;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::updateStatus Error: " . $e->getMessage());
            return false;
        }
    }

    public function getWhatsAppUsers(int $page = 1, int $limit = 20, ?string $search = null, ?string $status = null): array
    {
        if (!$this->enabled) {
            return ['users' => [], 'total' => 0];
        }

        try {
            $filter = [];

            if ($search) {
                $regex = new \MongoDB\BSON\Regex(preg_quote($search, '/'), 'i');
                $filter['$or'] = [
                    ['phone' => $regex],
                    ['contact_name' => $regex],
                ];
            }

            if ($status && $status !== 'all') {
                $filter['status'] = $status;
            }

            $total = $this->collection->countDocuments($filter);

            $skip = ($page - 1) * $limit;
            $cursor = $this->collection->find($filter, [
                'sort' => ['last_activity' => -1],
                'skip' => $skip,
                'limit' => $limit,
                'projection' => ['messages' => 0],
            ]);

            $users = [];
            foreach ($cursor as $doc) {
                $u = json_decode(json_encode($doc), true);
                if (isset($u['_id']['$oid'])) {
                    $u['_id'] = $u['_id']['$oid'];
                }
                $users[] = $u;
            }

            return ['users' => $users, 'total' => $total];
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::getWhatsAppUsers Error: " . $e->getMessage());
            return ['users' => [], 'total' => 0];
        }
    }

    public function getByPhone(string $phone): ?array
    {
        if (!$this->enabled) return null;

        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $doc = $this->collection->findOne(['phone' => $formattedPhone]);
            
            if (!$doc) return null;

            $u = json_decode(json_encode($doc), true);
            if (isset($u['_id']['$oid'])) {
                $u['_id'] = $u['_id']['$oid'];
            }
            return $u;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::getByPhone Error: " . $e->getMessage());
            return null;
        }
    }

    public function removeByPhone(string $phone): bool
    {
        if (!$this->enabled) return false;

        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            $result = $this->collection->deleteOne(['phone' => $formattedPhone]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::removeByPhone Error: " . $e->getMessage());
            return false;
        }
    }

    public function getStats(): array
    {
        if (!$this->enabled) return [];

        try {
            $pipeline = [
                ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ];

            $results = $this->collection->aggregate($pipeline)->toArray();
            $stats = ['total' => 0];
            foreach ($results as $doc) {
                $d = json_decode(json_encode($doc), true);
                $status = $d['_id'] ?? 'unknown';
                $count = $d['count'] ?? 0;
                $stats[$status] = $count;
                $stats['total'] += $count;
            }
            return $stats;
        } catch (Exception $e) {
            Log::error("WhatsAppMongoService::getStats Error: " . $e->getMessage());
            return [];
        }
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return substr($phone, 2);
        }
        
        return $phone;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
