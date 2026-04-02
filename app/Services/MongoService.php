<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\BSONArray;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * MongoDB service — uses the official mongodb/mongodb PHP library
 * with the ext-mongodb PHP extension.
 */
class MongoService
{
    protected Client $client;
    protected Collection $collection;
    protected string $database;

    public function __construct()
    {
        $url      = config('mongodb.url');
        $this->database = config('mongodb.database');

        $this->client = new Client($url);
        $this->collection = $this->client->selectDatabase($this->database)->selectCollection('members');
    }

    /**
     * Convert BSON document/array to plain PHP array recursively.
     */
    protected function toArray($doc): ?array
    {
        if ($doc === null) return null;

        // Convert BSON to array
        $arr = json_decode(json_encode($doc), true);

        // Recursively convert MongoDB JSON format to proper values
        $arr = $this->recursiveConvert($arr);

        // Remove MongoDB internal _id field
        unset($arr['_id']);
        return $arr;
    }

    /**
     * Recursively convert MongoDB-specific JSON formats to safe values.
     */
    protected function recursiveConvert($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        // Check for MongoDB date format: {"$date": {"$numberLong": "..."}}
        if (isset($value['$date'])) {
            if (is_array($value['$date']) && isset($value['$date']['$numberLong'])) {
                $timestamp = (int)($value['$date']['$numberLong'] / 1000);
                return date('Y-m-d H:i:s', $timestamp);
            }
            if (is_string($value['$date'])) {
                return $value['$date'];
            }
            return (string)$value['$date'];
        }

        // Check for MongoDB ObjectId format: {"$oid": "..."}
        if (isset($value['$oid'])) {
            return (string)$value['$oid'];
        }

        // Recursively process array elements
        foreach ($value as $key => $item) {
            $value[$key] = $this->recursiveConvert($item);
        }

        return $value;
    }

    /* ── public helpers ────────────────────────────────────── */

    public function findMemberByMobile(string $mobile): ?array
    {
        $doc = $this->collection->findOne(['mobile' => $mobile]);
        return $this->toArray($doc);
    }

    public function findMemberByEpic(string $epicNo): ?array
    {
        $doc = $this->collection->findOne(['epic_no' => strtoupper($epicNo)]);
        return $this->toArray($doc);
    }

    public function findMemberByUniqueId(string $uniqueId): ?array
    {
        $doc = $this->collection->findOne(['unique_id' => $uniqueId]);
        return $this->toArray($doc);
    }

