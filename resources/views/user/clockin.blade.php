@extends('user.layout.userdashboard')
@section('content')
    <div class="container-fluid content ">
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
            <div class="row supreme-container">
                <div class="col-md-6 col-lg-4">
                    <h5 class="text-white">Clock In and Out</h5>
                    <div class="bg-white p-3">
                        <form id="jobForm" action="" method="POST">
                            <div class="mb-3">
                                <select class="form-select" name="job_id" id="jobSelect">

                                    <option value="">Select Job</option>
                                    @foreach($jobdata as $jobd)
                                         <option value="{{ $jobd->id }}">{{ $jobd->name }}</option>
                                    @endforeach
                                </select>
                                <div id="jobSelectError" class="error-message text-danger"></div>
                            </div>
                            <!-- Date Range Selection -->
                            <div class="date-picker mb-3">
                                <div class="input-group">
                                    <input type="text" name="from_date" class="form-control" id="startDate" placeholder="Start Date">
                                </div>
                                <span> - </span>
                                <div class="input-group">
                                    <input type="text" name="to_date"  class="form-control" id="endDate" placeholder="End Date">
                                </div>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" id="submitbtn">Submit</button>
                            </div>
                        </form>
                    </div>
                    @php 
                    //print_r($data);
                    @endphp
                  @if($data)
                    <div class="bg-white mt-4">
                        <div>
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="text-034078 fw-600 font-18 m-0 text-start">Clock Out Here</p>
                                </div>
                                        <div class="mb-3 mt-3">
                                            <h5>
                                                @foreach($jobdata as $jobd)
                                                    {{ ($data['job_id']==$jobd->id)?$jobd->name:'' }} 
                                                @endforeach
                                            </h5> 
                                        </div>
                                <div class="circle-clock-out">
                                    <div>
                                        <span class="font-18">{{ $data['totalhours'] }}</span><br>
                                        <small>Day Total</small>
                                    </div>
                                </div>
                                <div class="my-2">
                                    <button class="btn custom-color check-out-hover" id="timeclockout">Clock Out</button>
                                </div>
                                <div class="modal fade" id="clockout">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title title-model" id="modalLabel1">Were You Injured
                                                    Today?</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('UpdateClockout') }}" id="updateclockout">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="custom-radio-popup">
                                                                <div class="form-check">
                                                                    <input type="hidden" name="id" value="{{ $data['id'] }}">
                                                                    <input type="hidden" name="job_id" value="{{ $data['job_id'] }}">
                                                                    <input class="form-check-input" type="radio" name="injoyed" id="yes" value="1">
                                                                    <label class="form-check-label" for="yes">Yes</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="injoyed" id="no" value="0" checked="">
                                                                    <label class="form-check-label" for="no">No</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <textarea name="description" class="form-control" rows="4" placeholder="Description" id=""></textarea>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                                Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white mt-4">
                        <div>
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="text-034078 fw-600 font-18 m-0 text-start">Clock In Here</p>
                                </div>
                                <form method="POST" action="{{ route('Addclockins') }}" id="clockinform"> <!-- Update action with your route -->
                                    @csrf <!-- Add CSRF token for security if using Laravel -->
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-10">
                                            <div class="mb-2 mt-2">
                                                <select class="form-select" name="job_id" id="jobSelect2">
                                                    <option value="">Select Job</option>
                                                    @foreach($jobdata as $jobd)
                                                        <option value="{{ $jobd->id }}">{{ $jobd->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div id="jobSelectError2" class="error-message text-danger"></div>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- Hidden fields to store clock-in date, time, latitude, and longitude -->
                                    <input type="hidden" name="tdate" id="clockinDate">
                                    <input type="hidden" name="clockin_time" id="clockinTime">
                                    <input type="hidden" name="clockin_latitude" id="clockinLatitude">
                                    <input type="hidden" name="clockin_longitude" id="clockinLongitude">
                                
                                    <div class="circle-clock-in">
                                        <div>
                                            <span class="font-18">0hrs 0mins</span><br>
                                            <small>Day Total</small>
                                        </div>
                                    </div>
                                    <div class="my-2">
                                        <button type="button" id="clockInButton" class="btn bg-Complete px-3">Clock In</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                  @endif
                </div>
                <div class="col-md-6 col-lg-8 mt-2">
                    <div class="table-scrollable">
                        <table class="table  table-bordered  bg-white jobs-table mt-4">
                            <thead class="jobs-thead">
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="jobs-table-body" id="timecarddetails">

                                <!-- First Row -->
                                @if(session('clockdetails'))
                                        @foreach(session('clockdetails') as $clockdetail)
                                            <!-- Display clock detail properties here -->
                                           
                                            <tr>
                                                <th>{{ $clockdetail->tdate }}</th>
                                                <td>{{ date('g:i A', strtotime($clockdetail->clockin)) }}</td>
                                                <td>{{ date('g:i A', strtotime($clockdetail->clockout)) }}</td>
                                                <td>
                                                    <div class="text-286FAC text-center">
                                                    <a href="{{ route('GettimeSheetDetails') }}?job_id={{ $clockdetail->job_id }}&tdate={{ $clockdetail->tdate }}">
                                                        <i class="fa-solid fa-greater-than arraow-Button"></i>
                                                    </a>
                                                    </div>
                                                </td>
                                            </tr>

                                        @endforeach

                                    @else
                                    <tr><td colspan="5">There is no record found.</td></tr>
                                    @endif 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @section('script')
    <script>

        jQuery(function () {
                jQuery('#startDate').datetimepicker({format:'MMM DD, YYYY'});
                jQuery('#endDate').datetimepicker({format:'MMM DD, YYYY'});
            });
        
            $(document).ready(function() {
            // Function to submit the form via AJAX
                $('#submitbtn').on('click', function() {
                    let job_id = $('#jobSelect').val();
                    if (!job_id) {
                        $('#jobSelectError').html('Please select a job.'); 
                        return; 
                    } else {
                        $('#jobSelectError').html(''); 
                    }

                    let formData = {
                        jobcontact_id: $('input[name="jobcontact_id"]').val(),
                        job_id: $('#jobSelect').val(),
                        from_date: $('#startDate').val(),
                        to_date: $('#endDate').val(),
                        _token: '{{ csrf_token() }}' // Include CSRF token if necessary
                    };
        
                    $.ajax({
                        url: '{{ route("getAllClocks") }}', // Update with the correct route
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log(response);
                            $('#timecarddetails').html(''); // Clear the table first
                            $('#totalhours').html('');
                            //$('#totalhours').html(response.totalhours);
                                if (response.tsheetclockins.length > 0) {
                                    const timesheetEntries = response.tsheetclockins;
                                    const totalHours = response.totalhours;
                                    timesheetEntries.forEach(entry => {
                                        var job_id = entry.job_id;
                                        var tdate = entry.tdate;
                                        //var clockin = entry.clockin;
                                        //var clockout = entry.clockout;
                                        var clockin = moment(entry.clockin, "HH:mm:ss").format('hh:mm A');
                                        if (entry.clockout === null || entry.clockout === undefined){
                                            var clockout='-:-'
                                        }else{
                                        var clockout = moment(entry.clockout, "HH:mm:ss").format('hh:mm A');
                                        }
                                        const formattedDate = moment(tdate).format('MMMM D, YYYY');
                                        
                                        // Use .append() to add each row to the table without overwriting previous rows
                                        $('#timecarddetails').append(`
                                            <tr>
                                                <th>${formattedDate}</th>
                                                <td>${clockin}</td>
                                                <td>${clockout}</td>
                                                <td>
                                                    <div class="text-286FAC text-center">
                                                    <a href="{{ route('GettimeSheetDetails') }}?job_id=${job_id}&tdate=${tdate}">
                                                        <i class="fa-solid fa-greater-than arraow-Button"></i>
                                                    </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        `);
                                    });
                                } else {
                                    $('#timecarddetails').html('<tr><td colspan="6">There is no record found</td></tr>');
                                }
        
                         
                        },
                        error: function(xhr, status, error) {
                          
                            $('#jobSelectError').html('');
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                if (errors.job_id) {
                                    $('#jobSelectError').html(errors.job_id[0]); 
                                }
                                
                            } else {
                                alert('An unexpected error occurred.'); 
                            }
                        }
                    });
                });
        
            // Trigger form submission when a job is selected
          
        let today = new Date();
        
        // Set start date to the first day of the current month
        let startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        $('#startDate').val(startDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short', // 'short' for 3-letter month abbreviation
            day: '2-digit'
        }));
        
        // Set end date to the last day of the current month
        let endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        $('#endDate').val(endDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit'
        }));
        });
         
