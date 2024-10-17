@extends('layouts.user_type.auth')

@section('content')

  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-lg-8">
        <div class="row">
          <div class="col-xl-6 mb-xl-0 mb-4">
            <div class="card bg-transparent shadow-xl">
              <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url(''); max-height:300px  "> 
                <a href="{{ asset('assets/img/requests-photos/'.$requests->photo) }}" target="_blank" style="cursor: zoom-in">
                  <img src="{{ asset('assets/img/requests-photos/'.$requests->photo) }}" alt="No Image">
              </a>
              </div>
            </div>
          </div>
          <div class="col-xl-6">
            <div class="row">
              <div class="col-md-6">
                <div class="card" style="min-height:300px">
                  <div class="card-header mx-4 p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                      <i class="fas fa-user opacity-10"></i>
                    </div>
                  </div>
                  <div class="card-body pt-0 p-3 text-center">
                    <h6 class="text-center mb-0">Customer</h6>
                    <span class="text-xs">{{ $requests->customer->username }}</span>
                    <hr class="horizontal dark my-3">
                    {{-- <h5 class="mb-0">{{ $requests->customer->email }}</h5> --}}
                    <span class="text-xs">{{  $requests->customer->email }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mt-md-0 mt-4">
                <div class="card" style="min-height:300px" >
                  <div class="card-header mx-4 p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                      <i class="fas fa-tools opacity-10"></i>
                    </div>
                  </div>
                  <div class="card-body pt-0 p-3 text-center">
                    <h6 class="text-center mb-0">Technician</h6>
                    <span class="text-xs">{{  $requests->technician->username }}</span>
                    <hr class="horizontal dark my-3">
                    {{-- <h5 class="mb-0">{{  $requests->technician->email }}</h5> --}}
                    <span class="text-xs">{{  $requests->technician->email }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-lg-0 mb-4">
            <div class="card mt-4">
              <div class="card-header pb-0 p-3">
                <div class="row">
                  <div class="col-6 d-flex align-items-center">
                    <h6 class="mb-0">Location</h6>
                  </div>
                  <div class="col-6 text-end">
                  </div>
                </div>
              </div>
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-md-6">
                  </div>
                </div>
                <div id="map" style="background-color:black; height: 100vh; z-index: 0;"></div>
              <script>
               var map = L.map("map").setView([{{ $requests->latitude }}, {{ $requests->longitude }}], 13); 
              L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { 
              maxZoom: 19,
              attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',}).addTo(map);
              L.marker([{{ $requests->latitude }}, {{ $requests->longitude }}]).addTo(map)
              .bindPopup('Requested location')
              .openPopup();
              </script>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-6 d-flex align-items-center">
                <h6 class="mb-0">Information</h6>
              </div>
            </div>
          </div>
          <div class="card-body p-3 pb-0">
            Request: {{ $requests->service->name }} <br>
            Price: {{ $requests->service->price }} VND <br>
            <span>Description: <pre>{{ $requests->description }}</pre></span>
          </div>
        </div>
      </div>
    </div>
  </div>
 
@endsection

