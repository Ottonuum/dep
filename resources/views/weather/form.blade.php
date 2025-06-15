@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Weather Search</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('weather.search') }}" method="GET" class="mb-4">
                    <div class="input-group">
                    <input type="text" name="city" class="form-control" placeholder="Enter city name..." required>
                    <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    </form>
                    
                    <div class="popular-cities mt-4">
                    <h4>Popular Cities</h4>
                    <div class="row">
                    <div class="col-md-3">
                    <a href="{{ route('weather.search', ['city' => 'Tallinn']) }}" class="btn btn-outline-primary btn-block mb-2">Tallinn</a>
                    </div>
                    <div class="col-md-3">
                    <a href="{{ route('weather.search', ['city' => 'Tartu']) }}" class="btn btn-outline-primary btn-block mb-2">Tartu</a>
                    </div>
                    <div class="col-md-3">
                    <a href="{{ route('weather.search', ['city' => 'Pärnu']) }}" class="btn btn-outline-primary btn-block mb-2">Pärnu</a>
                    </div>
                    <div class="col-md-3">
                    <a href="{{ route('weather.search', ['city' => 'Narva']) }}" class="btn btn-outline-primary btn-block mb-2">Narva</a>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 