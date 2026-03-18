<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\BSONArray;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * MongoDB service — uses the official mongodb/mongodb PHP library
 * with the ext-mongodb PHP extension.
 */
class MongoService
{
    protected Collection $collection;

    public function __construct()
    {
        $url      = env('MONGO_URL', 'mongodb://localhost:27017');
        $database = env('MONGO_DB_NAME', 'vanigan');

        $client = new Client($url);
        $this->collection = $client->selectDatabase($database)->selectCollection('members');
    }

    /**
     * Convert BSON document/array to plain PHP array recursively.
     */
    protected function toArray($doc): ?array
    {
        if ($doc === null) return null;
        $arr = json_decode(json_encode($doc), true);
        // Remove MongoDB internal _id field
        unset($arr['_id']);
        return $arr;
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
     * Create or update a member document keyed on epic_no.
     */
    public function upsertMember(string $epicNo, array $data): ?array
    {
        try {
            $data['epic_no']    = strtoupper($epicNo);
            $data['updated_at'] = now()->toISOString();

            $this->collection->updateOne(
                ['epic_no' => strtoupper($epicNo)],
                [
                    '$set'         => $data,
                    '$setOnInsert' => ['created_at' => now()->toISOString()],
                ],
                ['upsert' => true]
            );

            return $this->findMemberByEpic($epicNo);
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
     * Generate unique member ID: TNVS-XXXXXX
     */
    public function generateUniqueId(): string
    {
        return 'TNVS-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
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
                'sort' => ['created_at' => -1],
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
                ['sort' => ['created_at' => -1]]
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
     */
    public function getStats(): array
    {
        try {
            $totalMembers = $this->collection->countDocuments();
            $detailsCompleted = $this->collection->countDocuments(['details_completed' => true]);

            $todayStart = now()->startOfDay()->toISOString();
            $weekStart = now()->startOfWeek()->toISOString();
            $monthStart = now()->startOfMonth()->toISOString();

            $membersToday = $this->collection->countDocuments(['created_at' => ['$gte' => $todayStart]]);
            $membersThisWeek = $this->collection->countDocuments(['created_at' => ['$gte' => $weekStart]]);
            $membersThisMonth = $this->collection->countDocuments(['created_at' => ['$gte' => $monthStart]]);

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

            // Recent 10
            $recentMembers = [];
            foreach ($this->collection->find([], ['sort' => ['created_at' => -1], 'limit' => 10]) as $doc) {
                $m = $this->toArray($doc);
                if ($m) { unset($m['pin_hash']); $recentMembers[] = $m; }
            }

            $completionRate = $totalMembers > 0 ? round(($detailsCompleted / $totalMembers) * 100, 1) : 0;

            return compact(
                'totalMembers', 'detailsCompleted', 'completionRate', 'cardsUploaded',
                'totalReferrals', 'membersToday', 'membersThisWeek', 'membersThisMonth',
                'topReferrers', 'assemblyStats', 'districtStats', 'recentMembers'
            );
        } catch (Exception $e) {
            Log::error("MongoService::getStats Exception: " . $e->getMessage());
            return [
                'totalMembers' => 0, 'detailsCompleted' => 0, 'completionRate' => 0,
                'cardsUploaded' => 0, 'totalReferrals' => 0, 'membersToday' => 0,
                'membersThisWeek' => 0, 'membersThisMonth' => 0,
                'topReferrers' => [], 'assemblyStats' => [], 'districtStats' => [],
                'recentMembers' => [],
            ];
        }
    }

    /**
     * Get distinct values for a field (for filter dropdowns).
     */
    public function getDistinctValues(string $field): array
    {
        try {
            $values = $this->collection->distinct($field);
            $result = array_filter(array_map('strval', $values), fn($v) => !empty($v));
            sort($result);
            return $result;
        } catch (Exception $e) {
            Log::error("MongoService::getDistinctValues Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Store a loan request in the loan_requests collection.
     */
    public function storeLoanRequest(array $data): void
    {
        try {
            $db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
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
            $db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
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
            $db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
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
     * Delete loan request by unique_id.
     */
    public function deleteLoanRequestByUniqueId(string $uniqueId): bool
    {
        try {
            $db = $this->client->selectDatabase(config('database.connections.mongodb.database'));
            $loanRequestsCollection = $db->selectCollection('loan_requests');
            $result = $loanRequestsCollection->deleteOne(['unique_id' => $uniqueId]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("MongoService::deleteLoanRequestByUniqueId Exception: " . $e->getMessage());
            return false;
        }
    }
}
