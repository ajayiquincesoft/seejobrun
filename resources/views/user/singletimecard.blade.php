@extends('user.layout.userdashboard')
@section('content')
<div class="container-fluid content ">
            <div class="row supreme-container">
                <div class="col-md-6">
                    <h5 class="text-white">
                        {{ $contact->name }}
                    </h5>
                </div>
               
                <div class="col-md-6 text-end">
                    <a href="{{ url()->previous() }}" class="back_btn"><< Back</a>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="bg-white p-3 rounded-top  mt-2">
                        <form id="jobForm" action="" method="POST">
                            <!-- Job Selection Dropdown -->
                            <input type="hidden" name="jobcontact_id" class="form-control" value="{{ $employee_id }}">
                            <div class="mb-3">
                                <select class="form-select" name="jobid" id="jobSelect">
                                    <option selected disabled>Select Job</option>
                                    <option value='0'>All Jobs</option>
                                    @foreach($getjob as $jobdetails)
                                            <option value="{{$jobdetails->job->id}}">{{$jobdetails->job->name}}</option>
                                    @endforeach
                                   
                                </select>
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
                    <div class="bg-white mt-4">
                        <div>
                            <div class="card text-center">
                                <div class="card-header">
                                    <p class="text-034078 fw-600 font-18 m-0 text-start">Job Day Total</p>
                                </div>
                                <div class="circle-clock-timecard">
                                    <div>
                                        <span class="font-18" id="totalhours">0hrs 0mins</span><br>
                                        <small>Day Total</small>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-8">
                    <div class="table-scrollable rounded-top">
                        <table class="table  table-bordered  bg-white jobs-table mt-2">
                            <thead class="jobs-thead">
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Day's Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="jobs-table-body" id="timecarddetails">
                                <!-- First Row -->
                                <tr>
                                   
                                    <td colspan="6">There is no record founded.</td>
                                   
                                </tr>

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
            let formData = {
                jobcontact_id: $('input[name="jobcontact_id"]').val(),
                jobid: $('#jobSelect').val(),
                from_date: $('#startDate').val(),
                to_date: $('#endDate').val(),
                _token: '{{ csrf_token() }}' // Include CSRF token if necessary
            };

            $.ajax({
                url: '{{ route("getTimeCardsInfo") }}', // Update with the correct route
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#timecarddetails').html(''); // Clear the table first
                    $('#totalhours').html('');
                    $('#totalhours').html(response.totalhours);
                        if (response.timesheet.length > 0) {
                            const timesheetEntries = response.timesheet;
                            const totalHours = response.totalhours;
                            timesheetEntries.forEach(entry => {
                                var job_id = entry.job_id;
                                var tdate = entry.tdate;
                                var clockin = entry.clockin;
                                var clockout = entry.clockout;
                                var daytotal = entry.daytotal;
                                const formattedDate = moment(tdate).format('MMMM D, YYYY');
                                
                                // Use .append() to add each row to the table without overwriting previous rows
                                $('#timecarddetails').append(`
                                    <tr>
                                        <th>${formattedDate}</th>
                                        <td>${clockin}</td>
                                        <td>${clockout}</td>
                                        <th>${daytotal}</th>
                                        <td>
                                            <div class="text-286FAC text-center">
                                            <a href="{{ route('singletimecarddetails') }}?jobcontact_id={{ $employee_id }}&tdate=${tdate}">
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
                    // Handle error response
                    alert('An error occurred while submitting the form.');
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
@stop
