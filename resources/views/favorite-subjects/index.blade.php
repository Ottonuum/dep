@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Favorite Pokémon</h1>
        <a href="{{ route('favorite-subjects.create') }}" class="btn btn-primary">Add New Pokémon</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($subjects as $subject)
            <div class="col-md-4 mb-4">
                <div class="card h-100" data-id="{{ $subject->id }}">
                    <div class="card-img-container" style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                        <img src="{{ $subject->image }}" 
                             class="card-img-top" 
                             alt="{{ $subject->title }}"
                             style="max-height: 100%; width: auto; object-fit: contain;">
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $subject->title }}</h5>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="card-text">{{ $subject->description }}</p>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-primary">{{ $subject->element_type }}</span>
                            <span class="badge bg-danger">Power: {{ $subject->power_level }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- (modal and scripts here) -->
@endsection