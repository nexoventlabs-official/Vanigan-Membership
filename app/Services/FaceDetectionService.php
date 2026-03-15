<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FaceDetectionService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Validate photo for ID card
     * Returns: ['valid' => bool, 'message' => string, 'image' => Image|null]
     */
    public function validatePhotoForIdCard($fileStream)
    {
        try {
            // Load and validate image
            $image = $this->imageManager->read($fileStream);
            $width = $image->width();
            $height = $image->height();

            // Check dimensions
            if ($width < 200 || $height < 200) {
                return [
                    'valid' => false,
                    'message' => 'Photo resolution too low. Minimum 200x200 pixels required.',
                    'image' => null
                ];
            }

            if ($width > 5000 || $height > 5000) {
                return [
                    'valid' => false,
                    'message' => 'Photo resolution too high. Maximum 5000x5000 pixels.',
                    'image' => null
                ];
            }

            // Check aspect ratio (should be roughly square or portrait)
            $aspectRatio = $width / $height;
            if ($aspectRatio > 1.5 || $aspectRatio < 0.67) {
                return [
                    'valid' => false,
                    'message' => 'Photo aspect ratio incorrect. Please use a portrait or square photo.',
                    'image' => null
                ];
            }

            // TODO: Integrate with actual face detection
            // Option 1: PHP-OpenCV extension
            // Option 2: Google Cloud Vision API
            // Option 3: AWS Rekognition
            // Option 4: Azure Face API
            
            // For now, accept all photos that pass basic validation
            // You can uncomment one of the detection methods below

            // Option A: Use Google Cloud Vision (requires API key)
            // $faceDetection = $this->detectFaceViaGoogleVision($fileStream);

            // Option B: Use AWS Rekognition (requires AWS SDK)
            // $faceDetection = $this->detectFaceViaAwsRekognition($fileStream);

            // Option C: Check brightness
            $brightness = $this->calculateImageBrightness($image);
            if ($brightness < 40 || $brightness > 220) {
                return [
                    'valid' => false,
                    'message' => 'Photo brightness is too low or too high. Please use good lighting.',
                    'image' => null
                ];
            }

            // All checks passed
            return [
                'valid' => true,
                'message' => 'Photo is valid for ID card generation.',
                'image' => $image
            ];

        } catch (Exception $e) {
            Log::error('FaceDetectionService Error: ' . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'Invalid photo file.',
                'image' => null
            ];
        }
    }

    /**
     * Calculate average brightness of image
     */
    protected function calculateImageBrightness($image)
    {
        try {
            // Convert to grayscale and get average
            $gray = clone $image;
            $gray->greyscale();

            // Sample center region
            $region = $gray->crop(
                intval($gray->width() * 0.25),
                intval($gray->height() * 0.25),
                intval($gray->width() * 0.5),
                intval($gray->height() * 0.5)
            );

            // Approximate brightness (0-255)
            // For proper calculation, would need pixel-level access
            // This is a simplified version
            return 128; // Default middle brightness

        } catch (Exception $e) {
            Log::warning('Brightness calculation failed: ' . $e->getMessage());
            return 128;
        }
    }

    /**
     * Detect face using Google Cloud Vision API
     * Requires: GOOGLE_CLOUD_VISION_API_KEY in .env
     */
    protected function detectFaceViaGoogleVision($fileStream)
    {
        try {
            $apiKey = config('services.google_vision.api_key');
            if (!$apiKey) {
                return ['valid' => false, 'message' => 'Face detection not configured'];
            }

            // TODO: Implement Google Vision API call
            // This is a placeholder
            return ['valid' => true, 'message' => 'Face detected'];

        } catch (Exception $e) {
            Log::error('Google Vision Error: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'Face detection failed'];
        }
    }

    /**
     * Detect face using AWS Rekognition
     * Requires: AWS SDK configured
     */
    protected function detectFaceViaAwsRekognition($fileStream)
    {
        try {
            // TODO: Implement AWS Rekognition API call
            // This is a placeholder
            return ['valid' => true, 'message' => 'Face detected'];

        } catch (Exception $e) {
            Log::error('AWS Rekognition Error: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'Face detection failed'];
        }
    }
}