</script>
<script>

    document.getElementById('clockInButton').addEventListener('click', function() {
    // Get current date and time
    let job_id = $('#jobSelect2').val();
        if (!job_id) {
            $('#jobSelectError2').html('Please select a job.'); 
            return; 
        } else {
            $('#jobSelectError2').html(''); 
        }
    const FormattedDate = moment().format('YYYY-MM-DD'); // User's local date in YYYY-MM-DD format
    const FormattedTime = moment().format('HH:mm:ss');
   
    // Set date and time values to the hidden inputs
   document.getElementById('clockinDate').value = FormattedDate;
    document.getElementById('clockinTime').value = FormattedTime;

    // Get geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // Set latitude and longitude values
            document.getElementById('clockinLatitude').value = position.coords.latitude;
            document.getElementById('clockinLongitude').value = position.coords.longitude;

            // Submit the form after setting all required fields
            document.getElementById('clockinform').submit();
        }, function(error) {
            alert('Geolocation is required for clocking in.');
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
});

</script>
<script>
    // Get the user's current date, latitude, and longitude
    $('#timeclockout').on('click', function() {
        //alert('hello');
        // Get the current date
        $('#clockout').modal('show');
        const tdate = new Date().toISOString().slice(0, 10);

        // Check if geolocation is available
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const clockout_latitude = position.coords.latitude;
                const clockout_longitude = position.coords.longitude;

                // Add these fields to the #updateclockout container
                $('#updateclockout').append(`
                    <input type="hidden" name="tdate" value="${tdate}">
                    <input type="hidden" name="clockout_latitude" value="${clockout_latitude}">
                    <input type="hidden" name="clockout_longitude" value="${clockout_longitude}">
                `);
               
            }, function(error) {
                console.error('Error getting location: ', error);
                alert('Unable to retrieve location.');
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    });
</script>
@stop