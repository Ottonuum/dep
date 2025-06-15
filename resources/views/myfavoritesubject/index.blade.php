@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">My Favorite Pokemon</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Search Pokemon</h5>
                        <div class="input-group">
                            <input type="text" id="pokemonSearch" class="form-control" placeholder="Enter Pokemon name...">
                            <button class="btn btn-primary" id="searchButton">Search</button>
                        </div>
                        <div id="pokemonResult" class="mt-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img id="pokemonImage" src="" alt="" class="img-fluid">
                                        </div>
                                        <div class="col-md-8">
                                            <h5 id="pokemonName"></h5>
                                            <p><strong>Types:</strong> <span id="pokemonTypes"></span></p>
                                            <p><strong>Abilities:</strong> <span id="pokemonAbilities"></span></p>
                                            <button class="btn btn-success" id="addButton">Add to Favorites</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pokemon.store') }}" id="pokemonForm" class="mb-4" style="display: none;">
                        @csrf
                        <input type="hidden" name="name" id="formName">
                        <input type="hidden" name="image" id="formImage">
                        <input type="hidden" name="type" id="formType">
                        <input type="hidden" name="description" id="formDescription">
                    </form>

                    <div class="list-group">
                        @forelse($subjects as $subject)
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="{{ $subject['image'] }}" alt="{{ $subject['name'] }}" class="img-fluid">
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="mb-1">{{ $subject['name'] }}</h5>
                                        <p class="mb-1">{{ $subject['description'] }}</p>
                                        <small>Type: {{ $subject['type'] }}</small>
                                        <div class="mt-2">
                                            <a href="{{ route('pokemon.edit', $subject['id']) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('pokemon.destroy', $subject['id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">No Pokemon added yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 