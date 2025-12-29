<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed work models if they don't exist
        $workModels = [
            ['name' => 'remote', 'display_name' => 'Remote', 'order' => 1],
            ['name' => 'hybrid', 'display_name' => 'Hybrid', 'order' => 2],
            ['name' => 'on_site', 'display_name' => 'On-site', 'order' => 3],
        ];

        foreach ($workModels as $workModel) {
            DB::table('work_models')->insertOrIgnore([
                'id' => Str::uuid(),
                'name' => $workModel['name'],
                'display_name' => $workModel['display_name'],
                'is_active' => true,
                'order' => $workModel['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Migrate existing preferred_location data to pivot table
        $profiles = DB::table('talent_profiles')
            ->whereNotNull('preferred_location')
            ->get(['id', 'preferred_location']);

        foreach ($profiles as $profile) {
            $workModel = DB::table('work_models')
                ->where('name', $profile->preferred_location)
                ->first();

            if ($workModel) {
                DB::table('talent_profile_work_model')->insertOrIgnore([
                    'id' => Str::uuid(),
                    'talent_profile_id' => $profile->id,
                    'work_model_id' => $workModel->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Remove the old preferred_location column
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->dropColumn('preferred_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the preferred_location column
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->enum('preferred_location', ['remote', 'hybrid', 'on_site'])->nullable();
        });

        // Migrate data back from pivot table (take first work model)
        $profiles = DB::table('talent_profile_work_model')
            ->join('work_models', 'talent_profile_work_model.work_model_id', '=', 'work_models.id')
            ->select('talent_profile_work_model.talent_profile_id', 'work_models.name')
            ->orderBy('talent_profile_work_model.created_at')
            ->get()
            ->groupBy('talent_profile_id')
            ->map(function ($group) {
                return $group->first()->name;
            });

        foreach ($profiles as $profileId => $location) {
            DB::table('talent_profiles')
                ->where('id', $profileId)
                ->update(['preferred_location' => $location]);
        }
    }
};
