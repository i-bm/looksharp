<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Institution;
use App\Models\Region;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutocompleteController extends Controller
{
    /**
     * Search skills for autocomplete.
     */
    public function skills(Request $request): JsonResponse
    {
        Log::info('Autocomplete skills search request', [
            'query' => $request->get('q'),
        ]);

        $query = $request->get('q', '');
        $query = trim($query);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            $skills = DB::transaction(function () use ($query) {
                return Skill::where('is_active', true)
                    ->where('name', 'LIKE', '%'.$query.'%')
                    ->orderBy('name', 'asc')
                    ->limit(20)
                    ->get(['id', 'name'])
                    ->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'name' => $skill->name,
                        ];
                    })
                    ->toArray();
            });

            Log::info('Autocomplete skills search completed', [
                'query' => $query,
                'results_count' => count($skills),
            ]);

            return response()->json($skills);
        } catch (\Exception $e) {
            Log::error('Autocomplete skills search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Search industries for autocomplete.
     */
    public function industries(Request $request): JsonResponse
    {
        Log::info('Autocomplete industries search request', [
            'query' => $request->get('q'),
        ]);

        $query = $request->get('q', '');
        $query = trim($query);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            $industries = DB::transaction(function () use ($query) {
                return Industry::where('is_active', true)
                    ->where('name', 'LIKE', '%'.$query.'%')
                    ->orderBy('name', 'asc')
                    ->limit(20)
                    ->get(['id', 'name'])
                    ->map(function ($industry) {
                        return [
                            'id' => $industry->id,
                            'name' => $industry->name,
                        ];
                    })
                    ->toArray();
            });

            Log::info('Autocomplete industries search completed', [
                'query' => $query,
                'results_count' => count($industries),
            ]);

            return response()->json($industries);
        } catch (\Exception $e) {
            Log::error('Autocomplete industries search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Search institutions for autocomplete.
     */
    public function institutions(Request $request): JsonResponse
    {
        Log::info('Autocomplete institutions search request', [
            'query' => $request->get('q'),
        ]);

        $query = $request->get('q', '');
        $query = trim($query);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            $institutions = DB::transaction(function () use ($query) {
                return Institution::where('is_active', true)
                    ->where('name', 'LIKE', '%'.$query.'%')
                    ->orderBy('name', 'asc')
                    ->limit(20)
                    ->get(['id', 'name'])
                    ->map(function ($institution) {
                        return [
                            'id' => $institution->id,
                            'name' => $institution->name,
                        ];
                    })
                    ->toArray();
            });

            Log::info('Autocomplete institutions search completed', [
                'query' => $query,
                'results_count' => count($institutions),
            ]);

            return response()->json($institutions);
        } catch (\Exception $e) {
            Log::error('Autocomplete institutions search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Search cities for autocomplete.
     */
    public function cities(Request $request): JsonResponse
    {
        Log::info('Autocomplete cities search request', [
            'query' => $request->get('q'),
            'country' => $request->get('country'),
            'region' => $request->get('region'),
        ]);

        $query = $request->get('q', '');
        $query = trim($query);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            $cities = DB::transaction(function () use ($query, $request) {
                $cityQuery = City::where('is_active', true)
                    ->where('name', 'LIKE', '%'.$query.'%');

                // Filter by country if provided
                $countryParam = $request->get('country');
                if ($countryParam) {
                    $country = Country::where('name', $countryParam)
                        ->orWhere('code', strtoupper($countryParam))
                        ->first();

                    if ($country) {
                        $cityQuery->where('country_id', $country->id);
                    }
                }

                // Filter by region if provided
                $regionParam = $request->get('region');
                if ($regionParam) {
                    $region = Region::where('name', $regionParam)->first();

                    if ($region) {
                        $cityQuery->where('region_id', $region->id);
                    }
                }

                return $cityQuery->orderBy('name', 'asc')
                    ->limit(20)
                    ->get(['id', 'name'])
                    ->map(function ($city) {
                        return [
                            'id' => $city->id,
                            'name' => $city->name,
                        ];
                    })
                    ->toArray();
            });

            Log::info('Autocomplete cities search completed', [
                'query' => $query,
                'country' => $request->get('country'),
                'region' => $request->get('region'),
                'results_count' => count($cities),
            ]);

            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Autocomplete cities search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Search regions for autocomplete.
     */
    public function regions(Request $request): JsonResponse
    {
        Log::info('Autocomplete regions search request', [
            'query' => $request->get('q'),
            'country' => $request->get('country'),
        ]);

        $query = $request->get('q', '');
        $query = trim($query);

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        try {
            $regions = DB::transaction(function () use ($query, $request) {
                $regionQuery = Region::where('is_active', true)
                    ->where('name', 'LIKE', '%'.$query.'%');

                // Filter by country if provided (defaults to Ghana for backward compatibility)
                $countryParam = $request->get('country', 'Ghana');
                if ($countryParam) {
                    $country = Country::where('name', $countryParam)
                        ->orWhere('code', strtoupper($countryParam))
                        ->first();

                    if ($country) {
                        $regionQuery->where('country_id', $country->id);
                    }
                }

                return $regionQuery->orderBy('name', 'asc')
                    ->limit(20)
                    ->get(['id', 'name'])
                    ->map(function ($region) {
                        return [
                            'id' => $region->id,
                            'name' => $region->name,
                        ];
                    })
                    ->toArray();
            });

            Log::info('Autocomplete regions search completed', [
                'query' => $query,
                'country' => $request->get('country', 'Ghana'),
                'results_count' => count($regions),
            ]);

            return response()->json($regions);
        } catch (\Exception $e) {
            Log::error('Autocomplete regions search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([], 500);
        }
    }
}
