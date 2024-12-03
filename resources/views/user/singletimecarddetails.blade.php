@extends('user.layout.userdashboard')
@section('content')
        <div class="container-fluid content ">
            <div class="row supreme-container">
                <div class="col-md-12">
                    @if ($errors->any())
                       <div class="alert alert-danger">
                           <ul>
                               @foreach ($errors->all() as $error)
                               <li>{{ $error }}</li>
                               @endforeach
                           </ul>
                       </div>
                       @endif
                       @if(session('success'))
                       <div class="alert alert-success">{{session('success')}}</div>
                       @endif
               </div>
                @php
                //echo 'helloo->>'. $_GET['tdate'];   
                @endphp
                <div class="col-md-6">
                    <h5 class="text-white">{{ $job_name }} - {{ \Carbon\Carbon::parse($_GET['tdate'])->format('M d, Y') }}</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ url()->previous() }}" class="back_btn"><< Back</a>
                </div>
                <div class="col-md-6 col-lg-4 mt-2 ">
                    <div class="bg-white">
                        <div class="table-scrollable">
                            <table class="table  table-bordered  bg-white jobs-table ">
                                <thead class="jobs-thead">
                                    <tr>
                                        <th>Clock In </i></th>
                                        <th>Clock Out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    <!-- First Row -->
                                    @foreach($timesheet as $timesheets)
                                    <tr>
                                        <th>{{ date('g:i A', strtotime($timesheets->clockin)) }} <span class="mx-2"><i style="color: #286FAC;"
                                                    class="fa-solid fa-location-pin"></i></span><span class="mx-1"><a
                                                    class="text-decoration-none" href="javascript:void(0);" onclick="zoomOutMap()"><small>(Show in
                                                        Map)</small></a></span></th>
                                        <td>{{ date('g:i A', strtotime($timesheets->clockout)) }}</td>
                                        <td>
                                            <button class="action-button action-edit" data-toggle="modal"
                                                data-target="#clockin{{ $timesheets->id }}">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            <div class="modal fade" id="clockin{{ $timesheets->id }}" tabindex="-1" role="dialog"
                                                aria-labelledby="modalLabel1" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title title-model" id="modalLabel1">Edit
                                                                Time Card</h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="post" action="{{ route('editClockinClockouts') }}">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <input type="hidden" name="id" value="{{ $timesheets->id }}">
                                                                        <label for=""><strong>Clock In</strong></label>
                                                                        <input id="startTime{{ $timesheets->id }}" class="form-control form-input-sm" type="text" name="clockin" value="{{ date('g:i:s A', strtotime($timesheets->clockin)) }}">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for=""><strong>Clock Out</strong></label>
                                                                        <input id="endTime{{ $timesheets->id }}" class="form-control form-input-sm" type="text" name="clockout" value="{{ date('g:i:s A', strtotime($timesheets->clockout)) }}">
                                                                    </div>
                                                                    <div class="col-md-12 mt-2">
                                                                        <button type="submit" class="w-100  btn bg-286FAC">Save</button>
                                                                    </div>
                                                                </div>
                                                                
                                                                
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                        <th>Description of injury</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    @foreach($timesheet as $timesheets)
                                    <tr>
                                        <td><small>{{ $timesheets->description }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8 mt-2">
                    <div class="map" id="map" style="width: 100%; height: 450px;">
                        
                    </div>
                    
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
        @foreach($timesheet as $map)
        { lat: {{ $map->clockin_latitude }}, lng: {{ $map->clockin_longitude }}, date: "{{ $map->tdate}}" },
        @endforeach
    ];

    const clockoutLocations = [
        @foreach($timesheet as $map)
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