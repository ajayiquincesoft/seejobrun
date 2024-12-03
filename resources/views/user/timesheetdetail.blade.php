@extends('user.layout.userdashboard')
@section('content')
<div class="container-fluid content ">
    @php
      //print_r($clockdetails);  
    @endphp
            <div class="row supreme-container">
                <div class="col-md-6">
                    <h5 class="text-white">{{ ucwords($jobdetails->name) }} - {{ \Carbon\Carbon::parse($_GET['tdate'])->format('M d, Y') }}</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ url()->previous() }}" class="back_btn"><< Back</a>
                </div>
                
                <div class="col-md-6 col-lg-4 mt-2 ">
                    <div class="bg-white">
                        <div class="table-scrollable">
                            <table class="table  table-bordered  bg-white jobs-table ">
                                <thead class="jobs-thead">
                                    <tr rowspan="3" style="border-bottom: 1px solid #000;">
                                        <th style="border:0">Hours for this Job only </th><th style="border:0" class="text-end">{{ $totalhours }}</th> 
                                    </tr>
                                </thead>
                               
                                <thead class="jobs-thead">
                                    <tr>
                                        <th>Clock In </i></th>
                                        <th>Clock Out</th>
                                       
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    <!-- First Row -->
                                    
                                    @foreach($clockdetails as $timesheets)
                                        <tr>
                                            <th>{{ date('g:i A', strtotime($timesheets->clockin)) }} <span class="mx-2"><i style="color: #286FAC;"
                                                        class="fa-solid fa-location-pin"></i></span><span class="mx-1"><a
                                                        class="text-decoration-none" href="javascript:void(0);" onclick="zoomOutMap()"><small>(Show in
                                                            Map)</small></a></span></th>
                                            <td>
                                                @if($timesheets->clockout)
                                                {{ date('g:i A', strtotime($timesheets->clockout)) }}
                                                @else
                                                -:-
                                                @endif
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="table-scrollable m-0 p-0">
                            <table class="table  table-bordered  bg-white jobs-table ">
                                <thead class="jobs-thead">
                                    <tr>
                                        <th>Description of work </th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    @php
                                    $desc='';
                                    @endphp
                                    @foreach($clockdetails as $timesheets)
                                    @php
                                     $desc=$timesheets->description;
                                    @endphp
                                    @endforeach
                                        <tr>
                                            <td><small>{{  $desc }}</small></td>
                                        </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8 mt-2">
                    <div class="map" id="map" style="width: 100%; height: 450px;"></div>
                </div>
            </div>
        </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @section('script')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGesttfRUXxo9ekf4TqhbndTh6EKkOqX4"></script>
    <script>
        let map; // Define map globally so it can be accessed by zoomOutMap function
        
        // Arrays for clockin and clockout locations with latitude, longitude, and date
        const clockinLocations = [
            @foreach($clockdetails as $map)
            { lat: {{ $map->clockin_latitude }}, lng: {{ $map->clockin_longitude }}, date: "{{ $map->tdate}}" },
            @endforeach
        ];
    
        const clockoutLocations = [
            @foreach($clockdetails as $map)
            { lat: {{ $map->clockout_latitude }}, lng: {{ $map->clockout_longitude }}, date: "{{ $map->tdate }}" },
            @endforeach
        ];
    
        // Initialize the map
        function initMap() {
            // Center map at the first location if it exists
            if (clockinLocations.length > 0) {
                const center = { lat: clockinLocations[0].lat, lng: clockinLocations[0].lng };
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 4,
                    center: center
                });
    
                // Add clockin markers with blue color
                clockinLocations.forEach((location) => {
                    const position = { lat: location.lat, lng: location.lng };
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: "Clock-in",
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png" // Blue marker for clockin
                        }
                    });
    
                    // Display clockin date on marker click
                    const infowindow = new google.maps.InfoWindow();
                    marker.addListener("click", () => {
                        infowindow.setContent(`<p><strong>${location.date}</strong><br>Clock-in</p>`);
                        infowindow.open(map, marker);
                    });
                });
    
                // Add clockout markers with red color
                clockoutLocations.forEach((location) => {
                    const position = { lat: location.lat, lng: location.lng };
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: "Clock-out",
                        icon: {
                            url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png" // Red marker for clockout
                        }
                    });
    
                    // Display clockout date on marker click
                    const infowindow = new google.maps.InfoWindow();
                    marker.addListener("click", () => {
                        infowindow.setContent(`<p><strong>${location.date}</strong><br>Clock-out</p>`);
                        infowindow.open(map, marker);
                    });
                });
            } else {
                console.error("No locations found.");
            }
        }
    
        // Function to zoom out the map
        function zoomOutMap() {
            if (map) {
                map.setZoom(10); // Adjust the zoom level (lower zoom level zooms out)
            }
        }
    
        // Initialize the map on page load
        window.onload = initMap;
    </script>
@stop