@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Pokémon</h1>

    <form action="{{ route('favorite-subjects.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Pokémon Name</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image URL</label>
            <input type="text" class="form-control" id="image" name="image" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="element_type" class="form-label">Element Type</label>
            <select class="form-select" id="element_type" name="element_type" required>
                <option value="Fire">Fire</option>
                <option value="Water">Water</option>
                <option value="Grass">Grass</option>
                <option value="Electric">Electric</option>
                <option value="Psychic">Psychic</option>
                <option value="Fighting">Fighting</option>
                <option value="Dark">Dark</option>
                <option value="Fairy">Fairy</option>
                <option value="Dragon">Dragon</option>
                <option value="Steel">Steel</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="power_level" class="form-label">Power Level (1-1000)</label>
            <input type="number" class="form-control" id="power_level" name="power_level" min="1" max="1000" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Pokémon</button>
    </form>
</div>
@endsection 