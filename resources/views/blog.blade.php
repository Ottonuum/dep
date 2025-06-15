<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Map</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .map-container {
            flex: 3;
            height: 100%;
            position: relative;
        }
        .sidebar {
            flex: 1;
            background-color: #f5f5f5;
            padding: 20px;
            overflow-y: auto;
        }
        h1, h2 {
            color: #333;
            margin-top: 0;
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
        .btn-edit {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-cancel {
            background-color: #9e9e9e;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
            border-radius: 4px;
        }
        .hidden {
            display: none;
        }
        .info-box {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="map-container">
            <div id="map"></div>
        </div>
        <div class="sidebar">
            <h2>Markers</h2>
            <div class="marker-form" id="add-marker-form">
                <h3>Add Marker</h3>
                <form id="markerForm">
                    <input type="text" id="name" placeholder="Name" required>
                    <input type="number" id="latitude" placeholder="Latitude" step="any" required>
                    <input type="number" id="longitude" placeholder="Longitude" step="any" required>
                    <textarea id="description" placeholder="Description"></textarea>
                    <button type="submit">Add Marker</button>
                </form>
            </div>
            
            <div class="marker-form hidden" id="edit-marker-form">
                <h3>Edit Marker</h3>
                <form id="editForm">
                    <input type="hidden" id="edit-id">
                    <input type="text" id="edit-name" placeholder="Name" required>
                    <input type="number" id="edit-latitude" placeholder="Latitude" step="any" required>
                    <input type="number" id="edit-longitude" placeholder="Longitude" step="any" required>
                    <textarea id="edit-description" placeholder="Description"></textarea>
                    <div class="marker-actions">
                        <button type="submit" class="btn-edit">Update</button>
                        <button type="button" class="btn-cancel" id="cancelEdit">Cancel</button>
                    </div>
                </form>
        </div>
    </div>    
    <script>
    </script>
</body>
</html>