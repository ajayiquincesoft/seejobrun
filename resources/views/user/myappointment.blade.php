@extends('user.layout.userdashboard')
@section('content')
<div class="container-fluid content ">
        <div class="row">
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
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-primary text-center  add-new-job-btn" data-toggle="modal"
                        data-target="#AddAppointment">
                        Add Appointment</button>
                </div>
                <!-- Appointment calernder -->
                <div class="col-md-6 col-lg-4 col-sm-6">
                    <h5 class="text-white">My Appointments</h5>
                    <div class="mt-4  ">
                        <div class="calendar-container shadow rounded table-responsive">
                            <div id="myappointment"></div>
                        </div>
                    </div>
                </div>
                <!-- Appointment Tables -->
                <div class="col-md-6 col-lg-8 col-sm-6">
                    <div>
                        <h5 class="text-white" id="heading_text">Today's Appointments</h5>
                        <div class="table-responsive">
                            <table class="table  table-bordered  bg-white jobs-table mt-4">
                                <thead class="jobs-thead">
                                    <tr>
                                        <th style="width: 40%;">Meeting Title</th>
                                        <th style="width: 22%;">Start</th>
                                        <th style="width: 22%;">End</th>
                                        <th  style="width: 16%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body" id="date_events">
                                    <!-- First Row -->
                                 
                                    @if($Todaysevents->count()>0)
                                        @foreach($Todaysevents as $Todayevent)
                                        <tr>
                                            <td>
                                                <strong>{{ ucwords($Todayevent->title )}}</strong><br>
                                                <small>{{ $Todayevent->description }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($Todayevent->startdate)->isoFormat('dddd, h:mm A') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($Todayevent->enddate)->isoFormat('dddd, h:mm A') }}</td>
                                            <td>
                                                <button class="action-button action-delete" data-id="{{ $Todayevent->id }}" title="Delete">
                                                    <i class="fa-solid fa-x"></i>
                                                </button>
                                                <button class="action-button action-edit" data-toggle="modal"
                                                    data-target="#appointmentModal{{ $Todayevent->id }}" title="Edit">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <div class="modal fade" id="appointmentModal{{ $Todayevent->id }}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h5 class="modal-title title-model" id="modalLabel1">Edit
                                                                    Appointment</h5>
                                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>

                                                            <!-- Modal Body -->
                                                            <div class="modal-body">
                                                                <form method="post" action="{{ route('EditEvent') }}">
                                                                    @csrf 
                                                                    <div class="row">
                                                                        <div class="col-md-12 my-2">
                                                                            <input type="hidden" name="id" value="{{ $Todayevent->id }}" >
                                                                            <input class="form-control form-control-sm" type="text" name="title" value="{{ $Todayevent->title }}" placeholder="Meeting Title *" required>
                                                                        </div>
                                                                        <div class="col-md-12 my-2">
                                                                            <input type="text" name="startdate" value="{{ \Carbon\Carbon::parse(@$Todayevent->startdate)->format('M d, Y, g:i A') }}" class="form-control form-control-sm" placeholder="Start date" required>


                                                                        </div>
                                                                        <div class="col-md-12 my-2">
                                                                            <input type="text" name="enddate" value="{{ \Carbon\Carbon::parse(@$Todayevent->enddate)->format('M d, Y, g:i A') }}" class="form-control form-control-sm" placeholder="End date" required>
                                                                        </div>
                                                                        <div class="col-md-12 my-2">
                                                                            <select name="notification_alert" class="form-select form-select-sm text-muted" required>
                                                                                <option selected disabled>Reminders</option>
                                                                                <option value="5" {{ ($Todayevent->notification_alert == 5) ? 'selected' : ' ' }}>5 minutes before</option>
                                                                                <option value="10" {{ ($Todayevent->notification_alert == 10) ? 'selected' : ' ' }}>10 minutes before</option>
                                                                                <option value="15" {{ ($Todayevent->notification_alert == 15) ? 'selected' : ' ' }}>15 minutes before</option>
                                                                                <option value="30" {{ ($Todayevent->notification_alert == 30) ? 'selected' : ' ' }}>30 minutes before</option>
                                                                                <option value="60" {{ ($Todayevent->notification_alert == 60) ? 'selected' : ' ' }}>1 hour before</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-12 my-2">
                                                                            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Description">{{ $Todayevent->description }}</textarea>
                                                                        <div class="col-md-12">
                                                                            <button type="submit"
                                                                                class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                                                Update</button>
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
                                    @else
                                        <tr><td colspan="4"> There are no appointment for this date.</td></tr>
                                   @endif 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Add Appointment Model Pop up -->
                <div class="modal fade" id="AddAppointment">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title title-model" id="modalLabel1">Add New
                                    Appointment</h5>
                                <button type="button" class="btn-close" data-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form method="post" action="{{ route('addevent') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 my-2">
                                            <input type="text" name="title" value="" class="form-control form-control-sm"  placeholder="Meeting Title *" required>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <input id="startdate" type="text" name="startdate" class="form-control form-control-sm" placeholder="Start Date *"  required>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <input id="enddate" type="text" name="enddate" class="form-control form-control-sm" placeholder="End Date *" required>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <select name="notification_alert" class="form-select form-select-sm text-muted" required>
                                                <option selected disabled>Reminders</option>
                                                <option value="5"> 5 minutes before</option>
                                                <option value="10">10 minutes before</option>
                                                <option value="15">15 minutes before</option>
                                                <option value="30">30 minutes before</option>
                                                <option value="60">1 hour before</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 my-2">
                                            <textarea name="description" class="form-control form-control-sm"  rows="3" placeholder="Description"></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit"
                                                class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@section('script')
        <script>
            $(function () {
                $('#startdate').datetimepicker({
                    format: 'MMM DD, YYYY, hh:mm A', // Format for start date with time
                    icons: {
                        time: "fas fa-clock",
                        date: "fas fa-calendar",
                        up: "fas fa-arrow-up",
                        down: "fas fa-arrow-down",
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'fas fa-calendar-check-o',
                        clear: 'fas fa-trash',
                        close: 'fas fa-times'
                    },
                    useCurrent: false // Important for preventing issues with end date
                });
        
                $('#enddate').datetimepicker({
                    format: 'MMM DD, YYYY, hh:mm A', // Format for end date with time
                    icons: {
                        time: "fas fa-clock",
                        date: "fas fa-calendar",
                        up: "fas fa-arrow-up",
                        down: "fas fa-arrow-down",
                        previous: 'fas fa-chevron-left',
                        next: 'fas fa-chevron-right',
                        today: 'fas fa-calendar-check-o',
                        clear: 'fas fa-trash',
                        close: 'fas fa-times'
                    },
                    useCurrent: false // Important for preventing issues with start date
                });
        
                // Ensure end date can't be before start date
                $("#startdate").on("change.datetimepicker", function (e) {
                    $('#enddate').datetimepicker('minDate', e.date);
                });
                $("#enddate").on("change.datetimepicker", function (e) {
                    $('#startdate').datetimepicker('maxDate', e.date);
                });
            });
        </script>
    <script>
       
        $(document).ready(function() {
                $('#myappointment').fullCalendar({
                    firstDay: 1,
                    businessHours: false,
                    defaultView: 'month',
                    showNonCurrentDates: true,
                    fixedWeekCount: false,
                    contentHeight: "auto",
                    handleWindowResize: true,
                    themeSystem: 'bootstrap4',
                    editable: false,   // Disable event dragging
                    header: {
                        center: 'title',
                        left: 'prev',
                        right: 'next'
                    },
                events: [
                    @foreach($events as $event)
                        @php
                            $startDate = \Carbon\Carbon::parse($event->startdate);
                            $endDate = \Carbon\Carbon::parse($event->enddate);
                            $dates = [];
                            
                            // Loop through each day, including the end date's time
                            while ($startDate->lte($endDate)) {
                                // Include the end time on the last day
                                if ($startDate->isSameDay($endDate)) {
                                    $dates[] = [
                                        'date' => $startDate->format('Y-m-d'),
                                        'start' => $startDate->format('H:i:s'),
                                        'end' => $endDate->format('H:i:s')
                                    ];
                                } else {
                                    $dates[] = [
                                        'date' => $startDate->format('Y-m-d'),
                                        'start' => $startDate->format('H:i:s'),
                                        'end' => '23:59:59' // Event runs the whole day
                                    ];
                                }
                                $startDate->addDay()->startOfDay();
                            }
                        @endphp

                        @foreach($dates as $date)
                            {
                                title: '{{ @$event->title }}',
                                start: '{{ $date['date'] }}T{{ $date['start'] }}',  
                                icon: 'circle',
                            },
                        @endforeach
                    @endforeach

                            ],
                            eventRender: function(event, element) {
                                if(event.icon) {
                                    element.find(".fc-content").prepend("<i class='fa fa-" + event.icon + "'></i>");
                                    element.find(".fc-title").hide();
                                    element.find(".fc-time").hide();
                                }
                                    var currentEvents = jQuery('#myappointment').fullCalendar('clientEvents', function(ev) {
                                    return ev.start.format('YYYY-MM-DD') === event.start.format('YYYY-MM-DD') && ev.rendering !== 'background';
                                });
                                // If there's more than one visible event for this date, hide this event
                                if (currentEvents.length > 1 && event._id !== currentEvents[0]._id) {
                                    currentEvents.slice(1).forEach(function(ev) {
                                        jQuery('#myappointment').fullCalendar('removeEvents', ev._id);
                                    });
                                }
                          },
                                     // Handle event clicks
                        eventClick: function(event, jsEvent, view) {
                            $('#heading_text').html('');
                            // alert('Event: ' + event.start+'title->' + event.title);
                            $('.fc-event').removeClass('highlighted-event');
                                // Add highlight to the clicked event
                            $(this).addClass('highlighted-event');
                            
                            var date1 = event.start.format();
                           
                            var title = event.title;
                            $('#heading_text').html(moment(date1).format('MMM D, YYYY h:mm A'));
                            jQuery.ajax(
                            {
                                type: "POST",
                                url: "{{route('geteventsbydate')}}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    'event_date': date1,
                                },
                                success: function(res) 
                                {
                                    
                                    $('#date_events').html('');
                                    var pq = 1;
                        
                                    if((res.Events.length > 0) ){
                                        for (var i = 0; i < res.Events.length; i++) {
                                            var event = res.Events[i]; 
                                            var event_id = event.id; 
                                            var event_title = event.title; 
                                            var event_notification_alert = event.notification_alert; 
                                            var event_startdate =moment(event.startdate).format('MMM D, YYYY, h:mm A');
                                            var event_enddate = moment(event.enddate).format('MMM D, YYYY, h:mm A');
                                    
                                            var event_description = event.description;

                                            $('#date_events').append(`
                                                    <tr>
                                                        <td>
                                                            <strong style="text-transform: capitalize;">${event_title}</strong><br>
                                                            <small style="text-transform: capitalize;">${(event_description==null)? ' ': event_description}</small>
                                                        </td>
                                                        <td>${event_startdate}</td>
                                                        <td>${event_enddate}</td>
                                                        <td>
                                                            <button class="action-button action-delete" data-id="${event_id}" title="Delete">
                                                                <i class="fa-solid fa-x"></i>
                                                            </button>
                                                            <button class="action-button action-edit" data-toggle="modal" data-target="#appointmentModal${event_id}" title="Edit">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </button>
                                                        
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="appointmentModal${event_id}">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title title-model" id="modalLabel1">Edit Appointment</h5>
                                                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <!-- Modal Body -->
                                                                <div class="modal-body">
                                                                    <form method="post" action="{{ route('EditEvent') }}">
                                                                        @csrf
                                                                        <div class="row">
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="hidden" name="id" value="${event_id}">
                                                                                <input class="form-control form-control-sm" type="text" name="title" value="${event_title}" placeholder="Meeting Title *" required>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="text" name="startdate" value="${ moment(event.startdate).format('MMM D, YYYY, h:mm A')}" class="form-control form-control-sm" placeholder="Start date">
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="text" name="enddate" value="${ moment(event.enddate).format('MMM D, YYYY, h:mm A')}" class="form-control form-control-sm" placeholder="End date" required>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <select class="form-select form-select-sm text-muted" name="notification_alert">
                                                                                    <option selected disabled>Reminders</option>
                                                                                    <option>No Reminder</option>
                                                                                     <option value="5" ${(event_notification_alert==5)?'selected':''}>5 minutes before</option>
                                                                                    <option value="10" ${(event_notification_alert==10)?'selected':''}>10 minutes before</option>
                                                                                    <option value="15" ${(event_notification_alert==15)?'selected':''}>15 minutes before</option>
                                                                                    <option value="30" ${(event_notification_alert==30)?'selected':''}>30 minutes before</option>
                                                                                    <option value="60" ${(event_notification_alert==60)?'selected':''}>1 hour before</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Description">${event_description}</textarea>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                                                    Update
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </td>
                                                    </tr>
                                                    `);

                                            
                                                pq++;
                                            } 
                                    
                                    }else{

                                        $('#date_events').append('<tr><td colspan="3">There are no appointment for this date.</td></tr>');

                                        }
 
                                }
                            });

                        },
                       dayClick: function(date, jsEvent, view) {
                        $('#heading_text').html();
                           var clickedDate = date.format();
                          
                           $('.fc-day').removeClass('highlighted');  // Get the clicked date in 'YYYY-MM-DD' format
                           $(this).addClass('highlighted');
           
                            //var date1 = event.start.format();
                            //var title = event.title;
                            $('#heading_text').html(moment(clickedDate).format('MMM D, YYYY h:mm A'));
                            
                            jQuery.ajax(
                            {
                                type: "POST",
                                url: "{{route('geteventsbydate')}}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    'event_date': clickedDate,
                                },
                                success: function(res) 
                                {
                                    
                                    $('#date_events').html('');
                                    var pq = 1;
                        
                                    if((res.Events.length > 0) ){
                                        for (var i = 0; i < res.Events.length; i++) {
                                            var event = res.Events[i]; 
                                            var event_id = event.id; 
                                            var event_title = event.title; 
                                            var event_notification_alert = event.notification_alert; 
                                            var event_startdate =moment(event.startdate).format('MMM D, YYYY, h:mm A');
                                            var event_enddate = moment(event.enddate).format('MMM D, YYYY, h:mm A');
                                    
                                            var event_description = event.description;

                                            $('#date_events').append(`
                                                    <tr>
                                                        <td>
                                                            <strong style="text-transform: capitalize;">${event_title}</strong><br>
                                                            <small style="text-transform: capitalize;">${(event_description==null)? ' ': event_description}</small>
                                                        </td>
                                                        <td>${event_startdate}</td>
                                                        <td>${event_enddate}</td>
                                                        <td>
                                                            <button class="action-button action-delete" data-id="${event_id}" title="Delete">
                                                                <i class="fa-solid fa-x"></i>
                                                            </button>
                                                            <button class="action-button action-edit" data-toggle="modal" data-target="#appointmentModal${event_id}" title="Edit">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </button>
                                                        
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="appointmentModal${event_id}">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title title-model" id="modalLabel1">Edit Appointment</h5>
                                                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <!-- Modal Body -->
                                                                <div class="modal-body">
                                                                    <form method="post" action="{{ route('EditEvent') }}">
                                                                        @csrf
                                                                        <div class="row">
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="hidden" name="id" value="${event_id}">
                                                                                <input class="form-control form-control-sm" type="text" name="title" value="${event_title}" placeholder="Meeting Title *" required>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="text" name="startdate" value="${ moment(event.startdate).format('MMM D, YYYY, h:mm A')}" class="form-control form-control-sm" placeholder="Start date">
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <input type="text" name="enddate" value="${ moment(event.enddate).format('MMM D, YYYY, h:mm A')}" class="form-control form-control-sm" placeholder="End date" required>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <select class="form-select form-select-sm text-muted" name="notification_alert">
                                                                                    <option selected disabled>Reminders</option>
                                                                                    <option>No Reminder</option>
                                                                                     <option value="5" ${(event_notification_alert==5)?'selected':''}>5 minutes before</option>
                                                                                    <option value="10" ${(event_notification_alert==10)?'selected':''}>10 minutes before</option>
                                                                                    <option value="15" ${(event_notification_alert==15)?'selected':''}>15 minutes before</option>
                                                                                    <option value="30" ${(event_notification_alert==30)?'selected':''}>30 minutes before</option>
                                                                                    <option value="60" ${(event_notification_alert==60)?'selected':''}>1 hour before</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-12 my-2">
                                                                                <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Description">${event_description}</textarea>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                                                    Update
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </td>
                                                    </tr>
                                                    `);

                                            
                                                pq++;
                                            } 
                                    
                                    }else{

                                        $('#date_events').append('<tr><td colspan="3">There are no appointment for this date.</td></tr>');

                                        }
 
                                }
                            });
                       }

                       
                   });
           
                
           
               });
           
    </script> 
   <script>
    $(document).on('click', '.action-delete', function () {
        const eventId = $(this).data('id'); // Get the event ID
        var button = $(this); // Get the row of the event

        // Confirm deletion
        if (confirm('Are you sure you want to delete this event?')) {
            $.ajax({
                url: '{{ route('DeleteEvent') }}', // Use your named route
                type: 'POST', // Specify the request type
                data: {
                    id: eventId, // Send the event ID to the server
                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                },
                success: function(response) {
                    // Handle success response
                    alert(response.message);
                    button.closest('tr').remove();  // Remove the event from the DOM
                },
                error: function(xhr) {
                    // Handle error response
                    alert(xhr.responseJSON.error || 'An error occurred while deleting the event.');
                }
            });
        }
    });
</script>   
 @stop