<?php

namespace App\Http\Controllers;

use App\Models\MyFavoriteSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MyFavoriteSubjectController extends Controller
{
    public function index()
    {
        // Temporarily redirect to home to debug jQuery error
        return redirect()->route('home');
    }

    public function create()
    {
        return view('favorite-subjects.create');
    }

    public function searchPokemon(Request $request)
    {
        $pokemonName = strtolower($request->input('pokemon_name'));
        
        try {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$pokemonName}");
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'name' => ucfirst($data['name']),
                    'image' => $data['sprites']['front_default'],
                    'types' => collect($data['types'])->pluck('type.name'),
                    'abilities' => collect($data['abilities'])->pluck('ability.name')
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Pokemon not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching Pokemon data'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:50',
            'image' => 'required|string|max:255'
        ]);

        $subject = MyFavoriteSubject::create($validated);
        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon added successfully!');
    }

    public function edit($id)
    {
        $subject = MyFavoriteSubject::find($id);
        if (!$subject) {
            return redirect()->route('myfavoritesubject')->with('error', 'Pokemon not found!');
        }
        return view('myfavoritesubject.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|max:50',
            'image' => 'required|string|max:255'
        ]);

        $subject = MyFavoriteSubject::updateSubject($id, $validated);
        if (!$subject) {
            return redirect()->route('myfavoritesubject')->with('error', 'Pokemon not found!');
        }

        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon updated successfully!');
    }

    public function destroy($id)
    {
        MyFavoriteSubject::deleteSubject($id);
        return redirect()->route('myfavoritesubject')->with('success', 'Pokemon deleted successfully!');
    }

    public function apiIndex(Request $request)
    {
        $query = MyFavoriteSubject::query();

        // Filter by element type
        if ($request->has('element_type')) {
            $query->where('element_type', $request->element_type);
        }

        // Filter by power level range
        if ($request->has('min_power')) {
            $query->where('power_level', '>=', $request->min_power);
        }
        if ($request->has('max_power')) {
            $query->where('power_level', '<=', $request->max_power);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $subjects = $query->paginate($limit, ['*'], 'page', $page);
        
        return response()->json([
            'data' => $subjects->items(),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'last_page' => $subjects->lastPage(),
                'per_page' => $subjects->perPage(),
                'total' => $subjects->total(),
                'filters' => [
                    'element_type' => $request->element_type,
                    'min_power' => $request->min_power,
                    'max_power' => $request->max_power,
                    'search' => $request->search,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ]
            ]
        ]);
    }

    public function apiSearch(Request $request)
    {
        $apiUrl = $request->input('api_url', 'https://hajusrakendus.tak22jasin.itmajakas.ee/api/subjects');
        
        try {
            // If the URL is our own API endpoint, get data directly from the database
            if (strpos($apiUrl, '/api/favorite-subjects') !== false) {
                $subjects = MyFavoriteSubject::all();
                $items = $subjects->map(function($subject) {
                    return [
                        'title' => $subject->title,
                        'image' => $subject->image,
                        'description' => $subject->description,
                        'category' => $subject->element_type,
                        'interest_level' => $subject->power_level,
                        'created_at' => $subject->created_at
                    ];
                })->toArray();
            } else {
                // For external APIs
                $response = Http::get($apiUrl);
                $data = $response->json();
                $items = isset($data['data']) ? $data['data'] : $data;
            }
            
            return view('favorite-subjects.api-search', [
                'items' => $items,
                'apiUrl' => $apiUrl
            ]);
        } catch (\Exception $e) {
            return view('favorite-subjects.api-search', [
                'error' => 'Failed to fetch data from the API: ' . $e->getMessage(),
                'apiUrl' => $apiUrl
            ]);
        }
    }
} 