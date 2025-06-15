@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">API Search</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('api.search') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="url" 
                                   class="form-control" 
                                   name="api_url" 
                                   value="{{ $apiUrl ?? '' }}" 
                                   placeholder="Enter API URL"
                                   required>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>

                    @if(isset($error))
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($items) && count($items) > 0)
                <div class="row">
                    @foreach($items as $item)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                @if(isset($item['image']))
                                    <img src="{{ $item['image'] }}" 
                                         class="card-img-top" 
                                         alt="{{ $item['title'] ?? 'Image' }}"
                                         style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item['title'] ?? 'No Title' }}</h5>
                                    <p class="card-text">{{ $item['description'] ?? 'No Description' }}</p>
                                    
                                    @if(isset($item['category']))
                                        <span class="badge bg-primary">{{ $item['category'] }}</span>
                                    @endif
                                    
                                    @if(isset($item['interest_level']))
                                        <span class="badge bg-info">{{ $item['interest_level'] }}</span>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        Created: {{ isset($item['created_at']) ? \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(isset($items) && count($items) === 0)
                <div class="alert alert-info">
                    No items found in the API response.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
}

.badge {
    margin-right: 5px;
}

.input-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control:focus {
    box-shadow: none;
    border-color: #80bdff;
}
</style>
@endsection 