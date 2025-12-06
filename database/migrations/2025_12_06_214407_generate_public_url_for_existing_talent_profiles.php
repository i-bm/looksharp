<?php

use App\Models\TalentProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Generate public_url for existing profiles that don't have one
        $profiles = TalentProfile::whereNull('public_url')->orWhere('public_url', '')->get();

        foreach ($profiles as $profile) {
            $user = $profile->user;
            if ($user) {
                $firstName = $profile->first_name ?? $user->first_name ?? 'user';
                $lastName = $profile->last_name ?? $user->last_name ?? '';
                $publicUrl = generatePublicUrlSlug($firstName, $lastName, $profile->public_url);

                $profile->update(['public_url' => $publicUrl]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed
        // We could clear public_url if needed, but it's better to keep them
    }
};
