<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Institution;
use App\Services\GTECService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstitutionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting institutions seeder');

        $gtecService = new GTECService;

        try {
            Log::info('Fetching institutions from GTEC API');

            $institutions = $gtecService->fetchAllInstitutions();

            Log::info('Fetched institutions from GTEC API', [
                'total_institutions' => count($institutions),
            ]);

            if (empty($institutions)) {
                Log::warning('No institutions fetched from GTEC API');

                return;
            }

            DB::transaction(function () use ($institutions) {
                $created = 0;
                $updated = 0;
                $skipped = 0;

                foreach ($institutions as $institutionData) {
                    try {
                        // Map API response fields to Institution model fields
                        $mappedData = $this->mapInstitutionData($institutionData);

                        // Use updateOrCreate to avoid duplicates based on name
                        $institution = Institution::updateOrCreate(
                            ['name' => $mappedData['name']],
                            $mappedData
                        );

                        if ($institution->wasRecentlyCreated) {
                            $created++;
                            Log::debug('Created institution', [
                                'institution_id' => $institution->id,
                                'name' => $institution->name,
                            ]);
                        } else {
                            $updated++;
                            Log::debug('Updated institution', [
                                'institution_id' => $institution->id,
                                'name' => $institution->name,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $skipped++;
                        Log::error('Failed to process institution', [
                            'institution_name' => $institutionData['name'] ?? 'Unknown',
                            'error' => $e->getMessage(),
                        ]);

                        // Continue with next institution even if this one fails
                        continue;
                    }
                }

                Log::info('Completed seeding institutions', [
                    'created' => $created,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'total_processed' => count($institutions),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to seed institutions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to seed institutions. Please try again.');
        }
    }

    /**
     * Map GTEC API response data to Institution model fields.
     *
     * @param  array<string, mixed>  $apiData
     * @return array<string, mixed>
     */
    private function mapInstitutionData(array $apiData): array
    {
        return [
            'name' => $apiData['name'] ?? '',
            'email' => $apiData['email'] ?? null,
            'phone' => null, // Not available in API response
            'address' => null, // Not available in API response
            'city' => $apiData['location'] ?? null,
            'state' => $apiData['region'] ?? null,
            'zip' => null, // Not available in API response
            'country' => 'Ghana', // Default to Ghana
            'website' => null, // Not available in API response
            'logo' => null, // Not available in API response
            'is_active' => ($apiData['accredStatus'] ?? '') === 'Active',
        ];
    }
}
