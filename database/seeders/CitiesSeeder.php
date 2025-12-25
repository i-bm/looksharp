<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting CitiesSeeder');

        try {
            DB::transaction(function () {
                $ghana = Country::where('code', 'GH')->first();

                if (!$ghana) {
                    Log::warning('Ghana country not found. Please run CountriesSeeder first.');
                    return;
                }

                // Get regions for mapping
                $regions = Region::where('country_id', $ghana->id)->get()->keyBy('name');

                // Major cities in Ghana with their regions
                $cities = [
                    // Greater Accra Region
                    ['name' => 'Accra', 'region' => 'Greater Accra'],
                    ['name' => 'Tema', 'region' => 'Greater Accra'],
                    ['name' => 'Teshie', 'region' => 'Greater Accra'],
                    ['name' => 'Nungua', 'region' => 'Greater Accra'],
                    ['name' => 'Dome', 'region' => 'Greater Accra'],
                    ['name' => 'Kasoa', 'region' => 'Greater Accra'],
                    ['name' => 'Madina', 'region' => 'Greater Accra'],
                    ['name' => 'Ashaiman', 'region' => 'Greater Accra'],

                    // Ashanti Region
                    ['name' => 'Kumasi', 'region' => 'Ashanti'],
                    ['name' => 'Obuasi', 'region' => 'Ashanti'],
                    ['name' => 'Konongo', 'region' => 'Ashanti'],
                    ['name' => 'Ejura', 'region' => 'Ashanti'],
                    ['name' => 'Mampong', 'region' => 'Ashanti'],
                    ['name' => 'Bekwai', 'region' => 'Ashanti'],

                    // Northern Region
                    ['name' => 'Tamale', 'region' => 'Northern'],
                    ['name' => 'Yendi', 'region' => 'Northern'],
                    ['name' => 'Savelugu', 'region' => 'Northern'],

                    // Western Region
                    ['name' => 'Takoradi', 'region' => 'Western'],
                    ['name' => 'Tarkwa', 'region' => 'Western'],
                    ['name' => 'Axim', 'region' => 'Western'],
                    ['name' => 'Prestea', 'region' => 'Western'],
                    ['name' => 'Elmina', 'region' => 'Western'],

                    // Central Region
                    ['name' => 'Cape Coast', 'region' => 'Central'],
                    ['name' => 'Winneba', 'region' => 'Central'],
                    ['name' => 'Saltpond', 'region' => 'Central'],
                    ['name' => 'Agona Swedru', 'region' => 'Central'],
                    ['name' => 'Dunkwa', 'region' => 'Central'],

                    // Eastern Region
                    ['name' => 'Koforidua', 'region' => 'Eastern'],
                    ['name' => 'Nkawkaw', 'region' => 'Eastern'],
                    ['name' => 'Suhum', 'region' => 'Eastern'],
                    ['name' => 'Akim Oda', 'region' => 'Eastern'],
                    ['name' => 'Mpraeso', 'region' => 'Eastern'],
                    ['name' => 'Aburi', 'region' => 'Eastern'],
                    ['name' => 'Akropong', 'region' => 'Eastern'],

                    // Volta Region
                    ['name' => 'Ho', 'region' => 'Volta'],
                    ['name' => 'Hohoe', 'region' => 'Volta'],
                    ['name' => 'Keta', 'region' => 'Volta'],
                    ['name' => 'Aflao', 'region' => 'Volta'],
                    ['name' => 'Kpandu', 'region' => 'Volta'],

                    // Bono Region
                    ['name' => 'Sunyani', 'region' => 'Bono'],
                    ['name' => 'Berekum', 'region' => 'Bono'],
                    ['name' => 'Wenchi', 'region' => 'Bono'],

                    // Bono East Region
                    ['name' => 'Techiman', 'region' => 'Bono East'],
                    ['name' => 'Kintampo', 'region' => 'Bono East'],

                    // Ahafo Region
                    ['name' => 'Goaso', 'region' => 'Ahafo'],

                    // Western North Region
                    ['name' => 'Bibiani', 'region' => 'Western North'],
                    ['name' => 'Sefwi Wiawso', 'region' => 'Western North'],

                    // Upper East Region
                    ['name' => 'Bolgatanga', 'region' => 'Upper East'],
                    ['name' => 'Navrongo', 'region' => 'Upper East'],

                    // Upper West Region
                    ['name' => 'Wa', 'region' => 'Upper West'],

                    // Oti Region
                    ['name' => 'Dambai', 'region' => 'Oti'],

                    // North East Region
                    ['name' => 'Nalerigu', 'region' => 'North East'],

                    // Savannah Region
                    ['name' => 'Damongo', 'region' => 'Savannah'],

                    // Cities without specific region mapping (will be created with country only)
                    ['name' => 'Asamankese', 'region' => null],
                    ['name' => 'Oduponkpehe', 'region' => null],
                    ['name' => 'Kibi', 'region' => null],
                ];

                $createdCount = 0;
                foreach ($cities as $cityData) {
                    $region = null;
                    if ($cityData['region'] && isset($regions[$cityData['region']])) {
                        $region = $regions[$cityData['region']];
                    }

                    $city = City::updateOrCreate(
                        [
                            'name' => $cityData['name'],
                            'country_id' => $ghana->id,
                        ],
                        [
                            'region_id' => $region?->id,
                            'is_active' => true,
                        ]
                    );

                    if ($city->wasRecentlyCreated) {
                        $createdCount++;
                    }
                }

                Log::info('CitiesSeeder completed successfully', [
                    'cities_count' => count($cities),
                    'created_count' => $createdCount,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('CitiesSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

