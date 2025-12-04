<?php

namespace App\Services;

use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationService
{
    /**
     * Initialize a talent profile for a newly registered user.
     */
    public function initializeTalentProfile(User $user): TalentProfile
    {
        try {
            return DB::transaction(function () use ($user) {
                // Check if profile already exists
                if ($user->talentProfile) {
                    return $user->talentProfile;
                }

                // Create new talent profile with default values
                $talentProfile = TalentProfile::create([
                    'user_id' => $user->id,
                    'verification_status' => 'pending',
                    'profile_completeness_score' => 0,
                ]);

                return $talentProfile;
            });
        } catch (\Exception $e) {
            Log::error('Failed to initialize talent profile: '.$e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to create profile. Please try again.');
        }
    }
}