    /**
     * Create or update a member document keyed on mobile number.
     * This allows multiple members with the same EPIC but different mobiles.
     */
    public function upsertMember(string $epicNo, array $data): ?array
    {
        try {
            $data['epic_no']    = strtoupper($epicNo);
            $data['updated_at'] = now()->toISOString();

            // Key by mobile instead of epic_no to allow duplicate EPICs
            if (empty($data['mobile'])) {
                Log::error("MongoService::upsertMember - mobile is required");
                return null;
            }

            // Remove created_at from $data if it exists (to avoid conflict)
            $createdAt = $data['created_at'] ?? now()->toISOString();
            unset($data['created_at']);

            $this->collection->updateOne(
                ['mobile' => $data['mobile']],
                [
                    '$set'         => $data,
                    '$setOnInsert' => ['created_at' => $createdAt],
                ],
                ['upsert' => true]
            );

            return $this->findMemberByMobile($data['mobile']);
        } catch (Exception $e) {
            Log::error("MongoService::upsertMember Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update additional details for a member.
     */
    public function updateMemberDetails(string $epicNo, array $details): bool
    {
        try {
            $details['updated_at']        = now()->toISOString();
            $details['details_completed'] = true;

            $result = $this->collection->updateOne(
                ['epic_no' => strtoupper($epicNo)],
                ['$set' => $details]
            );

            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateMemberDetails Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update additional details for a member by unique_id (not epic_no).
     * Prevents wrong member from being updated when duplicate EPICs exist.
     *
     * @param string $uniqueId The member's unique_id (TNVS-XXXXXX)
     * @param array $details The details to update (dob, age, blood_group, address, etc.)
     * @return bool True if update successful, false otherwise
     */
    public function updateMemberDetailsByUniqueId(string $uniqueId, array $details): bool
    {
        try {
            $details['updated_at']        = now()->toISOString();
            $details['details_completed'] = true;

            $result = $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => $details]
            );

            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateMemberDetailsByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique member ID: TNVS-XXXXXXXX
     * Uses 4 random bytes (8 hex chars) with collision check loop.
     */
    public function generateUniqueId(): string
    {
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $id = 'TNVS-' . strtoupper(bin2hex(random_bytes(4)));
            if (!$this->findMemberByUniqueId($id)) {
                return $id;
            }
            Log::warning("MongoService::generateUniqueId collision on attempt {$i}: {$id}");
        }
        // Fallback: 6 bytes (12 hex chars) - virtually impossible to collide
        return 'TNVS-' . strtoupper(bin2hex(random_bytes(6)));
    }

    /**
     * Delete all members from the collection.
     */
    public function deleteAllMembers(): ?array
    {
        try {
            $result = $this->collection->deleteMany([]);
            return ['deletedCount' => $result->getDeletedCount()];
        } catch (Exception $e) {
            Log::error("MongoService::deleteAllMembers Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update card image URLs for a member.
     */
    public function updateCardUrls(string $uniqueId, string $frontUrl, string $backUrl): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => [
                    'card_front_url' => $frontUrl,
                    'card_back_url'  => $backUrl,
                    'updated_at'     => now()->toISOString(),
                ]]
            );
            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateCardUrls Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or create a referral ID for a member.
     */
    public function getOrCreateReferralId(string $uniqueId): ?string
    {
        try {
            $member = $this->findMemberByUniqueId($uniqueId);
            if (!$member) return null;

            if (!empty($member['referral_id'])) {
                return $member['referral_id'];
            }

            $referralId = 'REF-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => [
                    'referral_id' => $referralId,
                    'referral_count' => 0,
                    'updated_at' => now()->toISOString(),
                ]]
            );
            return $referralId;
        } catch (Exception $e) {
            Log::error("MongoService::getOrCreateReferralId Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Increment referral count for a member.
     */
    public function incrementReferralCount(string $referrerId): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['unique_id' => $referrerId],
                ['$inc' => ['referral_count' => 1], '$set' => ['updated_at' => now()->toISOString()]]
            );
            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::incrementReferralCount Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync referral_count field with actual count of referred members.
     */
    public function syncReferralCount(string $uniqueId, int $actualCount): bool
    {
        try {
            $result = $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => ['referral_count' => $actualCount, 'updated_at' => now()->toISOString()]]
            );
            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::syncReferralCount Exception: " . $e->getMessage());
            return false;
        }
    }

    /* ── Admin panel helpers ────────────────────────────────── */

    /**
     * Get paginated members list with optional search/filter.
     */
    public function getAllMembers(int $page = 1, int $limit = 20, ?string $search = null, ?string $assembly = null, ?string $district = null): array
    {
        try {
            $filter = [];

            if ($search) {
                $regex = new \MongoDB\BSON\Regex(preg_quote($search, '/'), 'i');
                $filter['$or'] = [
                    ['name' => $regex],
                    ['epic_no' => $regex],
                    ['mobile' => $regex],
                    ['unique_id' => $regex],
                ];
            }

            if ($assembly) {
                $filter['assembly'] = new \MongoDB\BSON\Regex(preg_quote($assembly, '/'), 'i');
            }

            if ($district) {
                $filter['district'] = new \MongoDB\BSON\Regex(preg_quote($district, '/'), 'i');
            }

            $total = $this->collection->countDocuments($filter);
            $skip = ($page - 1) * $limit;

            $cursor = $this->collection->find($filter, [
                'sort' => ['_id' => -1],
                'skip' => $skip,
                'limit' => $limit,
            ]);

            $members = [];
            foreach ($cursor as $doc) {
                $member = $this->toArray($doc);
                if ($member) {
                    unset($member['pin_hash']);
                    $members[] = $member;
                }
            }

            return [
                'members' => $members,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => (int) ceil($total / max($limit, 1)),
            ];
        } catch (Exception $e) {
            Log::error("MongoService::getAllMembers Exception: " . $e->getMessage());
            return ['members' => [], 'total' => 0, 'page' => $page, 'limit' => $limit, 'pages' => 0];
        }
    }

    /**
     * Get members referred by a specific member.
     */
    public function getMembersReferredBy(string $uniqueId): array
    {
        try {
            $cursor = $this->collection->find(
                ['referred_by' => $uniqueId],
                ['sort' => ['_id' => -1]]
            );

            $members = [];
            foreach ($cursor as $doc) {
                $member = $this->toArray($doc);
                if ($member) {
                    unset($member['pin_hash']);
                    $members[] = $member;
                }
            }

            return $members;
        } catch (Exception $e) {
            Log::error("MongoService::getMembersReferredBy Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get dashboard statistics via aggregation.
     * Results are cached for 5 minutes to reduce MongoDB load.
     */
    public function getStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 300, function () {
            return $this->fetchStatsFromDb();
        });
    }

    /**
     * Fetch stats directly from MongoDB (called by getStats via cache).
     */
    protected function fetchStatsFromDb(): array
    {
        try {
            $totalMembers = $this->collection->countDocuments();
            $detailsCompleted = $this->collection->countDocuments(['details_completed' => true]);
            $pendingMembers = $totalMembers - $detailsCompleted;

            // Loan requests count
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $totalLoanRequests = $loanRequestsCollection->countDocuments();

            // Use _id ObjectId for date filtering (created_at may be stored inconsistently)
            $todayOid = new \MongoDB\BSON\ObjectId(dechex(now()->startOfDay()->timestamp) . '0000000000000000');
            $weekOid = new \MongoDB\BSON\ObjectId(dechex(now()->startOfWeek()->timestamp) . '0000000000000000');
            $monthOid = new \MongoDB\BSON\ObjectId(dechex(now()->startOfMonth()->timestamp) . '0000000000000000');

            $membersToday = $this->collection->countDocuments(['_id' => ['$gte' => $todayOid]]);
            $membersThisWeek = $this->collection->countDocuments(['_id' => ['$gte' => $weekOid]]);
            $membersThisMonth = $this->collection->countDocuments(['_id' => ['$gte' => $monthOid]]);

            $cardsUploaded = $this->collection->countDocuments([
                'card_front_url' => ['$exists' => true, '$ne' => ''],
                'card_back_url' => ['$exists' => true, '$ne' => ''],
            ]);

            // Total referrals
            $aggResult = $this->collection->aggregate([
                ['$group' => ['_id' => null, 'total' => ['$sum' => ['$ifNull' => ['$referral_count', 0]]]]],
            ])->toArray();
            $totalReferrals = !empty($aggResult) ? (int)($aggResult[0]['total'] ?? 0) : 0;

            // Top 10 referrers
            $topReferrers = array_map(
                fn($doc) => json_decode(json_encode($doc), true),
                $this->collection->aggregate([
                    ['$match' => ['referral_count' => ['$gt' => 0]]],
                    ['$sort' => ['referral_count' => -1]],
                    ['$limit' => 10],
                    ['$project' => ['_id' => 0, 'unique_id' => 1, 'name' => 1, 'mobile' => 1, 'assembly' => 1, 'referral_count' => 1]],
                ])->toArray()
            );

            // Members by assembly (top 10)
            $assemblyStats = array_map(
                fn($doc) => ['assembly' => json_decode(json_encode($doc), true)['_id'] ?? 'Unknown', 'count' => json_decode(json_encode($doc), true)['count'] ?? 0],
                $this->collection->aggregate([
                    ['$group' => ['_id' => '$assembly', 'count' => ['$sum' => 1]]],
                    ['$sort' => ['count' => -1]],
                    ['$limit' => 10],
                ])->toArray()
            );

            // Members by district (top 10)
            $districtStats = array_map(
                fn($doc) => ['district' => json_decode(json_encode($doc), true)['_id'] ?? 'Unknown', 'count' => json_decode(json_encode($doc), true)['count'] ?? 0],
                $this->collection->aggregate([
                    ['$group' => ['_id' => '$district', 'count' => ['$sum' => 1]]],
                    ['$sort' => ['count' => -1]],
                    ['$limit' => 10],
                ])->toArray()
            );

            // Recent 10 (sort by _id desc since created_at may be stored inconsistently)
            $recentMembers = [];
            foreach ($this->collection->find([], ['sort' => ['_id' => -1], 'limit' => 10]) as $doc) {
                $m = $this->toArray($doc);
                if ($m) { unset($m['pin_hash']); $recentMembers[] = $m; }
            }

            $completionRate = $totalMembers > 0 ? round(($detailsCompleted / $totalMembers) * 100, 1) : 0;

            return compact(
                'totalMembers', 'detailsCompleted', 'pendingMembers', 'completionRate', 'cardsUploaded',
                'totalReferrals', 'totalLoanRequests', 'membersToday', 'membersThisWeek', 'membersThisMonth',
                'topReferrers', 'assemblyStats', 'districtStats', 'recentMembers'
            );
        } catch (Exception $e) {
            Log::error("MongoService::getStats Exception: " . $e->getMessage());
            return [
                'totalMembers' => 0, 'detailsCompleted' => 0, 'pendingMembers' => 0, 'completionRate' => 0,
                'cardsUploaded' => 0, 'totalReferrals' => 0, 'totalLoanRequests' => 0, 'membersToday' => 0,
                'membersThisWeek' => 0, 'membersThisMonth' => 0,
                'topReferrers' => [], 'assemblyStats' => [], 'districtStats' => [],
                'recentMembers' => [],
            ];
        }
    }

    /**
     * Get distinct values for a field (for filter dropdowns).
     * Cached for 10 minutes.
     */
    public function getDistinctValues(string $field): array
    {
        return Cache::remember("distinct_values_{$field}", 600, function () use ($field) {
            try {
                $values = $this->collection->distinct($field);
                $result = array_filter(array_map('strval', $values), fn($v) => !empty($v));
                sort($result);
                return $result;
            } catch (Exception $e) {
                Log::error("MongoService::getDistinctValues Exception: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Store a loan request in the loan_requests collection.
     */
    public function storeLoanRequest(array $data): void
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $loanRequestsCollection->insertOne($data);
        } catch (Exception $e) {
            Log::error("MongoService::storeLoanRequest Exception: " . $e->getMessage());
        }
    }

    /**
     * Get loan request by unique_id.
     */
    public function getLoanRequestByUniqueId(string $uniqueId): ?array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $doc = $loanRequestsCollection->findOne(['unique_id' => $uniqueId]);
            if ($doc) {
                return $this->toArray($doc);
            }
            return null;
        } catch (Exception $e) {
            Log::error("MongoService::getLoanRequestByUniqueId Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get loan request by mobile number.
     */
    public function getLoanRequestByMobile(string $mobile): ?array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $doc = $loanRequestsCollection->findOne(['mobile' => $mobile]);
            if ($doc) {
                return $this->toArray($doc);
            }
            return null;
        } catch (Exception $e) {
            Log::error("MongoService::getLoanRequestByMobile Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete member by unique_id.
     */
    public function deleteMemberByUniqueId(string $uniqueId): bool
    {
        try {
            $result = $this->collection->deleteOne(['unique_id' => $uniqueId]);
            Log::info("Deleted member: $uniqueId, deletedCount: " . $result->getDeletedCount());
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::deleteMemberByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete manual entry by unique_id.
     */
    public function deleteManualEntryByUniqueId(string $uniqueId): bool
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $manualCollection = $db->selectCollection('manual_entries');
            $result = $manualCollection->deleteOne(['unique_id' => $uniqueId]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::deleteManualEntryByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update member fields by unique_id (admin edit).
     */
    public function updateMemberFieldsByUniqueId(string $uniqueId, array $fields): bool
    {
        try {
            $fields['updated_at'] = now()->toISOString();

            $result = $this->collection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => $fields]
            );

            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::updateMemberFieldsByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete loan request by unique_id.
     */
    public function deleteLoanRequestByUniqueId(string $uniqueId): bool
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $result = $loanRequestsCollection->deleteOne(['unique_id' => $uniqueId]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::deleteLoanRequestByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all loan requests with optional date range filtering.
     */
    public function getAllLoanRequests(?string $from = null, ?string $to = null): array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $loanRequestsCollection = $db->selectCollection('loan_requests');

            $filter = [];

            if ($from && $to) {
                $filter['created_at'] = [
                    '$gte' => $from . 'T00:00:00',
                    '$lte' => $to . 'T23:59:59',
                ];
            }

            $total = $loanRequestsCollection->countDocuments($filter);
            $cursor = $loanRequestsCollection->find($filter, ['sort' => ['created_at' => -1]]);

            $requests = [];
            $uniqueIds = [];
            foreach ($cursor as $doc) {
                $r = $this->toArray($doc);
                if ($r) {
                    $requests[] = $r;
                    if (!empty($r['unique_id'])) $uniqueIds[] = $r['unique_id'];
                }
            }

            // Resolve member photos and names in bulk
            if (!empty($uniqueIds)) {
                $memberCursor = $this->collection->find(
                    ['unique_id' => ['$in' => $uniqueIds]],
                    ['projection' => ['unique_id' => 1, 'photo_url' => 1, 'name' => 1]]
                );
                $memberMap = [];
                foreach ($memberCursor as $m) {
                    $ma = $this->toArray($m);
                    if ($ma && !empty($ma['unique_id'])) {
                        $memberMap[$ma['unique_id']] = [
                            'photo_url' => $ma['photo_url'] ?? '',
                            'name' => $ma['name'] ?? '',
                        ];
                    }
                }
                foreach ($requests as &$req) {
                    $uid = $req['unique_id'] ?? '';
                    $req['photo_url'] = $memberMap[$uid]['photo_url'] ?? '';
                    // Set member_name from members collection (fallback to loan request's name field)
                    $req['member_name'] = $memberMap[$uid]['name'] ?? $req['name'] ?? '';
                }
                unset($req);
            }

            return ['requests' => $requests, 'total' => $total];
        } catch (Exception $e) {
            Log::error("MongoService::getAllLoanRequests Exception: " . $e->getMessage());
            return ['requests' => [], 'total' => 0];
        }
    }

    /**
     * Get members for reports filtered by date range.
     * Uses ObjectId timestamps for reliable date filtering.
     * Returns members with referrer info resolved.
     */
    public function getReportMembers(string $from, string $to, ?string $assembly = null, ?string $district = null, ?string $zone = null): array
    {
        try {
            $fromTs = strtotime($from . ' 00:00:00');
            $toTs = strtotime($to . ' 23:59:59');

            if (!$fromTs || !$toTs) {
                Log::error("MongoService::getReportMembers - Invalid date range: {$from} to {$to}");
                return ['members' => [], 'total' => 0, 'referral_count' => 0];
            }

            $fromOid = new \MongoDB\BSON\ObjectId(dechex($fromTs) . '0000000000000000');
            $toOid = new \MongoDB\BSON\ObjectId(dechex($toTs) . '0000000000000000');

            $filter = ['_id' => ['$gte' => $fromOid, '$lte' => $toOid]];

            if ($assembly) {
                $filter['assembly'] = new \MongoDB\BSON\Regex(preg_quote($assembly, '/'), 'i');
            }

            if ($district) {
                $filter['district'] = new \MongoDB\BSON\Regex(preg_quote($district, '/'), 'i');
            }

            if ($zone) {
                $filter['zone'] = new \MongoDB\BSON\Regex(preg_quote($zone, '/'), 'i');
            }

            $total = $this->collection->countDocuments($filter);
            $referralCount = $this->collection->countDocuments(array_merge($filter, [
                'referred_by' => ['$exists' => true, '$ne' => ''],
            ]));

            $cursor = $this->collection->find($filter, [
                'sort' => ['_id' => -1],
            ]);

            $members = [];
            // Collect all referred_by unique_ids to resolve referrer names in bulk
            $referrerIds = [];

            foreach ($cursor as $doc) {
                $m = $this->toArray($doc);
                if ($m) {
                    unset($m['pin_hash']);
                    $members[] = $m;
                    if (!empty($m['referred_by'])) {
                        $referrerIds[$m['referred_by']] = true;
                    }
                }
            }

            // Resolve referrer names
            $referrerNames = [];
            foreach (array_keys($referrerIds) as $refId) {
                $referrer = $this->findMemberByUniqueId($refId);
                if ($referrer) {
                    $referrerNames[$refId] = $referrer['name'] ?? 'Unknown';
                }
            }

            // Batch-fetch all referrals: one query instead of N individual queries
            $memberUniqueIds = array_filter(array_column($members, 'unique_id'));
            $referralMap = []; // unique_id => [referred_unique_id, ...]
            if (!empty($memberUniqueIds)) {
                try {
                    $referralCursor = $this->collection->find(
                        ['referred_by' => ['$in' => array_values($memberUniqueIds)]],
                        ['projection' => ['unique_id' => 1, 'referred_by' => 1]]
                    );
                    foreach ($referralCursor as $refDoc) {
                        $rd = $this->toArray($refDoc);
                        if (!empty($rd['referred_by']) && !empty($rd['unique_id'])) {
                            $referralMap[$rd['referred_by']][] = $rd['unique_id'];
                        }
                    }
                } catch (Exception $e) {
                    Log::warning("MongoService::getReportMembers batch referral lookup failed: " . $e->getMessage());
                }
            }

            // Attach referrer name + referred members list to each member
            foreach ($members as &$m) {
                $m['referrer_name'] = '';
                if (!empty($m['referred_by']) && isset($referrerNames[$m['referred_by']])) {
                    $m['referrer_name'] = $referrerNames[$m['referred_by']];
                }

                $m['referred_member_ids'] = [];
                $m['referral_count'] = 0;
                if (!empty($m['unique_id']) && isset($referralMap[$m['unique_id']])) {
                    $m['referred_member_ids'] = $referralMap[$m['unique_id']];
                    $m['referral_count'] = count($referralMap[$m['unique_id']]);
                }
            }
            unset($m);

            // Sum of all referral counts across members
            $totalReferralCount = array_sum(array_column($members, 'referral_count'));

            return [
                'members' => $members,
                'total' => $total,
                'referral_count' => $referralCount,
                'total_referral_count' => $totalReferralCount,
            ];
        } catch (Exception $e) {
            Log::error("MongoService::getReportMembers Exception: " . $e->getMessage());
            return ['members' => [], 'total' => 0, 'referral_count' => 0];
        }
    }

    /**
     * Find all members with duplicate EPIC numbers.
     * Returns array of EPICs that have multiple registrations with different mobiles.
     */
    public function findDuplicateEpics(): array
    {
        try {
            $pipeline = [
                ['$group' => [
                    '_id' => '$epic_no',
                    'count' => ['$sum' => 1],
                    'members' => ['$push' => [
                        'mobile' => '$mobile',
                        'name' => '$name',
                        'unique_id' => '$unique_id',
                        'created_at' => '$created_at'
                    ]]
                ]],
                ['$match' => ['count' => ['$gt' => 1]]],
                ['$sort' => ['count' => -1]]
            ];
            
            $results = $this->collection->aggregate($pipeline)->toArray();
            return array_map(fn($doc) => json_decode(json_encode($doc), true), $results);
        } catch (Exception $e) {
            Log::error("MongoService::findDuplicateEpics Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Find all members with a specific EPIC number.
     * Returns array of all members (useful for finding duplicates).
     */
    public function findAllMembersByEpic(string $epicNo): array
    {
        try {
            $cursor = $this->collection->find(
                ['epic_no' => strtoupper($epicNo)],
                ['sort' => ['_id' => -1]]
            );

            $members = [];
            foreach ($cursor as $doc) {
                $member = $this->toArray($doc);
                if ($member) {
                    unset($member['pin_hash']);
                    $members[] = $member;
                }
            }

            return $members;
        } catch (Exception $e) {
            Log::error("MongoService::findAllMembersByEpic Exception: " . $e->getMessage());
            return [];
        }
    }

    /* ═══════════════════════════════════════════════════════════════════════════════
     * MANUAL ENTRIES COLLECTION
     * ---------------------------------------------------------------------------
     * This collection stores users who registered with manually entered voter data
     * (when their EPIC number was not found in the MySQL voters database).
     *
     * Schema:
     * {
     *   unique_id: string,       // TNVS-XXXXXX format
     *   epic_no: string,         // MANUAL_XXXXXXXXX (pseudo-EPIC for manual entries)
     *   mobile: string,          // 10-digit mobile number
     *   name: string,            // User-provided name
     *   assembly: string,        // User-provided assembly/taluk
     *   district: string,        // Optional district (empty for manual entries)
     *   photo_url: string,       // Cloudinary photo URL
     *   manually_entered: true,  // Flag indicating manual entry
     *   verified_by_admin: bool, // Whether admin has verified this entry
     *   created_at: datetime,
     *   updated_at: datetime
     * }
     *
     * WHY ISOLATED:
     * - Separates verified voter data from manually entered data
     * - Allows admin review workflow for manual entries
     * - Does NOT conflict with the main 'members' collection which contains
     *   auto-verified voter records from MySQL lookup
     * - Easy to query, audit, and manage unverified users separately
     * ═══════════════════════════════════════════════════════════════════════════════ */

    /**
     * Store a manually entered user in the manual_entries collection.
     * Called when EPIC lookup fails and user provides data manually.
     */
    public function storeManualEntry(array $data): ?array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $manualCollection = $db->selectCollection('manual_entries');

            $data['manually_entered'] = true;
            $data['verified_by_admin'] = false;
            $data['updated_at'] = now()->toISOString();

            // Check if mobile already exists in manual entries
            $existing = $manualCollection->findOne(['mobile' => $data['mobile']]);
            if ($existing) {
                // Update existing entry (preserve original created_at)
                unset($data['created_at']);
                $manualCollection->updateOne(
                    ['mobile' => $data['mobile']],
                    ['$set' => $data]
                );
            } else {
                // Insert new entry
                $data['created_at'] = now()->toISOString();
                $manualCollection->insertOne($data);
            }

            Log::info("Manual entry stored: " . json_encode(['mobile' => $data['mobile'], 'name' => $data['name'] ?? '']));
            return $data;
        } catch (Exception $e) {
            Log::error("MongoService::storeManualEntry Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a manual entry by mobile number.
     */
    public function findManualEntryByMobile(string $mobile): ?array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $manualCollection = $db->selectCollection('manual_entries');
            $doc = $manualCollection->findOne(['mobile' => $mobile]);
            return $this->toArray($doc);
        } catch (Exception $e) {
            Log::error("MongoService::findManualEntryByMobile Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all manual entries (for admin review).
     */
    public function getAllManualEntries(int $page = 1, int $limit = 20, bool $unverifiedOnly = false): array
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $manualCollection = $db->selectCollection('manual_entries');

            $filter = [];
            if ($unverifiedOnly) {
                $filter['verified_by_admin'] = false;
            }

            $total = $manualCollection->countDocuments($filter);
            $skip = ($page - 1) * $limit;

            $cursor = $manualCollection->find($filter, [
                'sort' => ['_id' => -1],
                'skip' => $skip,
                'limit' => $limit,
            ]);

            $entries = [];
            foreach ($cursor as $doc) {
                $entry = $this->toArray($doc);
                if ($entry) {
                    unset($entry['pin_hash']);
                    $entries[] = $entry;
                }
            }

            return [
                'entries' => $entries,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => (int) ceil($total / max($limit, 1)),
            ];
        } catch (Exception $e) {
            Log::error("MongoService::getAllManualEntries Exception: " . $e->getMessage());
            return ['entries' => [], 'total' => 0, 'page' => $page, 'limit' => $limit, 'pages' => 0];
        }
    }

    /**
     * Mark a manual entry as verified by admin.
     */
    public function verifyManualEntry(string $uniqueId): bool
    {
        try {
            $db = $this->client->selectDatabase($this->database);
            $manualCollection = $db->selectCollection('manual_entries');
            $result = $manualCollection->updateOne(
                ['unique_id' => $uniqueId],
                ['$set' => ['verified_by_admin' => true, 'updated_at' => now()->toISOString()]]
            );
            return $result->getMatchedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::verifyManualEntry Exception: " . $e->getMessage());
            return false;
        }
    }
}
