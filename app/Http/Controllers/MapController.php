<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marker;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if ($query) {
            // Use OpenStreetMap's Nominatim service for geocoding
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel Map Application'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 5,
                'addressdetails' => 1
            ]);

            $results = collect($response->json());
            
            // Filter results to find the most relevant city match
            $cityResult = $results->first(function ($result) use ($query) {
                $address = $result['address'] ?? [];
                $cityName = strtolower($address['city'] ?? 
                           $address['town'] ?? 
                           $address['village'] ?? 
                           $address['suburb'] ?? 
                           '');
                
                // Check if the city name exactly matches the query (case-insensitive)
                return $cityName === strtolower($query);
            });

            // If no exact match found, try to find a result where the display name starts with the query
            if (!$cityResult) {
                $cityResult = $results->first(function ($result) use ($query) {
                    return strtolower($result['display_name']) === strtolower($query) ||
                           str_starts_with(strtolower($result['display_name']), strtolower($query . ','));
                });
            }

            // If still no match found, use the first result
            if (!$cityResult && $results->isNotEmpty()) {
                $cityResult = $results->first();
            }

            if ($cityResult) {
                $address = $cityResult['address'] ?? [];
                $cityName = $address['city'] ?? 
                           $address['town'] ?? 
                           $address['village'] ?? 
                           $address['suburb'] ?? 
                           $cityResult['display_name'];

                $searchResults = [[
                    'name' => $cityName,
                    'lat' => $cityResult['lat'],
                    'lng' => $cityResult['lon'],
                    'full_address' => $cityResult['display_name']
                ]];

                if ($request->wantsJson()) {
                    return response()->json($searchResults);
                }

                return view('map.index', compact('searchResults'));
            }
        }

        if ($request->wantsJson()) {
            return response()->json([]);
        }

        return redirect()->route('map.index');
    }

    public function getMap()
    {
        $markers = Marker::all();
        return view('map', compact('markers'));
    }
}
