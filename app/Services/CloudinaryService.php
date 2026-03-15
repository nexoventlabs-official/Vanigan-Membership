<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudinaryService
{
    /**
     * Upload photo to Cloudinary
     */
    public function uploadPhoto($filePath, $epicNo)
    {
        try {
            $result = Cloudinary::upload($filePath, [
                'folder' => 'member_photos',
                'public_id' => $epicNo,
                'overwrite' => true,
                'invalidate' => true,
                'resource_type' => 'auto'
            ]);

            if (!isset($result['secure_url'])) {
                throw new Exception('No URL in Cloudinary response');
            }

            Log::info("Photo uploaded: {$result['secure_url']}");
            return $result['secure_url'];

        } catch (Exception $e) {
            Log::error("Photo upload failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Upload card to Cloudinary
     */
    public function uploadCard($filePath, $epicNo)
    {
        try {
            $result = Cloudinary::upload($filePath, [
                'folder' => 'generated_cards',
                'public_id' => $epicNo . '_' . time(),
                'overwrite' => true,
                'invalidate' => true,
                'resource_type' => 'auto'
            ]);

            if (!isset($result['secure_url'])) {
                throw new Exception('No URL in Cloudinary response');
            }

            Log::info("Card uploaded: {$result['secure_url']}");
            return $result['secure_url'];

        } catch (Exception $e) {
            Log::error("Card upload failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download photo from Cloudinary URL
     */
    public function downloadPhoto($photoUrl)
    {
        try {
            $tempPath = storage_path('app/temp/photo_' . uniqid() . '.jpg');
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            $photoContent = file_get_contents($photoUrl);
            if ($photoContent === false) {
                throw new Exception('Failed to download photo');
            }

            file_put_contents($tempPath, $photoContent);
            return $tempPath;

        } catch (Exception $e) {
            Log::error("Photo download failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete file from Cloudinary
     */
    public function deleteFile($publicId)
    {
        try {
            Cloudinary::destroy($publicId);
            Log::info("File deleted from Cloudinary: {$publicId}");
            return true;

        } catch (Exception $e) {
            Log::error("Cloudinary delete failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Cloudinary usage stats
     */
    public function getUsageStats()
    {
        try {
            $usage = Cloudinary::api()->usage();
            return $usage;

        } catch (Exception $e) {
            Log::error("Cloudinary usage stats failed: " . $e->getMessage());
            return null;
        }
    }
}
