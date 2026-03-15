<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Voter ID Card Generation Service v2.0
 * Matches Python implementation exactly
 */
class CardGenerationService
{
    protected $imageManager;

    const TEMPLATE_WIDTH = 1004;
    const TEMPLATE_HEIGHT = 650;
    const FONT_SIZE = 30;
    const FONT_MIN_SIZE = 14;
    const FONT_COLOR = '#000000';

    const NAME_COORDS = [175, 189, 694, 520];
    const VOTER_ID_COORDS = [200, 243, 694, 495];
    const ASSEMBLY_COORDS = [230, 298, 694, 465];
    const DISTRICT_COORDS = [178, 351, 694, 517];

    const PHOTO_BOX = [65, 402, 244, 599];  // Adjusted: moved 5px inward from all sides
    const PHOTO_BORDER_WIDTH = 3;
    const PHOTO_BORDER_RADIUS = 24;

    const QR_BOX = [338, 418, 468, 548];
    const QR_WHITE_BOX = [318, 390, 486, 613];
    const QR_BG_COLOR = [229, 232, 237];
    const PTC_CODE_XY = [403, 555];
    const PTC_CODE_FONT_SIZE = 18;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function generateCard($voterData, $photoPath, $epicNo)
    {
        try {
            Log::info("Starting card generation for EPIC: {$epicNo}");
            Log::info("Photo path received: {$photoPath}");
            Log::info("Voter data: " . json_encode($voterData));
            
            $templatePath = config('app.template_path');
            if (!$templatePath || !file_exists($templatePath)) {
                throw new Exception('Card template not found');
            }

            $card = $this->imageManager->read($templatePath);
            $voterData = $this->sanitizeVoterData($voterData);

            $this->drawTextField($card, $voterData['voter_name'] ?? '', self::NAME_COORDS);
            $this->drawTextField($card, $voterData['epic_no'] ?? '', self::VOTER_ID_COORDS);
            $this->drawTextField($card, $voterData['assembly_name'] ?? '', self::ASSEMBLY_COORDS);
            $this->drawTextField($card, $voterData['district'] ?? '', self::DISTRICT_COORDS);

            if ($photoPath && file_exists($photoPath)) {
                Log::info("Photo file exists, calling pastePhoto");
                $this->pastePhoto($card, $photoPath);
            } else {
                Log::warning("Photo not found or path empty. PhotoPath: {$photoPath}");
            }

            $this->fillQrBackground($card);
            $this->pasteQrCode($card, $epicNo);

            if (isset($voterData['ptc_code']) && !empty($voterData['ptc_code'])) {
                Log::info("Drawing PTC code: " . $voterData['ptc_code']);
                $this->drawPtcCode($card, $voterData['ptc_code']);
            } else {
                Log::warning("No PTC code found in voter data");
            }

            // Save to public storage for web access
            $publicPath = storage_path('app/public/generated-cards/card_' . $epicNo . '_' . time() . '.jpg');
            if (!is_dir(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0755, true);
            }

            $card->toJpeg(95)->save($publicPath);

            if (!file_exists($publicPath)) {
                throw new Exception('Failed to save card image');
            }

            Log::info("Card generated successfully: {$publicPath}");
            return $publicPath;

        } catch (Exception $e) {
            Log::error("CardGenerationService Error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function sanitizeVoterData($data)
    {
        $sanitized = [];
        $maxLengths = [
            'voter_name' => 100,
            'epic_no' => 20,
            'assembly_name' => 100,
            'district' => 100,
            'ptc_code' => 50
        ];

        foreach ($data as $key => $value) {
            if (!is_string($value)) {
                $value = (string) $value;
            }
            $value = preg_replace('/[^\P{C}\s]/u', '', $value);
            $value = str_replace(['{', '}', '<', '>', '\\'], '', $value);
            $maxLen = $maxLengths[$key] ?? 100;
            $value = mb_substr($value, 0, $maxLen);
            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    protected function drawTextField($card, $text, $coords)
    {
        if (empty($text)) {
            return;
        }

        [$labelEndX, $y, $fieldEndX, $maxWidth] = $coords;
        $text = mb_strtoupper($text);
        $fontSize = $this->autoFitFontSize($text, $maxWidth, self::FONT_SIZE);
        $textWidth = $this->getTextWidth($text, $fontSize);
        $centerX = $labelEndX + (int)(($fieldEndX - $labelEndX - $textWidth) / 2);
        $fontPath = $this->getFontPath();

        $card->text($text, $centerX, $y, function (FontFactory $font) use ($fontSize, $fontPath) {
            if ($fontPath) {
                $font->file($fontPath);
            }
            $font->size($fontSize);
            $font->color(self::FONT_COLOR);
            $font->align('left');
            $font->valign('top');
        });
    }

    protected function autoFitFontSize($text, $maxWidth, $initialSize = 30)
    {
        $size = $initialSize;
        while ($size > self::FONT_MIN_SIZE) {
            $textWidth = $this->getTextWidth($text, $size);
            if ($textWidth <= $maxWidth) {
                break;
            }
            $size--;
        }
        return max($size, self::FONT_MIN_SIZE);
    }

    protected function getTextWidth($text, $fontSize)
    {
        return mb_strlen($text) * ($fontSize * 0.6);
    }

    protected function pastePhoto($card, $photoPath)
    {
        try {
            Log::info("Attempting to paste photo from: {$photoPath}");
            
            if (!file_exists($photoPath)) {
                Log::warning("Photo file does not exist: {$photoPath}");
                return;
            }
            
            Log::info("Photo file exists, size: " . filesize($photoPath) . " bytes");
            
            $photo = $this->imageManager->read($photoPath);
            $boxWidth = self::PHOTO_BOX[2] - self::PHOTO_BOX[0];   // 189
            $boxHeight = self::PHOTO_BOX[3] - self::PHOTO_BOX[1];  // 207
            $radius = self::PHOTO_BORDER_RADIUS;  // 24
            
            Log::info("Photo dimensions before resize: " . $photo->width() . "x" . $photo->height());
            
            // Crop-to-fill (matches Python)
            $photo->cover($boxWidth, $boxHeight);
            
            Log::info("Photo dimensions after resize: " . $photo->width() . "x" . $photo->height());

            // Create rounded corners using GD directly
            $photoWithRoundedCorners = $this->applyRoundedCorners($photo, $boxWidth, $boxHeight, $radius);
            
            // Place photo with rounded corners on card
            $card->place($photoWithRoundedCorners, 'top-left', self::PHOTO_BOX[0], self::PHOTO_BOX[1]);
            
            Log::info("Photo pasted successfully");
            
        } catch (Exception $e) {
            Log::error("Failed to paste photo: " . $e->getMessage());
            Log::error("Photo path was: {$photoPath}");
        }
    }

    /**
     * Apply rounded corners to photo using GD (matches Python PIL implementation)
     */
    protected function applyRoundedCorners($photo, $width, $height, $radius)
    {
        try {
            // Save photo to temp file
            $tempPhotoPath = storage_path('app/temp/photo_' . uniqid() . '.png');
            $photo->toPng()->save($tempPhotoPath);
            
            // Use GD directly for rounded corners with alpha transparency
            $source = imagecreatefrompng($tempPhotoPath);
            
            // Create output image with alpha channel
            $output = imagecreatetruecolor($width, $height);
            imagealphablending($output, false);
            imagesavealpha($output, true);
            
            // Fill with transparent background
            $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
            imagefill($output, 0, 0, $transparent);
            
            // Create rounded rectangle mask
            imagealphablending($output, true);
            
            // Draw rounded rectangle (photo area)
            $this->imageRoundedRectangle($output, 0, 0, $width - 1, $height - 1, $radius, $source);
            
            // Draw black border
            $black = imagecolorallocate($output, 0, 0, 0);
            $this->imageRoundedRectangleBorder($output, 0, 0, $width - 1, $height - 1, $radius, $black, self::PHOTO_BORDER_WIDTH);
            
            // Save to temp file
            $tempOutputPath = storage_path('app/temp/photo_rounded_' . uniqid() . '.png');
            imagepng($output, $tempOutputPath);
            
            // Clean up
            imagedestroy($source);
            imagedestroy($output);
            unlink($tempPhotoPath);
            
            // Load back into Intervention Image
            $result = $this->imageManager->read($tempOutputPath);
            unlink($tempOutputPath);
            
            return $result;
            
        } catch (Exception $e) {
            Log::warning("Failed to apply rounded corners, using square: " . $e->getMessage());
            return $photo;
        }
    }

    /**
     * Draw rounded rectangle with image fill
     */
    protected function imageRoundedRectangle($img, $x1, $y1, $x2, $y2, $radius, $sourceImg)
    {
        // Copy source image into rounded rectangle shape
        $width = $x2 - $x1 + 1;
        $height = $y2 - $y1 + 1;
        
        // Create mask
        $mask = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($mask, 255, 255, 255);
        $black = imagecolorallocate($mask, 0, 0, 0);
        
        imagefill($mask, 0, 0, $black);
        
        // Draw rounded rectangle on mask
        imagefilledrectangle($mask, $radius, 0, $width - $radius - 1, $height - 1, $white);
        imagefilledrectangle($mask, 0, $radius, $width - 1, $height - $radius - 1, $white);
        
        // Draw corner circles
        imagefilledellipse($mask, $radius, $radius, $radius * 2, $radius * 2, $white);
        imagefilledellipse($mask, $width - $radius - 1, $radius, $radius * 2, $radius * 2, $white);
        imagefilledellipse($mask, $radius, $height - $radius - 1, $radius * 2, $radius * 2, $white);
        imagefilledellipse($mask, $width - $radius - 1, $height - $radius - 1, $radius * 2, $radius * 2, $white);
        
        // Apply mask to copy source image
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $maskColor = imagecolorat($mask, $x, $y);
                if ($maskColor == $white) {
                    $sourceColor = imagecolorat($sourceImg, $x, $y);
                    imagesetpixel($img, $x1 + $x, $y1 + $y, $sourceColor);
                }
            }
        }
        
        imagedestroy($mask);
    }

    /**
     * Draw rounded rectangle border
     */
    protected function imageRoundedRectangleBorder($img, $x1, $y1, $x2, $y2, $radius, $color, $thickness)
    {
        $width = $x2 - $x1 + 1;
        $height = $y2 - $y1 + 1;
        
        imagesetthickness($img, $thickness);
        
        // Draw straight lines
        imageline($img, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);  // Top
        imageline($img, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);  // Bottom
        imageline($img, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);  // Left
        imageline($img, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);  // Right
        
        // Draw corner arcs
        imagearc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 0, $color);
        imagearc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
        
        imagesetthickness($img, 1);
    }

    protected function fillQrBackground($card)
    {
        try {
            $wb = self::QR_WHITE_BOX;
            $bgColor = self::QR_BG_COLOR;
            $fillColor = sprintf('rgb(%d,%d,%d)', $bgColor[0], $bgColor[1], $bgColor[2]);

            for ($y = $wb[1]; $y <= $wb[3]; $y++) {
                $card->drawLine(function ($line) use ($wb, $y, $fillColor) {
                    $line->from($wb[0], $y);
                    $line->to($wb[2], $y);
                    $line->color($fillColor);
                    $line->width(1);
                });
            }
        } catch (Exception $e) {
            Log::warning("Failed to fill QR background: " . $e->getMessage());
        }
    }

    protected function pasteQrCode($card, $epicNo)
    {
        try {
            $verifyUrl = config('app.url') . '/verify/' . $epicNo;
            $qrCode = new QrCode(data: $verifyUrl, size: 300, margin: 10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrData = $result->getString();

            $qrTempPath = storage_path('app/temp/qr_' . uniqid() . '.png');
            if (!is_dir(dirname($qrTempPath))) {
                mkdir(dirname($qrTempPath), 0755, true);
            }
            file_put_contents($qrTempPath, $qrData);

            $qrImage = $this->imageManager->read($qrTempPath);
            $qrWidth = self::QR_BOX[2] - self::QR_BOX[0];
            $qrHeight = self::QR_BOX[3] - self::QR_BOX[1];
            $qrImage->resize($qrWidth, $qrHeight);
            $card->place($qrImage, 'top-left', self::QR_BOX[0], self::QR_BOX[1]);

            if (file_exists($qrTempPath)) {
                unlink($qrTempPath);
            }
        } catch (Exception $e) {
            Log::warning("Failed to generate QR code: " . $e->getMessage());
        }
    }

    protected function drawPtcCode($card, $ptcCode)
    {
        try {
            $ptcCode = mb_strtoupper($ptcCode);
            $fontSize = self::PTC_CODE_FONT_SIZE;
            $textWidth = $this->getTextWidth($ptcCode, $fontSize);
            $centerX = self::PTC_CODE_XY[0] - (int)($textWidth / 2);
            $fontPath = $this->getFontPath();

            $card->text($ptcCode, $centerX, self::PTC_CODE_XY[1], function (FontFactory $font) use ($fontSize, $fontPath) {
                if ($fontPath) {
                    $font->file($fontPath);
                }
                $font->size($fontSize);
                $font->color(self::FONT_COLOR);
                $font->align('left');
                $font->valign('top');
            });
        } catch (Exception $e) {
            Log::warning("Failed to draw PTC code: " . $e->getMessage());
        }
    }

    protected function getFontPath()
    {
        $fontPath = config('app.font_path');
        if ($fontPath && file_exists($fontPath)) {
            return $fontPath;
        }

        $fontPaths = [
            storage_path('app/fonts/arialbd.ttf'),
            storage_path('app/fonts/arial.ttf'),
            storage_path('app/fonts/DejaVuSans-Bold.ttf'),
            storage_path('app/fonts/DejaVuSans.ttf'),
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];

        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        Log::warning('No font file found, using default');
        return null;
    }
}
