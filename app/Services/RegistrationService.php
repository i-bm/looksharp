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

                // Generate unique public URL slug from user's name
                $firstName = $user->first_name ?? 'user';
                $lastName = $user->last_name ?? '';
                // $publicUrl = generatePublicUrlSlug($firstName, $lastName);

                // Create new talent profile with default values
                // verification_status defaults to null (which represents 'not_started')
                $talentProfile = TalentProfile::create([
                    'user_id' => $user->id,
                    // 'public_url' => $publicUrl,
                    'verification_status' => null, // null = 'not_started' (handled in views/controllers)
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
