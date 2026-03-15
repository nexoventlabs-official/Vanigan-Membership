<?php

namespace App\Services;

use App\Helpers\VoterHelper;
use App\Models\GeneratedVoter;
use Illuminate\Support\Facades\Log;
use Exception;

class VoterLookupService
{
    /**
     * Comprehensive voter lookup
     * Combines voter DB data with generated card data
     */
    public function findVoterWithCardStatus($epicNo)
    {
        try {
            // Lookup voter from voters DB
            $voterData = VoterHelper::findByEpicNo($epicNo);

            if (!$voterData) {
                return [
                    'found' => false,
                    'message' => 'Voter not found in database',
                ];
            }

            // Check if card already generated
            $generatedCard = GeneratedVoter::where('epic_no', $epicNo)
                ->orderBy('created_at', 'desc')
                ->first();

            return [
                'found' => true,
                'voter' => $voterData,
                'card_generated' => $generatedCard ? true : false,
                'card' => $generatedCard ? [
                    'card_url' => $generatedCard->card_url,
                    'photo_url' => $generatedCard->photo_url,
                    'ptc_code' => $generatedCard->ptc_code,
                    'generated_at' => $generatedCard->created_at,
                ] : null,
            ];

        } catch (Exception $e) {
            Log::error("VoterLookupService Error: " . $e->getMessage());
            return [
                'found' => false,
                'message' => 'Error looking up voter',
            ];
        }
    }

    /**
     * Validate voter before card generation
     */
    public function validateVoterForGeneration($epicNo, $mobile)
    {
        try {
            // Find voter
            $voterData = VoterHelper::findByEpicNo($epicNo);

            if (!$voterData) {
                return [
                    'valid' => false,
                    'message' => 'Voter not found. Please check EPIC number.',
                ];
            }

            // Check if card already generated with same mobile
            $existingCard = GeneratedVoter::where('epic_no', $epicNo)
                ->where('mobile', $mobile)
                ->first();

            if ($existingCard) {
                return [
                    'valid' => false,
                    'message' => 'Card already generated for this voter with this mobile.',
                    'existing_card' => $existingCard->card_url,
                ];
            }

            return [
                'valid' => true,
                'message' => 'Voter validated successfully.',
                'voter_data' => $voterData,
            ];

        } catch (Exception $e) {
            Log::error("VoterLookupService::validateVoterForGeneration Error: " . $e->getMessage());
            return [
                'valid' => false,
                'message' => 'Validation error. Please try again.',
            ];
        }
    }
}
