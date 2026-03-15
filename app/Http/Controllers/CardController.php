<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GenerateCardJob;
use App\Models\GeneratedVoter;
use App\Helpers\VoterHelper;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    /**
     * Display card page
     */
    public function show(Request $request)
    {
        $mobile = session('verified_mobile');
        if (!$mobile) {
            return redirect()->route('home')->with('error', 'Please verify your mobile number first');
        }

        // Get existing card or latest generation
        $card = GeneratedVoter::where('mobile', $mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$card) {
            return redirect()->route('chatbot.epic')->with('error', 'No card found. Please complete the registration process.');
        }

        // Get voter details
        $voter = VoterHelper::findByEpicNo($card->epic_no);
        if (!$voter) {
            $voter = [
                'name' => $card->voter_name ?? 'N/A',
                'assembly_name' => $card->assembly_name ?? 'N/A',
                'district' => $card->district ?? 'N/A'
            ];
        }

        // Get generation count
        $genCount = GeneratedVoter::where('mobile', $mobile)->count();

        return view('card.display', [
            'cardUrl' => $card->card_url,
            'epicNo' => $card->epic_no,
            'voter' => $voter,
            'genCount' => $genCount
        ]);
    }

    /**
     * Generate new card
     */
    public function generateCard(Request $request)
    {
        try {
            $mobile = session('verified_mobile');
            if (!$mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mobile number not verified'
                ], 401);
            }

            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'epic_no' => 'required|string|min:5|max:20'
            ]);

            $epicNo = strtoupper(trim($request->input('epic_no')));
            $photoFile = $request->file('photo');

            // Verify voter exists
            $voter = VoterHelper::findByEpicNo($epicNo);
            if (!$voter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voter not found for the provided EPIC number'
                ], 404);
            }

            // Upload photo to Cloudinary
            $cloudinaryService = new CloudinaryService();
            $photoUrl = $cloudinaryService->upload($photoFile);

            if (!$photoUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload photo. Please try again.'
                ], 500);
            }

            // Create generation record
            $generation = GeneratedVoter::create([
                'mobile' => $mobile,
                'epic_no' => $epicNo,
                'voter_name' => $voter['name'] ?? '',
                'assembly_name' => $voter['assembly_name'] ?? $voter['assembly'] ?? '',
                'district' => $voter['district'] ?? '',
                'photo_url' => $photoUrl,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Dispatch async card generation job
            GenerateCardJob::dispatch($generation->id)->delay(now()->addSeconds(2));

            return response()->json([
                'success' => true,
                'message' => 'Card generation started successfully',
                'job_id' => $generation->id,
                'status_url' => route('api.card-status', $generation->id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = 'Validation failed';
            
            if ($errors->has('photo')) {
                $message = 'Please upload a valid image file (JPEG, PNG, JPG, GIF) under 5MB';
            } elseif ($errors->has('epic_no')) {
                $message = 'Please provide a valid EPIC number';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating card: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check card generation status
     */
    public function cardStatus($jobId)
    {
        try {
            $card = GeneratedVoter::find($jobId);

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'message' => 'Card generation record not found'
                ], 404);
            }

            // Verify ownership
            $mobile = session('verified_mobile');
            if ($card->mobile !== $mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'status' => $card->status,
                'progress' => $this->getProgress($card->status),
                'card_url' => $card->card_url,
                'message' => $this->getStatusMessage($card->status)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get progress percentage based on status
     */
    private function getProgress($status)
    {
        $progress = [
            'pending' => 10,
            'validating' => 20,
            'processing' => 50,
            'generating' => 75,
            'uploading' => 90,
            'completed' => 100,
            'failed' => 0,
        ];

        return $progress[$status] ?? 0;
    }

    /**
     * Get user-friendly status message
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'pending' => 'Card generation queued...',
            'validating' => 'Validating voter information...',
            'processing' => 'Processing your photo...',
            'generating' => 'Generating your ID card...',
            'uploading' => 'Finalizing card...',
            'completed' => 'Card generated successfully!',
            'failed' => 'Card generation failed. Please try again.',
        ];

        return $messages[$status] ?? 'Unknown status';
    }
}