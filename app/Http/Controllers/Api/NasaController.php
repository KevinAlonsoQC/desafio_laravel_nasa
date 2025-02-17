<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class NasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $endpoints_nasa = [];

    public function __construct()
    {
        $apiKey = env('NASA_API_KEY');
        $this->endpoints_nasa = [
            'RBE' => 'https://api.nasa.gov/DONKI/RBE?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
            'MPC' => 'https://api.nasa.gov/DONKI/MPC?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
            'HSS' => 'https://api.nasa.gov/DONKI/HSS?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
            'WSAEnlilSimulations' => 'https://api.nasa.gov/DONKI/WSAEnlilSimulations?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
            'IPS' => 'https://api.nasa.gov/DONKI/IPS?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
            'GST' => 'https://api.nasa.gov/DONKI/GST?startDate=2025-01-01&endDate=2025-02-17&api_key=' . $apiKey,
        ];
    }
    public function index()
    {
        $return = [];
        foreach ($this->endpoints_nasa as $endpoint) {
            $response = Http::get($endpoint);
            $json = $response->json();

            if (is_array($json)) {
                foreach ($json as $item) {
                    if (isset($item['instruments']) && is_array($item['instruments'])) {
                        foreach ($item['instruments'] as $instrument) {
                            if (isset($instrument['displayName'])) {
                                $return[] = $instrument['displayName'];
                            }
                        }
                    }
                }
            }
        }

        $return = array_values(array_unique($return));
        return response()->json([
            'instruments' => $return
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //El desafío no utiliza métodos post.
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/posts/' . $id);
        $data = $response->json();
        return ['title' => $data['title']];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //El desafío no utiliza métodos patch/put.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //El desafío no utiliza métodos delete.

    }

    public function activityIds()
    {
        $activityIds = [];
        foreach ($this->endpoints_nasa as $endpoint) {
            $response = Http::timeout(60)->get($endpoint);
            $data = $response->json();

            if (is_array($data)) {
                $stack = [$data];
                while (!empty($stack)) {
                    $current = array_pop($stack);
                    if (is_array($current)) {
                        if (isset($current['activityID']) && is_string($current['activityID'])) {
                            $parts = explode('-', $current['activityID']);
                            if (count($parts) >= 2) {
                                $finalId = $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                                $activityIds[] = $finalId;
                            }
                        }
                        foreach ($current as $value) {
                            if (is_array($value)) {
                                $stack[] = $value;
                            }
                        }
                    }
                }
            }
        }

        $activityIds = array_values(array_unique($activityIds));

        return response()->json([
            'activitysIds' => $activityIds,
        ]);
    }

    public function instrumentsPercentage()
    {
        $instrumentsCount = [];
        $totalCount = 0;

        foreach ($this->endpoints_nasa as $endpoint) {
            $response = Http::timeout(60)->get($endpoint);
            $data = $response->json();
            if (is_array($data)) {
                $stack = [$data];
                while (!empty($stack)) {
                    $current = array_pop($stack);
                    if (is_array($current)) {
                        if (isset($current['instruments']) && is_array($current['instruments'])) {
                            foreach ($current['instruments'] as $instrumentObj) {
                                if (isset($instrumentObj['displayName'])) {
                                    $displayName = $instrumentObj['displayName'];
                                    $instrumentsCount[$displayName] = ($instrumentsCount[$displayName] ?? 0) + 1;
                                    $totalCount++;
                                }
                            }
                        }
                        foreach ($current as $value) {
                            if (is_array($value)) {
                                $stack[] = $value;
                            }
                        }
                    }
                }
            }
        }

        $instrumentsPercentage = [];
        if ($totalCount > 0) {
            foreach ($instrumentsCount as $instrument => $count) {
                $instrumentsPercentage[$instrument] = round($count / $totalCount, 1);
            }
        }
        return response()->json([
            'instruments_use' => $instrumentsPercentage
        ]);
    }

    public function instrumentActivity(Request $request)
    {
        $request->validate([
            'instrument' => 'required|string',
        ]);
        $instrumentName = $request->input('instrument');
        $activityCount = [];
        $totalCount = 0;

        foreach ($this->endpoints_nasa as $endpoint) {
            $response = Http::timeout(60)->get($endpoint);
            $data = $response->json();

            if (is_array($data)) {
                $stack = [$data];
                while (!empty($stack)) {
                    $current = array_pop($stack);
                    if (is_array($current)) {
                        $hasInstrument = false;
                        if (isset($current['instruments']) && is_array($current['instruments'])) {
                            foreach ($current['instruments'] as $instObj) {
                                if (
                                    isset($instObj['displayName'])
                                    && $instObj['displayName'] === $instrumentName
                                ) {
                                    $hasInstrument = true;
                                    break;
                                }
                            }
                        }

                        if ($hasInstrument && isset($current['activityID'])) {
                            $activityIdFull = $current['activityID'];
                            $parts = explode('-', $activityIdFull);
                            if (count($parts) >= 2) {
                                $finalId = $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                                $activityCount[$finalId] = ($activityCount[$finalId] ?? 0) + 1;
                                $totalCount++;
                            }
                        }

                        foreach ($current as $value) {
                            if (is_array($value)) {
                                $stack[] = $value;
                            }
                        }
                    }
                }
            }
        }

        $activityPercentages = [];
        if ($totalCount > 0) {
            foreach ($activityCount as $activityId => $count) {
                $activityPercentages[$activityId] = round($count / $totalCount, 1);
            }
        }
        return response()->json([
            'instrument_activity' => [
                $instrumentName => $activityPercentages
            ]
        ]);
    }
}