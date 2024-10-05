@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="container-fluid">
        <div id='map' style='width: 1000px; height: 500px;'></div>
    </div>
    <div class="container-fluid ">
        <button id="sendLocation" class="btn btn-primary mt-1">Save changes</button>
    </div>

    <script>
        
        mapboxgl.accessToken = 'pk.eyJ1IjoiaG9hbmcyNjA1IiwiYSI6ImNtMW96a3lrcDE3ODcycm42ZHQ3aTA0enQifQ.nHsboTLl8JSA-aApCUpL1Q';
        var map = new mapboxgl.Map({
          container: 'map',
          style: 'mapbox://styles/mapbox/streets-v12',
          zoom: 13,
          center: [106.599385, 10.770822],
          // center: [-122.662323, 45.523751],
        });
    
        map.addControl(new mapboxgl.NavigationControl());
    
        let currentMarker = null;
        let coordinates = null;

        map.on('click', function (e) {
            coordinates = e.lngLat;
            
            if (currentMarker) {
                currentMarker.remove();
            }

            currentMarker = new mapboxgl.Marker()
                .setLngLat(coordinates) 
                .addTo(map); 
        });

        var user_id = {{ $user_id }};

        document.getElementById('sendLocation').addEventListener('click', function() {
            if (coordinates) {
                fetch('/location/add', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        
                    },
                    body: JSON.stringify({
                        id: user_id,
                        latitude: coordinates.lat,
                        longitude: coordinates.lng
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/user-profile/update'; 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } 
        });
    
    </script>
    
</div>
@endsection