<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting WorkModelSeeder');

        try {
            DB::transaction(function () {
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

                Log::info('WorkModelSeeder completed successfully', [
                    'count' => count($workModels),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('WorkModelSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
