
@extends('layouts.user_type.auth')

@section('content')
  <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
    
    <div id='map' style='width: 1000px; height: 500px;'></div>
    
  </div>

  <script>
    mapboxgl.accessToken = 'pk.eyJ1IjoiaG9hbmcyNjA1IiwiYSI6ImNtMW96a3lrcDE3ODcycm42ZHQ3aTA0enQifQ.nHsboTLl8JSA-aApCUpL1Q';
    var map = new mapboxgl.Map({
      container: 'map',
      style: 'mapbox://styles/mapbox/streets-v12',
      zoom: 13,
      center: [106.599385,
      10.770822],
      // center: [-122.662323, 45.523751],
    });

    map.addControl(new mapboxgl.NavigationControl());



    map.on('click', function (e) {
    const coordinates = e.lngLat;
    console.log('Kinh độ:', coordinates.lng, 'Vĩ độ:', coordinates.lat);

    new mapboxgl.Marker()
        .setLngLat(coordinates) // Đặt vị trí của marker
        .addTo(map); // Th
});



      </script>

@endsection

