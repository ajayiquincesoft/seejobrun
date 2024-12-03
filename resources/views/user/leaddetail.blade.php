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
        </div>
    </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <h5 class="job_ttle">
                        {{ $lead->deal_name }}
                    </h5>
                </div>
               
                <form method="POST" action="{{ route('DeleteLead') }}">

                    @csrf 
                    <input type="hidden" name="lead_id" value=" {{ $lead->id }}">
                    <button class="delete-jobs mt-2">Delete All Lead Information</button>

                </form>
            </div>
            <div class="row py-4">
                <div class="col-md-12">
                    <div class="tabs bg-white">
                        <button class="tab-button active" onclick="openTab(event, 'tab1')">TO DO</button>
                        <button class="tab-button" onclick="openTab(event, 'tab2')">GENERAL</button>
                    </div>
                </div>
            </div> 
           
            <div class="row">
                <div class="col-md-12">

                    {{-- To DO TAB --}}

                     <div class="tab-content1 active pt-4 " id="tab1">
                      <div class="todlist">
                        <!-- Sections Container -->
                        <div class="tasks_bg">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="newSection" placeholder="Add New Section">
                                <button class="btn btn-success" id="addSectionBtn" type="button">Add Section</button>
                            </div>
                            <div id="sectionsContainer">
                                <!-- Sections will be dynamically added here -->
                                    @foreach($allsections as $allsection)
                                        <div class="card mb-3" id="section-{{ $allsection->id }}">
                                            <div class="card-header bg-secondary text-white  justify-content-between">
                                                <div class="row">
                                                <div class="col-md-8">
                                                    <h5 class="section-name">{{ $allsection->sec_name }}</h5>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <button class="btn btn-sm btn-success edit-section-btn" data-section-id="{{ $allsection->id }}" title="Edit section"><i class="fa-solid fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-danger delete-section-btn" data-section-id="{{ $allsection->id }}" title="Delete section"><i class="fa-solid fa-x"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="input-group">
                                                    <button class="btn btn-primary showAddTaskbtn showAddTaskFormBtn{{ @$allsection->id }} mt-2 mb-3" type="button">Add Task</button>
                                                </div>
                                                <div class="input-group mb-2 mt-2 addTaskForm{{ @$allsection->id }}" style="display: none;">
                                                    <input type="text" class="form-control newTaskInput" placeholder="Task Name">
                                                    <input type="text" id="startdate{{ @$allsection->id }}" class="form-control newTaskEndDate" placeholder="End Date">
                                                    <textarea class="form-control newTaskDescription" placeholder="Task Description"></textarea>
                                                    <button class="btn btn-success addTaskBtn" data-section-id="{{ $allsection->id }}" type="button">Add Task</button>
                                                    <script>
                                                        jQuery(function () {
                                                            jQuery('.showAddTaskFormBtn{{ @$allsection->id }}').on('click', function() {
                                                                    jQuery('.addTaskForm{{ @$allsection->id }}').toggle();
                                                                    $('.showAddTaskFormBtn{{ @$allsection->id }}').hide(); // Toggle visibility of form
                                                                });
                                                            
                                                                jQuery('#startdate{{ @$allsection->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                            });
                                                    </script>
                                                </div>
                                                <ul class="list-group taskList">
                                                    @if(@$allsection->todosectiontask->count() > 0)
                                                    @foreach($allsection->todosectiontask as $task)
                                                        <li class="list-group-item d-flex justify-content-between" id="task-{{ $task->id }}" data-task-id="{{ $task->id }}">
                                                            <div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input task-status" type="checkbox" data-task-id="{{ $task->id }}" {{ ($task->status == 1) ? 'checked' : '' }}>
                                                                    <label class="form-check-label label_txt  {{ ($task->status == 1) ? 'generaltaskcompleted' : '' }}">{{ $task->task_name }}</label>
                                                                </div>
                                                                @if($task->enddate != '0000-00-00 00:00:00' && \Carbon\Carbon::parse($task->enddate)->isValid())
                                                                    <small>Due: {{ \Carbon\Carbon::parse($task->enddate)->format('M d, Y') }}</small>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <!-- Edit button triggers modal -->
                                                                <button class="btn btn-sm btn-success edit-task-btn" title="Edit task" data-toggle="modal" data-target="#edittask{{ $task->id }}">
                                                                    <i class="fa-solid fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger delete-task-btn" data-task-id="{{ $task->id }}" title="Delete task">
                                                                    <i class="fa-solid fa-x"></i>
                                                                </button>
                                                
                                                                <!-- Modal Structure -->
                                                                <div class="modal fade" id="edittask{{ $task->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $task->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title title-model" id="modalLabel{{ $task->id }}">{{ ucwords($task->task_name) }}</h5>
                                                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <!-- Form content for editing the task can go here -->
                                                                                <form method="POST" action="{{ route('updateToDoSectionTask') }}">
                                                                                    @csrf
                                                                                    <div class="form-group mt-2">
                                                                                        <label for="task-name-{{ $task->id }}">Task Name</label>
                                                                                        <input type="hidden" name="todosectask_id" class="form-control"  value="{{ $task->id }}">
                                                                                        <input type="text" name="task_name" class="form-control"  value="{{ $task->task_name }}">
                                                                                    </div>
                                                                                    <div class="form-group mt-2">
                                                                                        <label for="task-date-{{ $task->id }}">End Date</label>
                                                                                        <input type="text" name="enddate" class="form-control" id="edittaskdate{{ $task->id }}" value="{{ \Carbon\Carbon::parse($task->enddate)->format('M d, Y') }}">
                                                                                    </div>
                                                                                    <div class="form-group mt-2">
                                                                                        <label for="task-description-{{ $task->id }}">Description</label>
                                                                                        <textarea name="description" class="form-control"  rows="3">{{ $task->description }}</textarea>
                                                                                    </div>
                                                                                    <div class="form-group mt-2">
                                                                                        <label for="task status">Task Status </label>
                                                                                        <select class="form-control" name="status">
                                                                                            
                                                                                            <option value="1" {{ ($task->status==1)?'selected':'' }}>Completed</option>
                                                                                            <option value="0" {{ ($task->status==0)?'selected':'' }}>Not Completed</option>
                                                                                        </select>
                                                                                    </div> 
                                                                                    <div class="form-group mt-2">
                                                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                                                    </div>
                                                                            </div>
                                                                        
                                                                            
                                                                        
                                                                        </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            <script>
                                                                jQuery(function () {
                                                                        jQuery('#edittaskdate{{ $task->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                                    });
                                                            </script>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                            </div>
                                        </div>
                                    @endforeach

                            </div>
                        </div>
                     </div>

                      </div>

                  {{-- To DO TAB --}}


                      <div class="tab-content1 " id="tab2">
                        <div class="row">
                            <div class="col-md-12 col-lg-8">
                                <h5 class="text-white">General </h5>
                                <div class="row bg-white pt-4 mt-3 position-relative pb-4">
                                    <div class="col-md-3 d-flex">
                                        <img src="{{ asset('assets') }}/images/UserIcon.png" alt="User Image" class="genral-icon-image">
                                        <div class="client-details">
                                            <p class="p-0 m-0 client-label">Client Name</p>
                                            <p class="p-0 m-0 client-label-value">{{ $lead->name}}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex">
                                        
                                        <img src="{{ asset('assets') }}/images/phoneicon.png" alt="Phone Icon" class="genral-icon-image">
                                        <div class="client-details">
                                            <p class="p-0 m-0 client-label">Client’s Phone</p>
                                           <a href="tel:{{ $lead->mobile }}"> <p class="p-0 m-0 client-label-value">{{ $lead->mobile }}</p></a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex">
                                        
                                        <img src="{{ asset('assets') }}/images/Emailicon.png" alt="Email Icon" class="genral-icon-image">
                                        <div class="client-details">
                                            <p class="p-0 m-0 client-label">Email Address</p>
                                            <a href="mailto:{{ $lead->lead_email }}"><p class="p-0 m-0 client-label-value">{{ $lead->lead_email }}</p></a>
                                        </div>
                                    </div>
                                    <div class="col-md-1 position-absolute edit-icon-jobs mt-2" id="editgeneral" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </div>
                                </div>
                                
                                <div class="row bg-white">
                                    <div class="col-md-4 border-top-right py-5 text-center">
                                        <img src="{{ asset('assets') }}/images/Loaction.png" alt="Location Icon" class="genral-icon-image">
                                        <div class="client-details">
                                            <p class="p-0 m-0 client-label">Client Address</p>
                                            <p class="p-0 m-0 client-label-value">{{ $lead->address }} {{ $lead->city }} , {{ $lead->state }} {{ $lead->pincode }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 border-top-right py-5 text-center">   
                                        <img src="{{ asset('assets') }}/images/notes.png" alt="Location Icon" class="genral-icon-image">
                                        <div class="client-details">
                                            <p class="p-0 m-0 client-label">Notes </p>
                                            <p class="p-0 m-0 client-label-value">{{ $lead->description }}</p>
                                        </div>
                                    </div>
                                  
                                    <div class="col-md-4 border-top-right py-5 text-center">
                                        
                                        <div class="client-details">
                                            <button type="submit" class="btn general_submit w-100" id="convert_lead">Convert To Job </button>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-md-4">
                                {{-- convert lead in to Job --}}

                                    <div class="convert_lead_to_job" style="display:none">
                                        <h5 class="text-white">Convert To JOB </h5>
                                        <div class="bg-white p-3 mt-3 rounded">
                                            <div>
                                                <form method="post" action="{{ route('ConvertLeadToJob') }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="col-md-12 my-3">
                                                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                        <select class="form-select form-select-sm text-muted" name="job_type" required>
                                                            <option value="">Job type</option>
                                                            <option value="Residential">Residential</option>
                                                            <option value="Commercial">Commercial</option>
                                                            {{-- <option value="Lead">Lead</option> --}}
                                                           
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12 my-3">
                                                        <input class="form-control form-control-sm" type="text" name="name" placeholder="Job name *" value="{{ $lead->deal_name }}" required>
                                                    </div>
                                                    <div class="col-md-12   my-3">
                                                        <select class="form-select form-select-sm text-muted" name="client_id" onchange="fetchData(this.value)" required>
                                                            <option value="">Client’s Name *</option>
                                                            @if(!empty($All_contacts))
                                                                @foreach($All_contacts as $cont)
                                                                    @if($cont->type == 1)
                                                                        <option value="{{ $cont->id }}"> {{ $cont->name }} {{ $cont->mobile }} </option>
                                                                    @endif
                                                                @endforeach
                                                            @endif	
                                                            <option value="add_new_client">+ Add New Client</option>
                                                            
                                                        </select>
                                                    </div>
                                            
                                                    <div id="client_data">
                                                        <div class="col-md-12">
                                                            <h5 class="Client-Address">Address</h5>
                                                        </div>
                                                        <div class="col-md-12 my-3">
                                                            <input class="form-control form-control-sm" type="text" name="address" value="{{ $lead->address }}" placeholder="Street address">
                                                        </div>
                                                        <div class="col-md-12 my-3">
                                                            <input class="form-control form-control-sm" type="text" name="city" value="{{ $lead->city }}" placeholder="Town/City ">
                                                        </div>
                                                        <div class="col-md-12  my-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <input class="form-control form-control-sm" type="text" name="state" value="{{ $lead->state }}" placeholder="State">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input class="form-control form-control-sm" type="text" name="pincode" value="{{ $lead->pincode }}" placeholder="Zipcode">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="inputGroup radiobtn">
                                                        <div class="row">
                                                           <div class="col-md-6 d-flex justify-content-center my-2">
                                                               <input id="optionSigned" value="1" name="contract_status" type="radio" />
                                                               <label for="optionSigned">Signed</label>
                                                           </div>
                                                           <div class="col-md-6 d-flex justify-content-center my-2">
                                                               <input id="optionUnsigned" value="0" name="contract_status" type="radio" checked/>
                                                               <label for="optionUnsigned">Not Signed</label>
                                                           </div>
                                                       </div>	
                                               </div>
                                               <hr>
                                                    <div class="col-md-12">
                                                            <div class="inputGroup">
                                                                <div class="row">
                                                                <div class="col-md-6 d-flex justify-content-center my-2">
                                                                    <button type="submit" class="btn general_submit w-100">Convert To Job</button>
                                                                </div>
                                                                <div class="col-md-6 d-flex justify-content-center my-2">
                                                                    <button type="button" class="btn general_cancel_submit w-100" id="cancel_convert_lead">Cancel</button>
                                                                </div>
                                                            </div>	
                                                            </div>   
                                                    </div>
                                                </form>
                                            </div>


                                        </div>
                                    </div>

                                {{-- End convert lead in to Job --}}

                                <div class="update_job_general" style="display:none;">
                                    <h5 class="text-white">Update Lead General </h5>
                                    <div class="bg-white p-3 mt-3 rounded">
                                        <div>
                                            <form method="post" action="{{ route('UpdateLead') }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                                <div class="col-md-12 my-3">
                                                    <input class="form-control form-control-sm" type="text" name="deal_name" placeholder="Job name *" value="{{ $lead->deal_name }}" required>
                                                </div>
                
                                                <div class="col-md-12 my-3">
                                                    <input class="form-control form-control-sm" type="text" name="name" value="{{ $lead->name }}" placeholder="Client Name *" required>
                                                </div>
                
                                                <div class="col-md-12 my-3">
                                                    <input class="form-control form-control-sm" type="email" name="lead_email" value="{{ $lead->lead_email }}" placeholder="Email">
                                                </div>
                
                                                <div class="col-md-12  my-3">
                                                    <input id="phone" class="form-control form-control-sm" type="text" name="mobile" value="{{ $lead->mobile }}" placeholder="Phone Number">
                                                 </div>
                
                                                <div id="client_data">
                                                    <div class="col-md-12">
                                                        <h5 class="Client-Address">Address</h5>
                                                    </div>
                                                    <div class="col-md-12 my-3">
                                                        <input class="form-control form-control-sm" type="text" name="address" value="{{ $lead->address }}" placeholder="Street address">
                                                    </div>
                                                    <div class="col-md-12 my-3">
                                                        <input class="form-control form-control-sm" type="text" name="city" value="{{ $lead->city }}" placeholder="Town/City ">
                                                    </div>
                                                    <div class="col-md-12  my-3">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input class="form-control form-control-sm" type="text" name="state" value="{{ $lead->state }}" placeholder="State">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control form-control-sm" type="text" name="pincode" value="{{ $lead->pincode }}" placeholder="Zipcode">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 my-3">
                                                    <textarea name="description" class="form-control" placeholder="Description">{{ $lead->description }}</textarea>
                                                </div>
                                                <div class="col-md-12">
                                                        <div class="inputGroup">
                                                            <div class="row">
                                                            <div class="col-md-6 d-flex justify-content-center my-2">
                                                                <button type="submit" class="btn general_submit w-100">Update</button>
                                                            </div>
                                                            <div class="col-md-6 d-flex justify-content-center my-2">
                                                                <button type="button" class="btn general_cancel_submit w-100" id="cancel_general">Cancel</button>
                                                            </div>
                                                        </div>	
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
        </div>
</div>
<div class="row">
    <div class="modal fade fullscreen" id="add_contact" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title model-head" id="detailsModalLabel"><span style="color:rgb(202, 29, 29)"> Client Profile </span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="create_contact" action="{{ route('addContact') }}" enctype="multipart/form-data">
                         @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="type" value="1"/>
                                <div class="form-group ">
                                     <div class="add-pic">
                                        <input type="file" name="profile_pic" id="add-pic">
                                        <label for="add-pic"><img class="pro-pic" src="{{ asset('pro-pic.png') }}"></label>
                                        <span>Upload a Picture</span>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                 <input id="mobile" class="form-control form-control-sm " type="tel" name="mobile" placeholder="Phone Number *" required />
                                </div>
                                <div class="form-group mt-3">
                                    <input class="form-control form-control-sm " type="text" name="name" placeholder="Name*" required />
                                </div>
                                <div class="form-group mt-3">
                                    <input class="form-control form-control-sm " type="email" name="email" placeholder="Email*" required />
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <h5 class="Client-Address">Client’s Address</h5>
                            </div>
                            <div class="col-md-12 mt-3">
                                <input class="form-control form-control-sm" type="text" name="address"
                                    placeholder="Street address *"  >
                            </div>
                            <div class="col-md-12 mt-3">
                                <input class="form-control form-control-sm" type="text" name="city"
                                    placeholder="Town/City *"  >
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" name="state"
                                            placeholder="State" >
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" name="pincode"
                                            placeholder="Zipcode" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <textarea class="form-control form-control-sm" placeholder="Contract Notes"></textarea>
                            </div>
                            <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">Create</button>
                            </div>
                        </div>
                    </form>
                    <!-- Add more content here if needed -->
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 @section('script')
<script>
 // Add TO DO SECTION 
    $('#addSectionBtn').on('click', function() {
            let sectionName = $('#newSection').val();
            if(sectionName) {
                $.ajax({
                    url: '{{ route('AddToDoSection') }}',
                    method: 'POST',
                    data: {  _token: '{{ csrf_token() }}', sec_name: sectionName,lead_id:{{ $lead->id }} },
                    success: function(response) {
                        let section = response.section;
                        let sectionTemplate = `
                            <div class="card mb-3" id="section-${section.id}">
                                <div class="card-header bg-secondary text-white  justify-content-between">
                                    <div class="row">
                                      <div class="col-md-8">
                                        <h5 class="section-name">${section.sec_name}</h5>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button class="btn btn-sm btn-success edit-section-btn" data-section-id="${section.id}" title="Edit Section"><i class="fa-solid fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger delete-section-btn" data-section-id="${section.id}" title="Delete Section"><i class="fa-solid fa-x"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-2 mt-2">
                                        <input type="text" class="form-control newTaskInput" placeholder="Task Name">
                                        <input type="text" class="form-control newTaskEndDate" placeholder="End Date" min="1997-01-01" max="2099-12-31">
                                        <textarea class="form-control newTaskDescription" placeholder="Task Description"></textarea>
                                        <button class="btn btn-success addTaskBtn" data-section-id="${section.id}" type="button">Add Task</button>
                                    </div>
                                    <ul class="list-group taskList">
                                         
                                    </ul>
                                </div>
                            </div>
                        `;
                        $('#sectionsContainer').append(sectionTemplate);
                        $('.newTaskEndDate').last().datetimepicker({
                            format: 'MMM DD, YYYY', // Set format to 'Nov 23, 2024'
                            icons: {
                                time: 'fa fa-clock',
                                date: 'fa fa-calendar',
                                up: 'fa fa-chevron-up',
                                down: 'fa fa-chevron-down',
                                previous: 'fa fa-chevron-left',
                                next: 'fa fa-chevron-right',
                                today: 'fa fa-calendar-check',
                                clear: 'fa fa-trash',
                                close: 'fa fa-times'
                            }
                        });
                        $('#newSection').val('');
                    }
                });
            }
        });

$(document).on('click', '.edit-section-btn', function() {
    let sectionId = $(this).data('section-id');
    let sectionNameElement = $(`#section-${sectionId} .section-name`);
    let isEditable = sectionNameElement.attr('contenteditable') === 'true';
    
    if (!isEditable) {
        sectionNameElement.attr('contenteditable', 'true').focus();
        $(this).text('Save');
    } else {
        sectionNameElement.attr('contenteditable', 'false');
        let updatedSectionName = sectionNameElement.text();
        $.ajax({
            url: '{{ route('UpdateToDoSection') }}',
            method: 'POST',
            data: {  _token: '{{ csrf_token() }}', id: sectionId, sec_name: updatedSectionName },
            success: function() {
                //alert('Section updated successfully');
                $(this).html('<i class="fa-solid fa-edit"></i>');
            }.bind(this)
        });
    }
});

$(document).on('click', '.delete-section-btn', function() {
    let sectionId = $(this).data('section-id');

    //if (confirm("Are you sure you want to delete this section?")) {
        $.ajax({
            url: '{{ route('DeleteToDoSection') }}',
            method: 'POST',  // Using POST if DELETE is unavailable
            data: {  
                _token: '{{ csrf_token() }}',  // Ensure CSRF token is valid
                id: sectionId 
            },
            success: function(response) {
                if (response.success) {
                    //alert('Section deleted successfully'); 
                    $(`#section-${sectionId}`).remove();  // Remove section from DOM
                } else {
                    alert('Failed to delete section: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please check the console for details.');
                console.error('Error:', xhr.status, xhr.statusText, xhr.responseText);
            }
        });
    //}
});

// END TO DO SECTION


// ADD New TASK
                
        
$(document).on('click', '.addTaskBtn', function() {
    let button = this; // Store reference to this button
    let sectionId = $(button).data('section-id');
    let taskName = $(button).closest('.input-group').find('.newTaskInput').val();
    let taskEndDate = $(button).closest('.input-group').find('.newTaskEndDate').val();
    let taskDescription = $(button).closest('.input-group').find('.newTaskDescription').val();

    if (taskName) {
        $.ajax({
            url: '{{ route('AddToDoSectionTask') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                todosec_id: sectionId,
                task_name: taskName,
                enddate: taskEndDate,
                description: taskDescription
            },
            success: function(response) {
                // Clear the input fields
                $(button).closest('.input-group').find('.newTaskInput').val('');
                $(button).closest('.input-group').find('.newTaskEndDate').val('');
                $(button).closest('.input-group').find('.newTaskDescription').val('');
                
                let task = response.task;
                let taskTemplate = `
                    <li class="list-group-item d-flex justify-content-between">
                        <div>
                            <div class="form-check">
                                <input class="form-check-input task-status" type="checkbox" data-task-id="${task.id}">
                                <label class="form-check-label">${task.task_name}</label>
                            </div>
                            <small>Due: ${ moment(task.enddate).format('MMM DD, YYYY') }</small>
                        </div>
                        <div>
                            <!--<button class="btn btn-sm btn-success edit-task-btn" data-task-id="${task.id}" title="Edit task" data-toggle="modal" data-target="#edittask${task.id}"><i class="fa-solid fa-edit"></i></button>-->
                            <button class="btn btn-sm btn-danger delete-task-btn" data-task-id="${task.id}"  title="Delete task"><i class="fa-solid fa-x"></i></button>
                        </div>
                         
                    </li>`;
                
                $(`#section-${sectionId} .taskList`).append(taskTemplate);
            }
        });
    } else {
        alert("Please fill out task name.");
    }
});

$(document).on('change', '.task-status', function() {
            let taskId = $(this).data('task-id');
            let isChecked = $(this).is(':checked');
            //if (confirm("Are you sure you want to complete this task?")) {
                $.ajax({
                    url: '{{ route('updatetodotaskstatus') }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', todosectask_id: taskId, status: isChecked ? 1 : 0 },
                    success: function() {
                        if(isChecked) {
                            $(this).closest('.list-group-item').addClass('task-completed');
                            $(this).closest('.list-group-item').find('.label_txt').addClass('generaltaskcompleted');
                            
                        } else {
                            $(this).closest('.list-group-item').removeClass('task-completed');
                            $(this).closest('.list-group-item').find('.label_txt').removeClass('generaltaskcompleted');
                        }
                    }.bind(this)
                });
            //}
});

$(document).on('click', '.delete-task-btn', function() {
    let taskId = $(this).data('task-id');
        //if (confirm("Are you sure you want to delete this task?")) {
            $.ajax({
                url: '{{ route('deletetodotask') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    todosectask_id: taskId
                },
                success: function(response) {
                    if (response.success) {
                       // alert('Task deleted successfully');
                        $(`#task-${taskId}`).remove(); // Remove task from DOM
                    } else {
                        alert('Failed to delete task');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                    console.error(xhr.responseText);
                }
            });
       // }
});

$(document).ready(function() {
    $('#editgeneral').on('click', function() {
        $('.update_job_general').slideDown(1000); 
        $('.convert_lead_to_job').hide();
    });


    $('#cancel_general').on('click', function() {
            $('.update_job_general').slideUp(1000); 
    });


    $('#convert_lead').on('click', function() {
        $('.convert_lead_to_job').slideDown(1000); 
        $('.update_job_general').hide();
    });

    $('#cancel_convert_lead').on('click', function() {
            $('.convert_lead_to_job').slideUp(1000); 
    });

});
       
function fetchData(value) {

    if (value) {
        if(value =='add_new_client'){
            var credit_contact = @json(Auth::user()->credit_contact);
            if (credit_contact > 0) {
                // $('#detailsModalLabel span').text('Client Profile');
                // $('#add_contact_type').find('input[type="hidden"][name="type"]').remove();
                // $('#add_contact_type').append('<input type="hidden" name="type" value="1"/>');
                $('#add_contact').modal('show');
            }else{
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
            }
        }
    } else {
        $('#result').html(''); // Clear the result if no value is selected
    }

}

   
document.addEventListener("DOMContentLoaded", function() {
    // Retrieve activeTab from the session using Blade
    let activeTab = "{{ session('activeTab', 'tab1') }}"; // Default to 'tab1' if no session value

    // If an activeTab value exists, activate it
    if (activeTab) {
        openTab({ currentTarget: document.querySelector(`[onclick="openTab(event, '${activeTab}')"]`) }, activeTab);
    }
});

function openTab(event, tabId) {
    // Remove "active" class from all buttons and content
    document.querySelectorAll(".tab-button").forEach(button => button.classList.remove("active"));
    document.querySelectorAll(".tab-content1").forEach(content => content.classList.remove("active"));

    // Add "active" class to clicked tab and corresponding content
    event.currentTarget.classList.add("active");
    document.getElementById(tabId).classList.add("active");
}

$("#add-pic").change(function() {
    readLogoURL(this);
});

function readLogoURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.pro-pic').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

const input = document.querySelector("#mobile");
const iti = window.intlTelInput(input, {
initialCountry: "us", // Set the initial country to India
separateDialCode: true, // If you want the dial code visually separated
strictMode: true
});
document.querySelector('#create_contact').addEventListener('submit', function (event) {
// Prevent the default form submission to format the number first
event.preventDefault();

// Get the full phone number including the country code
const fullNumber = iti.getNumber();

// Set the full number back to the input field before submitting
document.querySelector("#mobile").value = fullNumber;

// Now, submit the form
this.submit();
});
</script>
    
@stop