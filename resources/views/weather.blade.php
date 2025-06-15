@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Weather Information</h2>
                    <a href="{{ route('weather.form') }}" class="btn btn-outline-primary">Search Another City</a>
                </div>
                <div class="card-body">
                    @if(isset($error))
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @elseif(isset($weather))
                        <div class="weather-info">
                            <div class="text-center mb-4">
                                <h3>{{ $weather['name'] }}, {{ $weather['sys']['country'] }}</h3>
                                <div class="weather-icon">
                                    <img src="http://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png" 
                                         alt="{{ $weather['weather'][0]['description'] }}"
                                         class="img-fluid">
                                </div>
                                <div class="temperature">
                                    <h1>{{ round($weather['main']['temp']) }}°C</h1>
                                    <p class="text-capitalize">{{ $weather['weather'][0]['description'] }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="weather-detail">
                                        <i class="fas fa-temperature-high"></i>
                                        <span>Feels like: {{ round($weather['main']['feels_like']) }}°C</span>
                                    </div>
                                    <div class="weather-detail">
                                        <i class="fas fa-temperature-low"></i>
                                        <span>Min: {{ round($weather['main']['temp_min']) }}°C</span>
                                    </div>
                                    <div class="weather-detail">
                                        <i class="fas fa-temperature-high"></i>
                                        <span>Max: {{ round($weather['main']['temp_max']) }}°C</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="weather-detail">
                                        <i class="fas fa-tint"></i>
                                        <span>Humidity: {{ $weather['main']['humidity'] }}%</span>
                                    </div>
                                    <div class="weather-detail">
                                        <i class="fas fa-wind"></i>
                                        <span>Wind: {{ $weather['wind']['speed'] }} m/s</span>
                                    </div>
                                    <div class="weather-detail">
                                        <i class="fas fa-compress-arrows-alt"></i>
                                        <span>Pressure: {{ $weather['main']['pressure'] }} hPa</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <small class="text-muted">
                                    Last updated: {{ now()->format('H:i:s') }}
                                    (Data is cached for 30 minutes)
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.weather-info {
    padding: 20px;
}
.weather-icon img {
    width: 100px;
    height: 100px;
}
.temperature h1 {
    font-size: 3rem;
    margin: 10px 0;
}
.weather-detail {
    margin: 15px 0;
    font-size: 1.1rem;
}
.weather-detail i {
    width: 25px;
    margin-right: 10px;
    color: #666;
}
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection 