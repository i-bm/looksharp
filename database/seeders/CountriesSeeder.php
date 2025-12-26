<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting CountriesSeeder');

        try {
            DB::transaction(function () {
                $countries = [
                    [
                        'name' => 'Ghana',
                        'code' => 'GH',
                        'is_active' => true,
                    ],
                ];

                foreach ($countries as $country) {
                    Country::updateOrCreate(
                        ['code' => $country['code']],
                        $country
                    );
                }

                Log::info('CountriesSeeder completed successfully', [
                    'countries_count' => count($countries),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('CountriesSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
