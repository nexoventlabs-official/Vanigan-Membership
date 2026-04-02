<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Manages WhatsApp Flow images stored in MongoDB.
 * Images are uploaded via admin panel and used in flow screens.
 */
class FlowImageService
{
    protected ?Client $client = null;
    protected ?Collection $collection = null;
    protected bool $enabled = false;

    public function __construct()
    {
        $url = config('mongodb.url');
        $database = config('mongodb.database', 'vanigan');

        if (empty($url)) return;

        try {
            $this->client = new Client($url);
            $this->collection = $this->client->selectDatabase($database)->selectCollection('flow_images');
            $this->enabled = true;
        } catch (Exception $e) {
            Log::error('FlowImageService: Connection failed - ' . $e->getMessage());
        }
    }

    public function getImageUrl(string $key): ?string
    {
        if (!$this->enabled) return null;

        try {
            $doc = $this->collection->findOne(['key' => $key]);
            if ($doc) {
                $arr = json_decode(json_encode($doc), true);
                return $arr['url'] ?? null;
            }
            return null;
        } catch (Exception $e) {
            Log::error("FlowImageService::getImageUrl Error: " . $e->getMessage());
            return null;
        }
    }

    public function setImage(string $key, string $url, string $label = ''): bool
    {
        if (!$this->enabled) return false;

        try {
            $this->collection->updateOne(
                ['key' => $key],
                ['$set' => [
                    'key' => $key,
                    'url' => $url,
                    'label' => $label,
                    'updated_at' => now()->toISOString(),
                ]],
                ['upsert' => true]
            );
            return true;
        } catch (Exception $e) {
            Log::error("FlowImageService::setImage Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteImage(string $key): bool
    {
        if (!$this->enabled) return false;

        try {
            $result = $this->collection->deleteOne(['key' => $key]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            Log::error("FlowImageService::deleteImage Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllImages(): array
    {
        if (!$this->enabled) return [];

        try {
            $cursor = $this->collection->find([], ['sort' => ['key' => 1]]);
            $images = [];
            foreach ($cursor as $doc) {
                $arr = json_decode(json_encode($doc), true);
                unset($arr['_id']);
                $images[] = $arr;
            }
            return $images;
        } catch (Exception $e) {
            Log::error("FlowImageService::getAllImages Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all required flow image keys with their labels.
     */
    public static function getRequiredKeys(): array
    {
        return [
            'flow_welcome_banner' => 'Welcome Flow Banner (1000x125)',
            'flow_register' => 'Register Option Icon',
            'flow_wings_list' => 'Wings List Option Icon',
            'flow_how_to_register' => 'How to Register Option Icon',
            'flow_website' => 'Website Option Icon',
            'flow_download' => 'Download Option Icon',
            'flow_help_support' => 'Help & Support Option Icon',
            'flow_view_card' => 'View Your Card Option Icon',
            'flow_refer_friend' => 'Refer a Friend Option Icon',
            'flow_become_organizer' => 'Become an Organizer Option Icon',
            'flow_your_members' => 'Your Members Option Icon',
            'flow_request_loan' => 'Request Loan Option Icon',
            'flow_loan_pvt_ltd' => 'Pvt Ltd Company Icon (Loan)',
            'flow_loan_partnership' => 'Partnership Business Icon (Loan)',
            'flow_loan_import_export' => 'Import Export Business Icon (Loan)',
            'welcome_image' => 'Welcome Message Image',
        ];
    }
}
