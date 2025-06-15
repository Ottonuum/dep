<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorite Subject - Pokemon</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Removed search-suggestions styles as real-time suggestions are removed */
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">Pokemon</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Add New Pokemon to Local List</h2>
                        <form action="{{ route('pokemon.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <input type="text" name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                Add My Pokemon Manually
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Search Pokemon (from PokeAPI)</h2>
                        <form action="{{ route('myfavoritesubject') }}" method="GET" class="mb-4 flex space-x-2">
                            <input type="text" 
                                   name="search" 
                                   id="pokemonSearch"
                                   placeholder="Search Pokemon by name..." 
                                   value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="mt-1 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                Search
                            </button>
                        </form>

                        @if(request('search') && empty($pokemons))
                            <div class="col-span-3 text-center py-8">
                                <p class="text-gray-500">No Pokemon found for "{{ request('search') }}". Try a different search term.</p>
                            </div>
                        @elseif(!request('search'))
                            <div class="col-span-3 text-center py-8">
                                <p class="text-gray-500">Enter a Pokemon name in the search bar to find it from the PokeAPI.</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pokemons as $pokemon)
                                <div class="bg-purple-100 p-4 rounded-lg">
                                    @if(isset($pokemon['image']))
                                        <img src="{{ $pokemon['image'] }}" alt="{{ $pokemon['name'] }}" class="w-32 h-32 mx-auto mb-4">
                                    @endif
                                    <h3 class="font-bold text-lg mb-2 text-center">{{ $pokemon['name'] }}</h3>
                                    <p class="mb-2 text-center">Type: {{ $pokemon['type'] }}</p>
                                    <p class="mb-2 text-center">{{ $pokemon['description'] }}</p>
                                    
                                    @if(isset($pokemon['abilities']))
                                        <div class="mb-2">
                                            <p class="text-center font-semibold">Abilities:</p>
                                            <div class="flex flex-wrap justify-center gap-2">
                                                @foreach($pokemon['abilities'] as $ability)
                                                    <span class="px-2 py-1 bg-blue-200 rounded-full text-sm">{{ $ability }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($pokemon['height']) || isset($pokemon['weight']))
                                        <div class="mb-2 text-center">
                                            @if(isset($pokemon['height']))
                                                <span class="mr-4">Height: {{ $pokemon['height'] }}m</span>
                                            @endif
                                            @if(isset($pokemon['weight']))
                                                <span>Weight: {{ $pokemon['weight'] }}kg</span>
                                            @endif
                                        </div>
                                    @endif

                                    @if(isset($pokemon['stats']))
                                        <div class="mt-2">
                                            <p class="text-center font-semibold mb-1">Base Stats:</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                @foreach($pokemon['stats'] as $stat)
                                                    <div class="text-sm">
                                                        <span class="font-medium">{{ $stat['name'] }}:</span>
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(($stat['value'] / 255) * 100, 100) }}%"></div>
                                                        </div>
                                                        <span class="text-xs">{{ $stat['value'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-4 text-center">
                                        @if($pokemon['is_added'])
                                            <span class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">Already Added</span>
                                        @else
                                            <form action="{{ route('pokemon.add-api') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="name" value="{{ $pokemon['name'] }}">
                                                <input type="hidden" name="type" value="{{ $pokemon['type'] }}">
                                                <input type="hidden" name="description" value="{{ $pokemon['description'] }}">
                                                <input type="hidden" name="image" value="{{ $pokemon['image'] }}">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition">
                                                    Add to My Pokemon
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">My Locally Added Pokemon</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse(\App\Models\Pokemon::all() as $pokemon)
                                <div class="bg-purple-100 p-4 rounded-lg">
                                    @if(isset($pokemon['image_url']))
                                        <img src="{{ $pokemon['image_url'] }}" alt="{{ $pokemon['name'] }}" class="w-32 h-32 mx-auto mb-4">
                                    @endif
                                    <h3 class="font-bold text-lg mb-2 text-center">{{ $pokemon['name'] }}</h3>
                                    <p class="mb-2 text-center">Type: {{ $pokemon['type'] }}</p>
                                    <p class="mb-2 text-center">{{ $pokemon['description'] }}</p>
                                    <p class="text-sm text-gray-600 text-center">Added by: {{ $pokemon['added_by'] }}</p>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-8">
                                    <p class="text-gray-500">No Pokemon added to your local list yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Removed JavaScript for real-time suggestions -->
</body>
</html> 