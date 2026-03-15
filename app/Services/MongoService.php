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
     * Generate unique member ID: VNG-XXXXXXX
     */
    public function generateUniqueId(): string
    {
        return 'VNG-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
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
}
