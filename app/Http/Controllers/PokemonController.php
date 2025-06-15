<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PokemonController extends Controller
{
    private $apiUrl = 'https://pokeapi.co/api/v2';

    public function index(Request $request)
    {
        $query = strtolower($request->input('search'));
        $pokemons = [];
        $addedPokemons = Pokemon::pluck('name');

        try {
            if ($query) {
                // Fetch all 151 Pokemon names to search locally
                $allPokemonNames = Cache::remember('pokeapi.all_pokemon_names', 60 * 24, function () {
                    $response = Http::get("{$this->apiUrl}/pokemon?limit=151");
                    if ($response->successful()) {
                        return collect($response->json()['results'])->pluck('name')->toArray();
                    }
                    return [];
                });

                $matchingNames = array_filter($allPokemonNames, function ($pokemonName) use ($query) {
                    return strpos($pokemonName, $query) !== false;
                });

                foreach ($matchingNames as $pokemonName) {
                    // Fetch detailed info for each matching Pokemon
                    $pokemonDetail = Cache::remember('pokeapi.pokemon.' . $pokemonName, 60 * 24, function () use ($pokemonName) {
                        return Http::get("{$this->apiUrl}/pokemon/{$pokemonName}")->json();
                    });

                    if ($pokemonDetail) {
                        $pokemons[] = [
                            'name' => ucfirst($pokemonDetail['name']),
                            'type' => isset($pokemonDetail['types'][0]['type']['name']) ? ucfirst($pokemonDetail['types'][0]['type']['name']) : 'N/A',
                            'description' => isset($pokemonDetail['types'][0]['type']['name']) ? "A {$pokemonDetail['types'][0]['type']['name']} type Pokemon." : 'No description available.',
                            'image' => $pokemonDetail['sprites']['front_default'] ?? 'https://via.placeholder.com/96x96',
                            'abilities' => array_map(function($ability) {
                                return ucfirst($ability['ability']['name']);
                            }, $pokemonDetail['abilities'] ?? []),
                            'height' => isset($pokemonDetail['height']) ? $pokemonDetail['height'] / 10 : 'N/A',
                            'weight' => isset($pokemonDetail['weight']) ? $pokemonDetail['weight'] / 10 : 'N/A',
                            'stats' => array_map(function($stat) {
                                return [
                                    'name' => ucfirst($stat['stat']['name']),
                                    'value' => $stat['base_stat']
                                ];
                            }, $pokemonDetail['stats'] ?? []),
                            'is_added' => in_array(strtolower($pokemonDetail['name']), array_map('strtolower', $addedPokemons))
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error fetching Pokemon data: ' . $e->getMessage());
        }

        return view('myfavoritesubject', compact('pokemons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        // Check if Pokemon already exists locally to prevent duplicates
        if (Pokemon::exists('name', $request->name)) {
            return back()->with('error', 'This Pokemon is already in your list!');
        }

        Pokemon::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'added_by' => session('user_email') ?? 'Guest',
        ]);

        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon added successfully!');
    }

    public function edit($id)
    {
        $pokemon = Pokemon::find($id);
        if (!$pokemon) {
            return redirect()->route('myfavoritesubject')->with('error', 'Pokemon not found!');
        }
        // Since we are not persisting to a separate view for edit with cache, 
        // we'll just redirect back with an error or success if not found.
        // If a separate edit view is needed, it would be a new feature.
        return view('pokemon.edit', compact('pokemon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $pokemon = Pokemon::update($id, [
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description
        ]);

        if (!$pokemon) {
            return redirect()->route('myfavoritesubject')->with('error', 'Pokemon not found for update!');
        }

        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon updated successfully!');
    }

    public function destroy($id)
    {
        $success = Pokemon::destroy($id);

        if (!$success) {
            return redirect()->route('myfavoritesubject')->with('error', 'Failed to delete Pokemon!');
        }

        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon deleted successfully!');
    }

    public function addApiPokemon(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        // Check if Pokemon already exists locally to prevent duplicates
        if (Pokemon::exists('name', $request->name)) {
            return back()->with('error', 'This Pokemon is already in your list!');
        }

        Pokemon::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'added_by' => session('user_email') ?? 'API User',
            'image_url' => $request->image // Store image URL from API
        ]);

        return back()->with('success', $request->name . ' added to your Pokemon list!');
    }
} 