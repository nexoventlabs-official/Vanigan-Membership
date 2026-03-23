<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\GeneratedVoter;
use App\Models\GenerationStat;
use App\Models\VerifiedMobile;
use App\Services\CardGenerationService;
use App\Services\CloudinaryService;
use App\Helpers\SecurityHelper;

class GenerateCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobId;
    protected $mobile;
    protected $epicNo;
    protected $photoUrl;
    protected $voterData;
    protected $referralCode;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        $jobId,
        $mobile,
        $epicNo,
        $photoUrl,
        $voterData,
        $referralCode = null
    ) {
        $this->jobId = $jobId;
        $this->mobile = $mobile;
        $this->epicNo = $epicNo;
        $this->photoUrl = $photoUrl;
        $this->voterData = $voterData;
        $this->referralCode = $referralCode;
    }

    /**
     * Execute the job.
     */
    public function handle(CardGenerationService $cardService, CloudinaryService $cloudinaryService)
    {
        try {
            // Update job status: processing
            $this->updateJobStatus('processing', 20, 'Validating voter data...');

            // Validate voter data
            if (!$this->voterData || empty($this->voterData['epic_no'])) {
                throw new \Exception('Invalid voter data');
            }

            // Update job status: downloading photo
            $this->updateJobStatus('processing', 30, 'Downloading photo...');

            // Download photo from Cloudinary
            $photoPath = $cloudinaryService->downloadPhoto($this->photoUrl);
            if (!$photoPath || !file_exists($photoPath)) {
                throw new \Exception('Failed to download photo');
            }

            // Update job status: generating card
            $this->updateJobStatus('processing', 50, 'Generating ID card...');

            // Generate card image
            $cardImagePath = $cardService->generateCard(
                $this->voterData,
                $photoPath,
                $this->epicNo
            );

            if (!$cardImagePath || !file_exists($cardImagePath)) {
                throw new \Exception('Failed to generate card');
            }

            // Update job status: uploading to cloudinary
            $this->updateJobStatus('processing', 70, 'Uploading card to cloud...');

            // Upload card to Cloudinary
            $cardUrl = $cloudinaryService->uploadCard($cardImagePath, $this->epicNo);

            if (!$cardUrl) {
                throw new \Exception('Failed to upload card to Cloudinary');
            }

            // Update job status: saving to database
            $this->updateJobStatus('processing', 85, 'Saving card information...');

            // Generate PTC code
            $ptcCode = SecurityHelper::generatePtcCode();

            // Save to database
            GeneratedVoter::updateOrCreate(
                ['EPIC_NO' => $this->epicNo, 'MOBILE_NO' => $this->mobile],
                [
                    'NAME' => $this->voterData['voter_name'] ?? $this->voterData['name'] ?? 'N/A',
                    'ASSEMBLY_NAME' => $this->voterData['assembly_name'] ?? 'N/A',
                    'AC_NO' => $this->voterData['AC_NO'] ?? null,
                    'photo_url' => $this->photoUrl,
                    'card_url' => $cardUrl,
                    'ptc_code' => $ptcCode,
                    'generated_at' => now(),
                ]
            );

            // Update generation stats
            GenerationStat::updateOrCreate(
                ['epic_no' => $this->epicNo],
                [
                    'generation_count' => \DB::raw('generation_count + 1'),
                    'last_generated' => now(),
                ]
            );

            // Mark mobile as verified
            VerifiedMobile::updateOrCreate(
                ['mobile' => $this->mobile],
                ['verified_at' => now()]
            );

            // Handle referral if present
            if ($this->referralCode) {
                $this->handleReferral($this->referralCode, $this->epicNo);
            }

            // Clean up temporary files
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            if (file_exists($cardImagePath)) {
                unlink($cardImagePath);
            }

            // Update job status: completed
            $this->updateJobStatus('completed', 100, 'Card generated successfully!', [
                'card_url' => $cardUrl,
                'ptc_code' => $ptcCode,
                'voter_name' => $this->voterData['voter_name'] ?? 'N/A',
            ]);

            Log::info("Card generated successfully for EPIC: {$this->epicNo}");

        } catch (\Exception $e) {
            Log::error("GenerateCardJob Error: " . $e->getMessage());
            $this->updateJobStatus('failed', 0, 'Error: ' . $e->getMessage());

            // Re-throw for queue to handle retries
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error("GenerateCardJob failed permanently: " . $exception->getMessage());
        $this->updateJobStatus('failed', 0, 'Card generation failed. Please try again.');
    }

    /**
     * Update job status in cache for polling
     */
    protected function updateJobStatus($status, $progress, $message, $data = [])
    {
        $jobStatus = [
            'status' => $status,
            'progress' => $progress,
            'message' => $message,
            'updated_at' => now()->toIso8601String(),
        ];

        if (!empty($data)) {
            $jobStatus = array_merge($jobStatus, $data);
        }

        // Store in cache for 1 hour
        app(\App\Services\CacheService::class)->put('job:' . $this->jobId, $jobStatus, 3600);

        Log::info("Job {$this->jobId} status: {$status} ({$progress}%)");
    }

    /**
     * Handle referral bonus
     */
    protected function handleReferral($referralCode, $epicNo)
    {
        try {
            // TODO: Implement referral logic
            // Find referrer by referral code
            // Update referred_members_count
            // Award bonus points if applicable
        } catch (\Exception $e) {
            Log::warning("Referral handling failed: " . $e->getMessage());
        }
    }
}
