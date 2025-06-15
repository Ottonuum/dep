@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Interactive Map</h2>
                        <div class="search-box">
                            <form id="search-form" class="d-flex">
                                <input type="text" name="query" class="form-control me-2" placeholder="Search city...">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Marked Locations</h3>
                </div>
                <div class="card-body">
                    <div id="markers-list">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        width: 100%;
        height: 600px;
        z-index: 1;
    }
    .search-box {
        min-width: 300px;
    }
    .marker-item {
        background-color: #f8f9fa;
    }
    .marker-item:hover {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let markers = [];
        const markersList = document.getElementById('markers-list');
        let map;
        let searchedCities = new Set();

        // Initialize the map
        function initMap() {
            if (map) {
                map.remove();
            }
            
            map = L.map('map').setView([0, 0], 2);

            // Add the OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add click event to add markers
            map.on('click', async function(e) {
                const locationName = await getLocationName(e.latlng.lat, e.latlng.lng);
                addMarker(e.latlng.lat, e.latlng.lng, locationName);
            });

            // Add a marker for the user's location if available
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    
                    const locationName = await getLocationName(userLat, userLng);
                    addMarker(userLat, userLng, locationName);
                    map.setView([userLat, userLng], 13);
                });
            }
        }

        // Initialize map on page load
        initMap();

        // Function to get location name from coordinates
        async function getLocationName(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`);
                const data = await response.json();
                const address = data.address || {};
                
                return address.city || 
                       address.town || 
                       address.village || 
                       address.suburb || 
                       data.display_name;
            } catch (error) {
                console.error('Error getting location name:', error);
                return 'Unknown Location';
            }
        }

        // Function to add marker to the map and list
        function addMarker(lat, lng, name, fullAddress = '') {
            // Check if we already have a marker for this city
            const existingMarker = markers.find(m => m.name === name);
            if (existingMarker) {
                // Update the existing marker's position
                existingMarker.marker.setLatLng([lat, lng]);
                existingMarker.lat = lat;
                existingMarker.lng = lng;
                existingMarker.fullAddress = fullAddress;
                
                // Update the marker in the list
                const markerDiv = markersList.querySelector(`[data-city="${name}"]`);
                if (markerDiv) {
                    markerDiv.querySelector('.lat').textContent = `Latitude: ${lat}`;
                    markerDiv.querySelector('.lng').textContent = `Longitude: ${lng}`;
                    if (fullAddress) {
                        markerDiv.querySelector('.address').textContent = fullAddress;
                    }
                }
                return;
            }

            const marker = L.marker([lat, lng])
                .addTo(map)
                .bindPopup(name)
                .openPopup();
            
            markers.push({
                marker: marker,
                lat: lat,
                lng: lng,
                name: name,
                fullAddress: fullAddress
            });

            addMarkerToList(marker, lat, lng, name, fullAddress);
        }

        // Function to add marker to the list
        function addMarkerToList(marker, lat, lng, name, fullAddress = '') {
            const markerDiv = document.createElement('div');
            markerDiv.className = 'marker-item mb-3 p-2 border rounded';
            markerDiv.setAttribute('data-city', name);
            markerDiv.innerHTML = `
                <h5>${name}</h5>
                <p class="mb-1 lat">Latitude: ${lat}</p>
                <p class="mb-1 lng">Longitude: ${lng}</p>
                ${fullAddress ? `<p class="mb-1 small text-muted address">${fullAddress}</p>` : ''}
                <button class="btn btn-danger btn-sm delete-marker" 
                        data-lat="${lat}" 
                        data-lng="${lng}">
                    Delete
                </button>
            `;
            markersList.appendChild(markerDiv);

            // Add delete functionality
            markerDiv.querySelector('.delete-marker').addEventListener('click', function() {
                map.removeLayer(marker);
                markerDiv.remove();
                markers = markers.filter(m => m.lat !== lat || m.lng !== lng);
                searchedCities.delete(name);
            });
        }

        // Handle search form submission
        document.getElementById('search-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const query = this.querySelector('input[name="query"]').value;
            
            try {
                const response = await fetch(`/map/search?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const results = await response.json();
                
                // Add new markers without clearing existing ones
                if (results.length > 0) {
                    const firstResult = results[0];
                    map.setView([firstResult.lat, firstResult.lng], 13);
                    
                    results.forEach(result => {
                        if (!searchedCities.has(result.name)) {
                            searchedCities.add(result.name);
                            addMarker(
                                parseFloat(result.lat),
                                parseFloat(result.lng),
                                result.name,
                                result.full_address
                            );
                        }
                    });
                }
            } catch (error) {
                console.error('Error searching locations:', error);
            }
        });
    });
</script>
@endpush
@endsection 