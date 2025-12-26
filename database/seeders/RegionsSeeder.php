<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting RegionsSeeder');

        try {
            DB::transaction(function () {
                $ghana = Country::where('code', 'GH')->first();

                if (!$ghana) {
                    Log::warning('Ghana country not found. Please run CountriesSeeder first.');
                    return;
                }

                $regions = [
                    'Ahafo',
                    'Ashanti',
                    'Bono',
                    'Bono East',
                    'Central',
                    'Eastern',
                    'Greater Accra',
                    'North East',
                    'Northern',
                    'Oti',
                    'Savannah',
                    'Upper East',
                    'Upper West',
                    'Volta',
                    'Western',
                    'Western North',
                ];

                $createdCount = 0;
                foreach ($regions as $regionName) {
                    $region = Region::updateOrCreate(
                        [
                            'country_id' => $ghana->id,
                            'name' => $regionName,
                        ],
                        [
                            'is_active' => true,
                        ]
                    );

                    if ($region->wasRecentlyCreated) {
                        $createdCount++;
                    }
                }

                Log::info('RegionsSeeder completed successfully', [
                    'regions_count' => count($regions),
                    'created_count' => $createdCount,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('RegionsSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

