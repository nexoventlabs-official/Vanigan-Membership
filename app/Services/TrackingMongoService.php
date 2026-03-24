<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Tracking MongoDB service — tracks incomplete registrations.
 * Users are tracked from the moment they enter their mobile number
 * through each step until card generation. Once card is generated,
 * the user is removed from this DB (they exist in the main DB).
 *
 * Steps: mobile_entered → otp_verified → epic_validated → photo_uploaded → pin_set → card_generated (removed)
 */
class TrackingMongoService
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
            Log::warning('TrackingMongoService: No tracking MongoDB URL configured. Tracking disabled.');
            return;
        }

        try {
            $this->client = new Client($url);
            $this->collection = $this->client->selectDatabase($this->database)->selectCollection('incomplete_registrations');
            $this->enabled = true;
        } catch (Exception $e) {
            Log::error('TrackingMongoService: Connection failed - ' . $e->getMessage());
        }
    }

    /**
     * Track a registration step for a mobile number.
     * Creates or updates the tracking document keyed by mobile.
     */
    public function trackStep(string $mobile, string $step, array $data = []): bool
    {
        if (!$this->enabled) return false;

        try {
            $now = now()->setTimezone('Asia/Kolkata')->toISOString();

            $updateData = [
                'mobile' => $mobile,
                'last_step' => $step,
                'last_activity' => $now,
            ];

            // Merge any extra data (name, epic_no, assembly, district, etc.)
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    $updateData[$key] = $value;
                }
            }

            // Build the step history entry
            $stepEntry = [
                'step' => $step,
                'timestamp' => $now,
            ];

            $this->collection->updateOne(
                ['mobile' => $mobile],
                [
                    '$set' => $updateData,
                    '$setOnInsert' => ['started_at' => $now],
                    '$push' => ['step_history' => $stepEntry],
                ],
                ['upsert' => true]
            );

            return true;
        } catch (Exception $e) {
            Log::error("TrackingMongoService::trackStep Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a user from tracking (called when card is successfully generated).
     */
    public function removeByMobile(string $mobile): bool
    {
        if (!$this->enabled) return false;

        try {
            $result = $this->collection->deleteOne(['mobile' => $mobile]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("TrackingMongoService::removeByMobile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all incomplete registrations (users who started but didn't generate card).
     * Supports pagination, search, and sorting.
     */
    public function getIncompleteRegistrations(int $page = 1, int $limit = 20, ?string $search = null, ?string $step = null, ?string $from = null, ?string $to = null): array
    {
        if (!$this->enabled) {
            return ['users' => [], 'total' => 0];
        }

        try {
            $filter = [];

            if ($search) {
                $regex = new \MongoDB\BSON\Regex(preg_quote($search, '/'), 'i');
                $filter['$or'] = [
                    ['mobile' => $regex],
                    ['name' => $regex],
                    ['epic_no' => $regex],
                ];
            }

            if ($step) {
                $filter['last_step'] = $step;
            }

            // Date filtering on started_at (stored as ISO string)
            if ($from || $to) {
                $dateFilter = [];
                if ($from) {
                    $dateFilter['$gte'] = $from . 'T00:00:00+05:30';
                }
                if ($to) {
                    $dateFilter['$lte'] = $to . 'T23:59:59+05:30';
                }
                $filter['started_at'] = $dateFilter;
            }

            $total = $this->collection->countDocuments($filter);

            $skip = ($page - 1) * $limit;
            $cursor = $this->collection->find($filter, [
                'sort' => ['last_activity' => -1],
                'skip' => $skip,
                'limit' => $limit,
            ]);

            $users = [];
            foreach ($cursor as $doc) {
                $u = json_decode(json_encode($doc), true);
                // Clean up MongoDB internals
                if (isset($u['_id']['$oid'])) {
                    $u['_id'] = $u['_id']['$oid'];
                }
                unset($u['step_history']); // Don't send full history to listing
                $users[] = $u;
            }

            return ['users' => $users, 'total' => $total];
        } catch (Exception $e) {
            Log::error("TrackingMongoService::getIncompleteRegistrations Error: " . $e->getMessage());
            return ['users' => [], 'total' => 0];
        }
    }

    /**
     * Get a single tracking record with full step history.
     */
    public function getByMobile(string $mobile): ?array
    {
        if (!$this->enabled) return null;

        try {
            $doc = $this->collection->findOne(['mobile' => $mobile]);
            if (!$doc) return null;

            $u = json_decode(json_encode($doc), true);
            if (isset($u['_id']['$oid'])) {
                $u['_id'] = $u['_id']['$oid'];
            }
            return $u;
        } catch (Exception $e) {
            Log::error("TrackingMongoService::getByMobile Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get stats: count by last_step.
     */
    public function getStepStats(): array
    {
        if (!$this->enabled) return [];

        try {
            $pipeline = [
                ['$group' => ['_id' => '$last_step', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
            ];

            $results = $this->collection->aggregate($pipeline)->toArray();
            $stats = [];
            foreach ($results as $doc) {
                $d = json_decode(json_encode($doc), true);
                $stats[$d['_id'] ?? 'unknown'] = $d['count'] ?? 0;
            }
            return $stats;
        } catch (Exception $e) {
            Log::error("TrackingMongoService::getStepStats Error: " . $e->getMessage());
            return [];
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
