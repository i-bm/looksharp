<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GTECService
{
    private const BASE_URL = 'https://gtec.edu.gh';

    private const API_ENDPOINT = '/institution-category/api';

    private const REQUEST_DELAY_SECONDS = 0.5; // Delay between requests to be respectful

    /**
     * Get all institution categories from GTEC.
     *
     * @return array<string>
     */
    public function getAllCategories(): array
    {
        return [
            'Chartered Private Tertiary Institution',
            'Traditional University',
            'Regionally-Owned (West Africa) Tertiary Institution',
            'Public Technical University',
            'Private Tertiary Institutions Offering HND/Degree Programmes',
            'Public College of Education',
            'Public Degree Awarding and Professional Institution',
            'Private Nurses/Health Training College',
            'Public Nurses/Health Training College',
            'Private Polytechnic',
            'Private College of Education',
            'Tutorial College',
            'Distance Learning Institution',
            'Registered Foreign Institution',
            'College of Agriculture',
        ];
    }

    /**
     * Fetch institutions from GTEC API for a specific category and page.
     *
     * @return array{numberOfElements: int, totalPages: int, page: int, max: int, lines: array}
     *
     * @throws \Exception
     */
    public function fetchInstitutionsByCategory(string $categoryName, int $page = 1): array
    {
        Log::info('Fetching institutions from GTEC API', [
            'category' => $categoryName,
            'page' => $page,
        ]);

        try {
            $url = self::BASE_URL.self::API_ENDPOINT.'?name='.urlencode($categoryName).'&page='.$page;

            $response = Http::timeout(30)->get($url);

            if (! $response->successful()) {
                Log::error('GTEC API request failed', [
                    'category' => $categoryName,
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception("Failed to fetch institutions from GTEC API. Status: {$response->status()}");
            }

            $data = $response->json();

            if (! isset($data['lines'])) {
                Log::warning('Unexpected API response structure', [
                    'category' => $categoryName,
                    'page' => $page,
                    'response' => $data,
                ]);

                return [
                    'numberOfElements' => 0,
                    'totalPages' => 0,
                    'page' => $page,
                    'max' => 0,
                    'lines' => [],
                ];
            }

            Log::info('Successfully fetched institutions from GTEC API', [
                'category' => $categoryName,
                'page' => $page,
                'count' => count($data['lines']),
                'totalPages' => $data['totalPages'] ?? 0,
            ]);

            return [
                'numberOfElements' => $data['numberOfElements'] ?? 0,
                'totalPages' => $data['totalPages'] ?? 0,
                'page' => $data['page'] ?? $page,
                'max' => $data['max'] ?? 0,
                'lines' => $data['lines'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Exception while fetching institutions from GTEC API', [
                'category' => $categoryName,
                'page' => $page,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Fetch all institutions from all categories.
     *
     * @return array<int, array{id: int|null, name: string, email: string|null, transactionId: string, category: string, location: string, accredStatus: string, region: string, level: int}>
     */
    public function fetchAllInstitutions(): array
    {
        Log::info('Starting to fetch all institutions from GTEC');

        $allInstitutions = [];
        $categories = $this->getAllCategories();
        $totalCategories = count($categories);

        foreach ($categories as $index => $category) {
            Log::info('Processing category', [
                'category' => $category,
                'progress' => ($index + 1).'/'.$totalCategories,
            ]);

            try {
                $page = 1;
                $totalPages = 1;

                do {
                    // Add delay between requests to be respectful
                    if ($page > 1 || $index > 0) {
                        usleep((int) (self::REQUEST_DELAY_SECONDS * 1000000));
                    }

                    $response = $this->fetchInstitutionsByCategory($category, $page);
                    $totalPages = $response['totalPages'];

                    foreach ($response['lines'] as $institution) {
                        // Use transactionId as unique key to avoid duplicates
                        $key = $institution['transactionId'] ?? $institution['name'] ?? uniqid('inst_', true);
                        $allInstitutions[$key] = $institution;
                    }

                    $page++;
                } while ($page <= $totalPages);

                Log::info('Completed processing category', [
                    'category' => $category,
                    'institutions_found' => count($response['lines']),
                    'total_pages' => $totalPages,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to process category, continuing with next', [
                    'category' => $category,
                    'error' => $e->getMessage(),
                ]);

                // Continue with next category even if this one fails
                continue;
            }
        }

        Log::info('Completed fetching all institutions from GTEC', [
            'total_institutions' => count($allInstitutions),
            'total_categories' => $totalCategories,
        ]);

        return array_values($allInstitutions);
    }
}
