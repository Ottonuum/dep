@extends('layouts.app')

@section('content')
<div class="container">
    <div class="map-container">
        <div id="map"></div>
    </div>
    <div class="sidebar">
        <h1>Map Locations</h1>
        <div class="marker-form">
            <h2>Search Location</h2>
            <input type="text" id="location-search" placeholder="Search for a city...">
            <button id="search-button">Search</button>
            <div id="search-results"></div>
        </div>

        <h2>Saved Locations</h2>
        <ul class="marker-list">
            @foreach ($markers as $marker)
            <li class="marker-item" data-lat="{{ $marker['latitude'] }}" data-lon="{{ $marker['longitude'] }}">
                <h3>{{ $marker['name'] }}</h3>
                <p>{{ $marker['description'] }}</p>
                <div class="marker-actions">
                    <button class="edit-marker" data-id="{{ $marker['id'] }}">Edit</button>
                    <button class="delete-marker" data-id="{{ $marker['id'] }}">Delete</button>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodRQ+cZRjE1V2T63K6+Kj1j6F5j7r3J3m8t6t5n7w1f7g5s0a5g5t5e5u5v5w5x5y5z5A=" crossorigin="">
    <style>
    html, body, #app, .container, .map-container {
        height: 100%;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
        }
        .map-container {
            flex: 3;
        }
        .sidebar {
            flex: 1;
            background-color: #f5f5f5;
            padding: 20px;
            overflow-y: auto;
        }
        #map {
            height: 100%;
            width: 100%;
        }
        .marker-form {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .marker-form input, .marker-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .marker-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .marker-list {
            list-style: none;
            padding: 0;
        }
        .marker-item {
            background-color: white;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .marker-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
        }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-ftWd4S83K7l3G+A0o5N2Y7g0P7J8w1k1B2D9E2G3N4C5D6E7F8H9I0J1K2L3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z7A=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = {};

        function addMarkerToMap(id, lat, lon, name, description) {
            var marker = L.marker([lat, lon]).addTo(map)
                .bindPopup(`<b>${name}</b><br>${description}<br><button class="edit-marker-btn" data-id="${id}">Edit</button> <button class="delete-marker-btn" data-id="${id}">Delete</button>`).openPopup();
            markers[id] = marker;
            return marker;
        }

        // Load existing markers
        @foreach ($markers as $marker)
            addMarkerToMap('{{ $marker['id'] }}', {{ $marker['latitude'] }}, {{ $marker['longitude'] }}, '{{ $marker['name'] }}', '{{ $marker['description'] }}');
        @endforeach

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lon = e.latlng.lng;

            axios.get(`/map/reverse-geocode?lat=${lat}&lon=${lon}`)
                .then(function (response) {
                    const locationName = response.data.display_name || `Lat: ${lat.toFixed(2)}, Lon: ${lon.toFixed(2)}`;
                    const description = response.data.description || 'No description';

                    var markerName = prompt("Enter a name for this location:", locationName);
                    if (markerName) {
                        var markerDescription = prompt("Enter a description for this location:", description);
                        axios.post('{{ route('map.store') }}', {
                            name: markerName,
                            description: markerDescription,
                            latitude: lat,
                            longitude: lon,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(function (response) {
                            if (response.data.success) {
                                const newMarker = response.data.marker;
                                addMarkerToMap(newMarker.id, newMarker.latitude, newMarker.longitude, newMarker.name, newMarker.description);
                                updateSidebar(newMarker);
                            }
                        })
                        .catch(function (error) {
                            console.error("Error adding marker:", error);
                            alert("Failed to add marker.");
                        });
                    }
                })
                .catch(function (error) {
                    console.error("Error during reverse geocoding:", error);
                    alert("Failed to get location name.");
                });
        });

        document.getElementById('search-button').addEventListener('click', function() {
            const query = document.getElementById('location-search').value;
            axios.get(`/map/search?query=${query}`)
                .then(function (response) {
                    const resultsDiv = document.getElementById('search-results');
                    resultsDiv.innerHTML = '';
                    response.data.forEach(result => {
                        const p = document.createElement('p');
                        p.innerHTML = `<a href="#" data-lat="${result.lat}" data-lon="${result.lon}" data-name="${result.name}">${result.name}</a>`;
                        p.addEventListener('click', function(e) {
                            e.preventDefault();
                            map.setView([result.lat, result.lon], 13);
                        });
                        resultsDiv.appendChild(p);
                    });
                })
                .catch(function (error) {
                    console.error("Error during search:", error);
                    alert("Failed to search location.");
                });
        });

        document.querySelector('.marker-list').addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-marker')) {
                const id = e.target.dataset.id;
                const listItem = e.target.closest('.marker-item');
                const currentName = listItem.querySelector('h3').textContent;
                const currentDescription = listItem.querySelector('p').textContent;

                const newName = prompt("Edit name:", currentName);
                if (newName !== null) {
                    const newDescription = prompt("Edit description:", currentDescription);
                    if (newDescription !== null) {
                        axios.put(`/map/update/${id}`, {
                            name: newName,
                            description: newDescription,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(response => {
                            if (response.data.success) {
                                listItem.querySelector('h3').textContent = newName;
                                listItem.querySelector('p').textContent = newDescription;
                                if (markers[id]) {
                                    markers[id].setPopupContent(`<b>${newName}</b><br>${newDescription}<br><button class="edit-marker-btn" data-id="${id}">Edit</button> <button class="delete-marker-btn" data-id="${id}">Delete</button>`);
                                }
                            }
                        })
                        .catch(error => console.error("Error updating marker:", error));
                    }
                }
            } else if (e.target.classList.contains('delete-marker')) {
                const id = e.target.dataset.id;
                if (confirm("Are you sure you want to delete this marker?")) {
                    axios.delete(`/map/destroy/${id}`, {
                        data: { _token: '{{ csrf_token() }}' }
                    })
                    .then(response => {
                        if (response.data.success) {
                            e.target.closest('.marker-item').remove();
                            if (markers[id]) {
                                map.removeLayer(markers[id]);
                                delete markers[id];
                            }
                        }
                    })
                    .catch(error => console.error("Error deleting marker:", error));
                }
            }
        });

        function updateSidebar(marker) {
            const list = document.querySelector('.marker-list');
            const listItem = document.createElement('li');
            listItem.classList.add('marker-item');
            listItem.dataset.lat = marker.latitude;
            listItem.dataset.lon = marker.longitude;
            listItem.innerHTML = `
                <h3>${marker.name}</h3>
                <p>${marker.description}</p>
                <div class="marker-actions">
                    <button class="edit-marker" data-id="${marker.id}">Edit</button>
                    <button class="delete-marker" data-id="${marker.id}">Delete</button>
                </div>
            `;
            list.appendChild(listItem);
        }
    });
    </script>
@endpush