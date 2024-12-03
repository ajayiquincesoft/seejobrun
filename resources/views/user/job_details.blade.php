@extends('user.layout.userdashboard')
@section('content')
<?php  //echo 'helloo'.$jobdata['items']['name'];

 $contacts = $data['contactshared'];
 foreach( $contacts as $contactshared){
     $contactShared1 = $contactshared->contactshared1;
     $contactsharedlength = $contactShared1->count();
 }

?>

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
                 @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
         </div>
         <div class="d-flex justify-content-between align-items-center">
             <div class="text-white">
                 <h5 class="job_ttle">
                     {{ $job_details->first()->name }}
                 </h5>
             </div>
             @if($job_details->first()->user_id == auth()->id())
                <form method="POST" action="{{ route('deletejob')}}">
                    @csrf
                    <input type="hidden" name="job_id" value="{{ @$job_details->first()->id }}">
                    <button class="delete-jobs">Delete All Job Information</button>
                </form>
             @endif
         </div>
         <div class="row py-4">
             <div class="col-md-12">
                @if($contacts && $contacts->isNotEmpty())
                <ul class="nav nav-tabs bg-white d-flex" id="myTab" role="tablist">
                    @foreach($contacts as $contactshared)
                        @php
                            $contactsharedlength = $contactshared->contactshared1->count();
                        @endphp
            
                        @if($contactsharedlength < 1)
                            <!-- Show all tabs if contactshared1 is empty -->
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == '') ? 'active' : '' }}" id="task-assignment-tab" data-bs-toggle="tab"
                                    data-bs-target="#task-assignment" type="button" role="tab"
                                    aria-controls="task-assignment" aria-selected="true">Task Assignment</button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'general') ? 'active' : '' }}" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                                    type="button" role="tab" aria-controls="general" aria-selected="false">General</button>
                            </li>

                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'todo') ? 'active' : '' }}" id="todo-tab" data-bs-toggle="tab" data-bs-target="#todo"
                                    type="button" role="tab" aria-controls="todo" aria-selected="false">TO DO</button>
                            </li>

                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'calendar') ? 'active' : '' }}" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar"
                                    type="button" role="tab" aria-controls="calendar"
                                    aria-selected="false">Calendar</button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'final-punchlist') ? 'active' : '' }}" id="final-punchlist-tab" data-bs-toggle="tab"
                                    data-bs-target="#final-punchlist" type="button" role="tab"
                                    aria-controls="final-punchlist" aria-selected="false">Final Punchlist</button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'stage') ? 'active' : '' }}" id="stage-tab" data-bs-toggle="tab" data-bs-target="#stage"
                                    type="button" role="tab" aria-controls="stage" aria-selected="false">Stage</button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'document') ? 'active' : '' }}" id="document-tab" data-bs-toggle="tab" data-bs-target="#document"
                                    type="button" role="tab" aria-controls="document"
                                    aria-selected="false">Document</button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'pictures') ? 'active' : '' }}" id="pictures-tab" data-bs-toggle="tab" data-bs-target="#pictures"
                                    type="button" role="tab" aria-controls="pictures"
                                    aria-selected="false">Pictures</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'contacts') ? 'active' : '' }}" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts"
                                    type="button" role="tab" aria-controls="contacts"
                                    aria-selected="false">Contacts</button>
                            </li>
                        @else
                            <!-- Conditionally show tabs based on contactshared1 values -->
                            @php
                               $contactShared1 = $contactshared->contactshared1->first(); // Get the first shared permissions (if needed)
                          
                           @endphp
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == '') ? 'active' : '' }}" id="task-assignment-tab" data-bs-toggle="tab"
                                    data-bs-target="#task-assignment" type="button" role="tab"
                                    aria-controls="task-assignment" aria-selected="true">Task Assignment</button>
                            </li>
                            @if($contactShared1->general == 1)
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'general') ? 'active' : '' }}" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                                    type="button" role="tab" aria-controls="general" aria-selected="false">General</button>
                            </li>
                            @endif

                            @if($contactShared1->todo == 1)
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'todo') ? 'active' : '' }}" id="todo-tab" data-bs-toggle="tab" data-bs-target="#todo"
                                    type="button" role="tab" aria-controls="todo" aria-selected="false">TO DO</button>
                            </li>
                            @endif

                            @if($contactShared1->calendar == 1)
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'calendar') ? 'active' : '' }}" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar"
                                    type="button" role="tab" aria-controls="calendar"
                                    aria-selected="false">Calendar</button>
                            </li>
                            @endif
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'final-punchlist') ? 'active' : '' }}" id="final-punchlist-tab" data-bs-toggle="tab"
                                    data-bs-target="#final-punchlist" type="button" role="tab"
                                    aria-controls="final-punchlist" aria-selected="false">Final Punchlist</button>
                            </li>
                          
                            @if($contactShared1->stage == 1)
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link {{ (session('activeTab') == 'stage') ? 'active' : '' }}" id="stage-tab" data-bs-toggle="tab" data-bs-target="#stage"
                                    type="button" role="tab" aria-controls="stage" aria-selected="false">Stage</button>
                            </li>
                            @endif
                           
                           
                            @if($contactShared1->document == 1)
                                <li class="nav-item flex-fill" role="presentation">
                                    <button class="nav-link {{ (session('activeTab') == 'document') ? 'active' : '' }}" id="document-tab" data-bs-toggle="tab" data-bs-target="#document"
                                        type="button" role="tab" aria-controls="document"
                                        aria-selected="false">Document</button>
                                </li>
                            @endif
                            @if($contactShared1->pictures == 1)
                                <li class="nav-item flex-fill" role="presentation">
                                    <button class="nav-link {{ (session('activeTab') == 'pictures') ? 'active' : '' }}" id="pictures-tab" data-bs-toggle="tab" data-bs-target="#pictures"
                                        type="button" role="tab" aria-controls="pictures"
                                        aria-selected="false">Pictures</button>
                                </li>
                            @endif
                            @if($contactShared1->contact == 1)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ (session('activeTab') == 'contacts') ? 'active' : '' }}" id="contacts-tab" data-bs-toggle="tab"
                                        data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts"
                                        aria-selected="false">Contacts</button>
                                </li>
                            @endif
                        @endif
                    @endforeach
                </ul>
            @endif
            
                
                 <div class="tab-content mt-3" id="myTabContent">
                     <div class="tab-pane fade show {{ (session('activeTab') == '')?'active':'' }}" id="task-assignment" role="tabpanel"
                         aria-labelledby="task-assignment-tab">
                         <div class="row my-5">
                             <div class="col-md-8">
                                 <div class="d-flex justify-content-between">
                                     <div>
                                         <h6 class="text-white">Permit Number <br>@if($job_details->first()->permit_no)<span
                                             style="font-size: 12px;">  ( {{ @$job_details->first()->permit_no }} )</span>@endif</h6>
                                     </div>
                                     <div class="d-flex justify-content-end ">
                                         <div class="px-3">
                                             <h6 class="text-white">Lock box code <br> @if($job_details->first()->Lock_box_code)<span
                                                 style="font-size: 12px;float: right;line-height: 24px;"> ( {{ @$job_details->first()->Lock_box_code }} )</span> @endif</h6>
                                         </div>
                                         <div>
                                             @if(@$job_details->first()->jobinspection->first()->contact->mobile)
                                                 <a href="tel:{{  $job_details->first()->jobinspection->first()->contact->mobile }}"> <button class="btn call-Inspection font-14"><i
                                                      class="fa-solid fa-phone mr-1"></i>
                                                      <span class="mx-2">Call For Inspection</span></button></a>

                                             @else
                                             <button class="btn call-Inspection font-14" onclick="return confirm('No inspection number available');"><i
                                                 class="fa-solid fa-phone mr-1"></i>
                                                 <span class="mx-2">Call For Inspection</span></button>
                                             @endif
                                          </div>
                                     </div>
                                 </div>
                                 <div class="" style="overflow: hidden;overflow-x: scroll; ">
                                    
                                     <table class="table  table-bordered  bg-white jobs-table mt-4">
                                         <thead class="jobs-thead">
                                             <tr>
                                                 <th width="35%">Task Name</th>
                                                 <th width="10%">Room</th>
                                                 <th width="15%">Assign To</th>
                                                 <th width="15%">Start Date</th>
                                                 <th width="10%">Priority</th>
                                                 <th width="15%">Action</th>
                                             </tr>
                                         </thead>
                                         <tbody class="jobs-table-body">
                                             <!-- First Row -->
                                             @foreach($tasks as $task )
                                         
                                            @if(count($task->taskassignment)>0)
                                             @foreach($task->taskassignment as $taskde)
                                            
                                                <tr>
                                                <td width="35%">
                                                <div class="form-check">

                                                    <input type="checkbox" id="task{{ @$taskde->id }}" name="taskid" class="form-check-input big-checkbox taskcheckbox" value="{{ @$taskde->id }}" {{ @$taskde->status == 1 ? 'checked' : '' }}>

                                                    <label class="form-check-label checkbox-label color_black" for="task1" > 
                                                        {{ ucwords(@$taskde->title) }}
                                                        {{-- @if(strlen(@$taskde->title) >= 15)
                                                                {{ ucwords(substr(@$taskde->title, 0, 15)) }} ....
                                                            @else
                                                                {{ ucwords(@$taskde->title) }}
                                                            @endif --}}
                                                    </label>
                                                </div>
                                                </td>
                                                <td width="10%">{{ ucwords(@$taskde->room) }}</td>
                                                <td width="15%">{{ @$taskde->contact_name  }}</td>
                                                <td width="15%">{{ \Carbon\Carbon::parse(@$taskde->startdate)->format('M d, Y') }}</td>
                                                <td width="10%">
                                                    @if($taskde->priority == 'High')
                                                        <span class="badge badge-fixed bg-high">{{ @$taskde->priority }}</span>
                                                    @elseif($taskde->priority == 'Medium')
                                                        <span class="badge badge-fixed bg-medium">{{ @$taskde->priority }}</span>
                                                    @elseif($taskde->priority == 'Complete')
                                                        <span class="badge badge-fixed bg-complete">{{ @$taskde->priority }}</span>
                                                    @else
                                                        <span class="badge badge-fixed bg-low">{{ @$taskde->priority }}</span>
                                                    @endif

                                                </td>
                                                <td width="15%">
                                                    <button class="action-button action-delete dsingletask" data-id="{{ @$taskde->id }}" title="Delete">
                                                        <i class="fa-solid fa-x"></i>
                                                    </button>
                                                    <button class="action-button action-edit " data-toggle="modal"
                                                    data-target="#edit{{ @$taskde->id }}" title="Edit">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <!-------------Edit task ----------->
                                                    <div class="modal fade" id="edit{{ @$taskde->id }}" tabindex="-1"
                                                        role="dialog" aria-labelledby="modalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title title-model"
                                                                    id="modalLabel1">Edit Task Assign 
                                                                    </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="bg-white p-3 rounded" >
                                                                    <div>
                                                                        <form method="post" action="{{ route('updateSingleTaskAssignment') }}" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <div class=" col-md-12 mb-3">
                                                                                <input class="form-control form-control-sm" type="hidden"
                                                                                name="id"  value="{{ @$taskde->id }}">
                                                                                <input class="form-control form-control-sm" type="text"
                                                                                    name="title" placeholder="Task name *" value="{{ @$taskde->title }}" required>
                                                                                <input class="form-control form-control-sm" type="hidden"
                                                                                    name="job_id"  value=" {{ @$job_details->first()->id }}">
                                                                            </div>
                                                                            <div class="col-md-12 mb-3">
                                                                                <input type="text" name="room" class="form-control form-control-sm"
                                                                                    placeholder="Room" value="{{ @$taskde->room }}">
                                                                            </div>
                                                                            <div class="col-md-12 mb-3">
                                                                                <select name="priority" class="form-select form-select-sm text-muted" required>
                                                                                    <option selected disabled value="">Priority</option>
                                                                                    <option value="High" {{ @$taskde->priority=='High'?'selected':' ' }}>High</option>
                                                                                    <option value="Medium" {{ @$taskde->priority=='Medium'?'selected':' ' }}>Medium</option>
                                                                                    <option value="Low" {{ @$taskde->priority=='Low'?'selected':' ' }}>Low</option>

                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-12  mb-3">
                                                                                <select name="assign_to" class="form-select form-select-sm text-muted" onchange="addnewContact(this.value,{{ @$taskde->id }})"  required>
                                                                                    <option selected>Assign To</option>
                                                                                    <option value=" {{ @$task->contact->id }}" selected> {{ @$task->contact->name }}</option>
                                                                                    @foreach($allcontact as $alcontact)
                                                                                        <option value=" {{ @$alcontact->id }}"> {{ @$alcontact->name }}</option>
                                                                                    @endforeach
                                                                                    <option value="addcontact">+ Add New Contact</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-12 mb-3">
                                                                                <div class="row">
                                                                                    <div class="col-md-6 ">
                                                                                        <input type="text" 
                                                                                        id="startdate{{ $taskde->id }}" 
                                                                                        name="startdate" 
                                                                                        value="{{ $taskde->startdate ? \Carbon\Carbon::parse($taskde->startdate)->format('M d, Y') : 'No start date' }}"  class="form-control form-control-sm">
                                                                                    </div>
                                                                                    <div class="col-md-6 ">
                                                                                        <input type="text" 
                                                                                        id="enddate{{ $taskde->id }}" 
                                                                                        name="enddate" 
                                                                                        value="{{ $taskde->startdate ? \Carbon\Carbon::parse($taskde->enddate)->format('M d, Y') : 'No end date' }}" 
                                                                                        class="form-control form-control-sm">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12 mb-3">
                                                                                <textarea name="description" rows="4" class="form-control form-control-sm"
                                                                                    id="" placeholder="Description">{{ @$taskde->description }}</textarea>
                                                                            </div>
                                                                            <div class="col-md-12 mb-3">

                                                                            <div id="existing-images-{{ @$taskde->id }}">
                                                                                <div class="row">
                                                                                @foreach($taskde->taskassignmentimages as $doc)
                                                                                <div class="col-md-3" id="image-{{ $doc->id }}">
                                                                                    <div class="image-item">
                                                                                        <button type="button" class="btn btn-danger btn-sm delete-image-btn" data-id="{{ $doc->id }}" data-task-id="{{ $taskde->id }}">Delete</button>
                                                                                        <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$taskde->title) }}">
                                                                                            <img src="{{ asset($doc->image) }}" width="150" height="150" alt="{{ ucwords(@$taskde->title) }}" class="img-fluid"/>
                                                                                        </a>
                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                @endforeach
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="col-md-12 mb-3">
                                                                            <input type="file" name="new_images[]" class="form-control form-control-sm" multiple>
                                                                        </div>
                                                                            <div class="col-md-12">
                                                                                <button type="submit" class="btn Stage-submit w-100">Save Change</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                        
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <script>

                                                    jQuery(function () {
                                                            jQuery('#startdate{{ @$taskde->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                            jQuery('#enddate{{ @$taskde->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                        });


                                                </script> --}}
                                                <script>
                                                    $(document).ready(function () {
                                                        // Initialize Bootstrap datepickers
                                                        $('#startdate{{ @$taskde->id }}').datepicker({
                                                            format: 'M d, yyyy', // Month abbreviated, Day, Year
                                                        autoclose: true,
                                                        todayHighlight: true,
                                                        }).on('changeDate', function (e) {
                                                            // Get the selected date
                                                            const startDate = $('#startdate{{ @$taskde->id }}').datepicker('getDate');
                                                            if (startDate) {
                                                                // Add one day to the start date
                                                                const nextDay = new Date(startDate);
                                                                nextDay.setDate(startDate.getDate() + 1);
                                                    
                                                                // Set the new date in the enddate field
                                                                $('#enddate{{ @$taskde->id }}').datepicker('setDate', nextDay);
                                                            }
                                                        });
                                                    
                                                        $('#enddate{{ @$taskde->id }}').datepicker({
                                                            format: 'M d, yyyy', // Month abbreviated, Day, Year
                                                        autoclose: true,
                                                        todayHighlight: true,
                                                        });
                                                    });
                                                    
                                                    </script>
                                                </div>
                                                    <!-------------End tasks---------->

                                                    <!-- View Button Trigger Modal -->
                                                    <button class="action-button action-view" data-toggle="modal"
                                                        data-target="#viewModal{{ @$taskde->id }}" title="view">
                                                        <i class="fa-regular fa-eye"></i>
                                                    </button>
                                                    <!-- Modal for Row 1 -->
                                                    <div class="modal fade" id="viewModal{{ @$taskde->id }}" tabindex="-1"
                                                        role="dialog" aria-labelledby="modalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title title-model"
                                                                        id="modalLabel1">{{ @$job_details->first()->name }}</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table">
                                                                        <tr>
                                                                            <th>Task Name</th>
                                                                            <td class=" title-model-table ">
                                                                                {{ ucwords(@$taskde->title) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Priority</th>
                                                                            <td class="text-end">@if($taskde->priority == 'High')
                                                                                <span class="badge badge-fixed bg-high">{{ @$taskde->priority }}</span>
                                                                            @elseif($taskde->priority == 'Medium')
                                                                                <span class="badge badge-fixed bg-medium">{{ @$taskde->priority }}</span>
                                                                            @elseif($taskde->priority == 'Complete')
                                                                                <span class="badge badge-fixed bg-complete">{{ @$taskde->priority }}</span>
                                                                            @else
                                                                                <span class="badge badge-fixed bg-low">{{ @$taskde->priority }}</span>
                                                                            @endif
                                                                        
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Room</th>
                                                                            <td class="text-end title-model-table">
                                                                                {{ ucwords(@$taskde->room) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Assign To</th>
                                                                            <td class="text-end title-model-table">
                                                                                {{ @$taskde->contact_name }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Start Date</th>
                                                                            <td class="text-end title-model-table">
                                                                                {{ \Carbon\Carbon::parse(@$taskde->startdate)->format('M d, Y') }}</td>
                                                                        </tr>
                                                                    </table>
                                                                    <div>
                                                                        <h6 class="title-model-table">Description</h6>
                                                                        <p class="text-justify">{{ @$taskde->description }}</p>
                                                                    </div>
                                                                    <hr>
                                                                    <div>
                                                                        <h6 class="title-model-table">Document</h6>
                                                                        
                                                                        @foreach($taskde->taskassignmentimages as $doc)
                                                                        <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$taskde->title) }}">
                                                                            <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$taskde->title) }}" class="img-fluid"/>
                                                                        </a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                                 @endforeach
                                             @else
                                             <tr>
                                                <td colspan="6">There is no task</td>
                                             </tr>
                                             @endif
                                            @endforeach

                                             <!-- Repeat similar structure for other rows, making sure each row has a unique modal ID and button trigger -->
                                         </tbody>

                                     </table>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <h5 class="text-white">Assign a New Task</h5>
                                 <div class="bg-white p-3 rounded" >
                                     <div>
                                         <form method="post" action="{{ route('addTaskAssingment') }}" enctype="multipart/form-data">
                                             @csrf
                                             <div class=" col-md-12 mb-3">
                                                 <input class="form-control form-control-sm" type="hidden" name="job_id"  value=" {{ @$job_details->first()->id }}">
                                                 <input  type="text" name="title" value="" placeholder="Task name *" class="form-control form-control-sm" required>
                                             </div>
                                             <div class="col-md-12 mb-3">
                                                 <input type="text" name="room" value="" class="form-control form-control-sm"
                                                     placeholder="Room">
                                             </div>
                                             <div class="col-md-12 mb-3">
                                                 <select name="priority" class="form-select form-select-sm text-muted" required>
                                                     <option selected disabled value="">Priority</option>
                                                     <option value="High">High</option>
                                                     <option value="Medium">Medium</option>
                                                     <option value="Low">Low</option>
                                                 </select>
                                             </div>
                                             <div class="col-md-12  mb-3">
                                                 <select class="form-select form-select-sm text-muted" name="assign_to" value="" onchange="addnewContactinaddtask(this.value)" required>
                                                    <option selected value="">Assign To</option>
                                                         @foreach($allcontact as $alcontact)
                                                         <option value=" {{ @$alcontact->id }}"> {{ @$alcontact->name }}</option>
                                                      @endforeach
                                                     <option value="addcontact">+ Add New Contact</option>
                                                 </select>
                                             </div>
                                             <div class="col-md-12 mb-3">
                                                 <div class="row">
                                                     <div class="col-md-6 ">
                                                         <input type="text" 
                                                         id="taskstartd" 
                                                         name="startdate"  placeholder="Start Date"
                                                         value=""  class="form-control form-control-sm">
                                                     </div>
                                                     <div class="col-md-6 endtask">
                                                    <input type="text" id="taskendd" name="enddate" value="" placeholder="EndDate" class="form-control form-control-sm">
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="col-md-12 mb-3">
                                                 <textarea name="description" rows="4" class="form-control form-control-sm" id="" placeholder="Description"></textarea>
                                             </div>

                                             <div class="col-md-12 mb-3">
                                                 <input type="file" name="new_images[]" class="form-control form-control-sm" multiple>
                                             </div>
                                             <div class="col-md-12">
                                                 <button type="submit" class="btn Stage-submit w-100">Save New
                                                     Task</button>
                                             </div>
                                         </form>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="tab-pane fade show {{ (session('activeTab') == 'general')?'active':'' }}" id="general" role="tabpanel" aria-labelledby="general-tab">
                         <div class="row my-5">
                             @php 
                                 //echo $jobgeneral->first()->name;
                             @endphp
                             <div class="col-md-12 col-lg-8">
                                 <div class="d-flex justify-content-between">
                                     <div>
                                         <h6 class="text-white">Permit Number <br>@if($job_details->first()->permit_no)<span
                                                 style="font-size: 12px;">  ( {{ @$job_details->first()->permit_no }} )</span>@endif</h6>
                                     </div>
                                     <div class="d-flex justify-content-end ">
                                         <div class="px-3">
                                             <h6 class="text-white">Lock box code <br> @if($job_details->first()->Lock_box_code)<span
                                                     style="font-size: 12px;float: right;line-height: 24px;"> ( {{ @$job_details->first()->Lock_box_code }} ) </span> @endif </h6>
                                         </div>
                                         <div>
                                             
                                         @if(@$job_details->first()->jobinspection->first()->contact->mobile)
                                         <a href="tel:{{  $job_details->first()->jobinspection->first()->contact->mobile }}"> <button class="btn call-Inspection font-14"><i
                                                 class="fa-solid fa-phone mr-1"></i>
                                                 <span class="mx-2">Call For Inspection</span></button></a>
                                             @else
                                             <button class="btn call-Inspection font-14" onclick="return confirm('No inspection number available');"><i
                                                 class="fa-solid fa-phone mr-1"></i>
                                                 <span class="mx-2">Call For Inspection</span></button>
                                             @endif
                                         </div>
                                        
                                     </div>
                                 </div>
                                 <div class="row bg-white pt-4 mt-5  position-relative ">
                                     <div class="col-md-3 d-flex ">
                                         <img src="{{ asset('assets') }}/images/UserIcon.png" alt="User Image" class="genral-icon-image">
                                         <div class="client-details">
                                             <p class="p-0 m-0 client-label">Client Name</p>
                                             <p class="p-0 m-0 client-label-value">
                                             @foreach ($jobgeneral as $jobgener)
                                                 @if ($jobgener->contact)
                                                    {{ $jobgener->contact->name }}
                                                 @else
                                                    No contact information available.
                                                 @endif
                                             @endforeach
                                         </p>
                                         </div>
                                     </div>
                                     <div class="col-md-4 4 d-flex">
                                         <img src="{{ asset('assets') }}/images/Loaction.png" alt="User Image" class="genral-icon-image">
                                         <div class="client-details">
                                             <p class="p-0 m-0 client-label">Client Address</p>
                                             <p class="p-0 m-0 client-label-value">@foreach ($jobgeneral as $jobgener)
                                                 @if ($jobgener->contact)
                                                    {{ $jobgener->contact->address }}  {{ $jobgener->contact->city }}, {{ $jobgener->contact->state }}  {{ $jobgener->contact->pincode }}
                                                 @else
                                                    No contact information available.
                                                 @endif
                                             @endforeach </p>
                                         </div>
                                     </div>
                                     <div class="col-md-4 d-flex ">
                                         <img src="{{ asset('assets') }}/images/Loaction.png" alt="User Image" class="genral-icon-image">
                                         <div class="client-details">
                                             <p class="p-0 m-0 client-label">Job Address</p>
                                             <p class="p-0 m-0 client-label-value">{{ $jobgeneral->first()->address }}  {{ $jobgeneral->first()->city }}, {{ $jobgeneral->first()->state }}
                                                 {{ $jobgeneral->first()->pincode }}</p>
                                         </div>
                                     </div>
                                     {{-- <div class="col-md-1 position-absolute edit-icon-jobs mt-1" id="editgeneral" {{ @$job_details->first()->user_id }}>
                                         <i class="fa-solid fa-pen-to-square"></i>
                                     </div> --}}
                                     <div class="col-md-1 position-absolute edit-icon-jobs mt-1" id="editgeneral" 
                                        data-permission="{{ Auth::id() == @$job_details->first()->user_id ? 'allowed' : 'denied' }}" title="EDIT">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </div>
                                     <div class="row my-2 ">
                                         <div class="col-md-3 border-top-right py-5 text-center">
                                             <img src="{{ asset('assets') }}/images/phoneicon.png" alt="User Image"
                                                 class="genral-icon-image">
                                             <div class="client-details">
                                                 <p class="p-0 m-0 client-label">Client’s Phone</p>
                                                 <p class="p-0 m-0 client-label-value">@foreach ($jobgeneral as $jobgener)
                                                     @if ($jobgener->contact)
                                                        {{ $jobgener->contact->address }}  {{ $jobgener->contact->city }}, {{ $jobgener->contact->state }}  {{ $jobgener->contact->pincode }}
                                                     @else
                                                        No contact information available.
                                                     @endif
                                                 @endforeach</p>
                                             </div>
                                         </div>
                                         <div class="col-md-3  border-top-right py-5 text-center">
                                             <img src="{{ asset('assets') }}/images/Emailicon.png" alt="User Image"
                                                 class="genral-icon-image">
                                             <div class="client-details">
                                                 <p class="p-0 m-0 client-label">Email Address</p>
                                                 <p class="p-0 m-0 client-label-value">@foreach ($jobgeneral as $jobgener)
                                                     @if ($jobgener->contact)
                                                      <a href="mailto:{{ $jobgener->contact->email }}">  {{ $jobgener->contact->email }}</a>
                                                     @else
                                                        No contact information available.
                                                     @endif
                                                 @endforeach
                                                 </p>
                                             </div>
                                         </div>
                                         <div class="col-md-3 border-top-right py-5  text-center">
                                             <img src="{{ asset('assets') }}/images/buildingicon.png" alt="User Image"
                                                 class="genral-icon-image">
                                             <div class="client-details">
                                                 <p class="p-0 m-0 client-label">Gate Code</p>
                                                 <p class="p-0 m-0 client-label-value"> {{ @$job_details->first()->gate_no }} </p>
                                             </div>
                                         </div>
                                         <div class="col-md-3 border-top-right py-5 text-center">
                                        
                                         
                                                  @if($job_details->first()->contract_status==1)
                                                  <img src="{{ asset('assets') }}/images/signed.png" alt="User Image" class="genral-icon-image">
                                                     <div class="client-details">
                                                         <p class="p-0 m-0 client-label">Contract Status</p>
                                                         <p class="p-0 m-0 client-label-value-green" style="color:#49B030;">Signed</p>
                                                     </div> 

                                                  @else
                                                  <img src="{{ asset('assets') }}/images/crossicon.png" alt="User Image" class="genral-icon-image">
                                                  <div class="client-details">
                                                      <p class="p-0 m-0 client-label">Contract Status</p>
                                                      <p class="p-0 m-0 client-label-value-danger">Unsigned</p>
                                                  </div> 
                                                  @endif
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-lg-4 col-md-12">
                                 <div class="update_job_general" style="display:none;">
                                     <h5 class="text-white">Update Job General </h5>
                                     <div class="bg-white p-3 rounded" >
                                         <div>
                                             <form method="post" action="{{ route('updateJobGeneral') }}" enctype="multipart/form-data">
                                                 @csrf
                                                 <div class="col-md-12 mb-3">
                                                    <label style="color: #444141;"> Job Name</label>
                                                    <input type="text" name="name" value="{{ @$job_details->first()->name }}" class="form-control form-control-sm"
                                                        placeholder="Job Name">
                                                </div>
                                                 <div class=" col-md-12 mb-3">
                                                     <label style="color: #444141;"> Permit Number </label>
                                                     <input class="form-control form-control-sm" type="hidden" name="job_id"  value=" {{ @$job_details->first()->id }}">
                                                     <input  type="text" name="permit_no" value="{{ @$job_details->first()->permit_no }}" placeholder="Permit Number*" class="form-control form-control-sm">
                                                 </div>
                                                 <div class="col-md-12 mb-3">
                                                     <label style="color: #444141;"> New Gate code</label>
                                                     <input type="text" name="gate_no" value="{{ @$job_details->first()->gate_no }}" class="form-control form-control-sm"
                                                         placeholder="New Gate code">
                                                 </div>
                                                 <div class="col-md-12 mb-3">
                                                     <label style="color: #444141;"> New Lock Code</label>
                                                     <input type="text" name="Lock_box_code" value="{{ @$job_details->first()->Lock_box_code }}" class="form-control form-control-sm"
                                                         placeholder="New Lock Code">
                                                 </div>
                                                 {{-- @if(@$job_details->first()->jobinspection->first()->contact->mobile)
                                                     <div class="col-md-12  mb-3">
                                                         <label style="color: #444141;"> Job Inspection phone</label>
                                                         <input type="text" name="inpection_number" value="{{ @$job_details->first()->jobinspection->first()->contact->mobile }}" class="form-control form-control-sm"
                                                         placeholder="Inspection number">
                                                     </div>
                                                 @endif --}}
                                                 <div class="col-md-12  mb-3">
                                                     <select class="form-select form-select-sm text-muted" name="job_type">
                                                         <option value="">Job type</option>
                                                         <option value="Residential" {{ (@$job_details->first()->job_type=='Residential')?'selected':'' }}>Residential</option>
                                                         <option value="Commercial" {{ (@$job_details->first()->job_type=='Commercial')?'selected':'' }}>Commercial</option>
                                                         <option value="Archived" {{ (@$job_details->first()->job_type=='Archived')?'selected':'' }}>Archived</option>
                                                     </select>
                                                 </div>
                                                 <div class="col-md-12  mb-3">
                                                     <div class="inputGroup radiobtn">
                                                         <div class="row">
                                                         <div class="col-md-6 d-flex justify-content-center my-2">
                                                             <input id="optionSigned" value="1" name="contract_status" type="radio" {{ $job_details->first()->contract_status == 1 ? 'checked' : '' }} />
                                                             <label for="optionSigned">Signed</label>
                                                         </div>
                                                         <div class="col-md-6 d-flex justify-content-center my-2">
                                                             <input id="optionUnsigned" value="0" name="contract_status" type="radio" {{ $job_details->first()->contract_status == 0 ? 'checked' : '' }}/>
                                                             <label for="optionUnsigned">Not Signed</label>
                                                         </div>
                                                     </div>	
                                                     </div>
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

                     <div class="tab-pane fade show {{ (session('activeTab') == 'todo')?'active':'' }}" id="todo" role="tabpanel" aria-labelledby="todo-tab">
                       
                        <div class="todlist mt-4">
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

                     <div class="tab-pane fade show {{ (session('activeTab') == 'calendar')?'active':'' }}" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                         <div class="row">
                             <div class="col-md-8"> 
                                 <h5 class="text-white"> Calendar </h5>
                                 <div id="taskcalendar" class="mt-4"></div> 
                             </div>
                             <div class="col-md-4">
                                 <h5 class="text-white"> View Tasks </h5>
                                 <table class="table  table-bordered bg-white jobs-table mt-4">
                                     <thead class="jobs-thead">
                                         <tr>
                                             <th>S.No</th>
                                             <th>Task name</th>
                                             <th>Assign To</th>
                                             
                                         </tr>
                                     </thead>
                                     <tbody class="jobs-table-body" id="taskdata">
                                         <tr>
                                             <td colspan="4">There is no task.</td>
                                            
                                         </tr>
                                         
                                         
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                         
                     </div>
                     <div class="tab-pane fade show {{ (session('activeTab') == 'final-punchlist')?'active':'' }}" id="final-punchlist" role="tabpanel" aria-labelledby="final-punchlist-tab">
                        {{-- -- Final Punch list  --}}
                        <div class="row my-5">
                         <div class="col-md-8">
                             <div class="d-flex justify-content-between">
                                 <div class="d-flex justify-content-end ">
  
                                     <div>
                                         @php 
                                          $client_id = $job_details->first()->contact->first()->contact_user_id;
                                          $contact_user_type = $job_details->first()->contact->first()->type;
                                         $pmessage = @$punchapprove['get_approvedpunchlists']['message'];
                                         $punchappsts = @$punchapprove['get_approvedpunchlists']['status'];
                                         $psubmessage = @$punchapprove['get_approvedpunchlists']['submessage'];
                                        
                                          //dd($punchapprove);
                                          
                                         @endphp
                                         @if(@$punchappsts==0)
                                            @if($pmessage)
                                                <button  id="{{ (($job_details->first()->contact->first()->contact_user_id == Auth::user()->id) and ($job_details->first()->contact->first()->type==1) ) ? 'punchlistallchecksbox' : '' }}" class="1 btn call-Inspection font-14" {{ (($job_details->first()->contact->first()->contact_user_id != Auth::user()->id) and ($job_details->first()->contact->first()->type==1) ) ? 'disabled ' : '' }} >
                                                    <span class="mx-2" style="font-weight: 600;">{{ $pmessage }}</span>
                                                </button>

                                                <span style="color:#fff; font-size:12px; padding-left: 20px;">{{ $psubmessage }}</span>
                                            @endif
                                         @else
                                         @if($pmessage)
                                            <button  class="btn call-Inspection font-14" {{ (($job_details->first()->contact->first()->contact_user_id != Auth::user()->id) and ($job_details->first()->contact->first()->type==1) ) ? 'disabled' : '' }} >
                                                <span class="mx-2"  style="font-weight: 600;">{{ $pmessage }}</span>
                                            </button>
                                            <span style="color:#fff; font-size:12px;padding-left: 20px;">{{ $psubmessage }}</span>
                                         @endif
                                         @endif

                                     </div>

                                 </div>
                             </div>
                             <div class="" style="overflow: hidden;overflow-x: scroll; ">
                                
                                 <table class="table  table-bordered  bg-white jobs-table mt-4">
                                     <thead class="jobs-thead">
                                         <tr>
                                             <th>Puch List Name</th>
                                             <th>Room</th>
                                             <th>Assign To</th>
                                             <th>Start Date</th>
                                             <th>Priority</th>
                                             <th>Action</th>
                                         </tr>
                                     </thead>
                                     <tbody class="jobs-table-body">
                                         <!-- First Row -->
                                         @php
                                             //dd($punchlists);

                                         @endphp

                                    
                                @foreach($punchlists as $punchlist )
                              
                                @if(count($punchlist->punchlist)>0)
                                    @foreach($punchlist->punchlist as $Fpunchl)
                                         <tr>
                                             <td>
                                             <div class="form-check">
                                                
                                                 <input type="checkbox" id="punchlistid{{ @$Fpunchl->id }}" name="punchlistid" class="form-check-input punchlist-checkbox" value="{{ @$Fpunchl->id }}" {{ @$Fpunchl->status == 1 ? 'checked' : '' }}  {{ (@$punchappsts==1)?'disabled':'' }}>

                                                 <label class="form-check-label checkbox-label"
                                                     for="task1"> 
                                                     @if(strlen(@$Fpunchl->title) >= 15)
                                                             {{ ucwords(substr(@$Fpunchl->title, 0, 15)) }} ....
                                                         @else
                                                             {{ ucwords(@$Fpunchl->title) }}
                                                         @endif
                                                 </label>
                                             </div>
                                             </td>
                                             <td>{{ ucwords(@$Fpunchl->room) }}</td>
                                             <td>{{ @$Fpunchl->contact_name  }}</td>
                                             <td>{{ \Carbon\Carbon::parse(@$Fpunchl->startdate)->format('M d, Y') }}</td>
                                             <td>
                                                 @if($Fpunchl->priority == 'High')
                                                     <span class="badge badge-fixed bg-high">{{ @$Fpunchl->priority }}</span>
                                                 @elseif($Fpunchl->priority == 'Medium')
                                                     <span class="badge badge-fixed bg-medium">{{ @$Fpunchl->priority }}</span>
                                                 @elseif($Fpunchl->priority == 'Complete')
                                                     <span class="badge badge-fixed bg-complete">{{ @$Fpunchl->priority }}</span>
                                                  @else
                                                     <span class="badge badge-fixed bg-low">{{ @$Fpunchl->priority }}</span>
                                                  @endif
                                             
                                             </td>
                                             <td>
                                                @if($job_details->first()->user_id == auth()->id())
                                                    <button class="action-button action-delete-punchlist" data-id="{{ @$Fpunchl->id }}"  {{ (@$punchappsts == 1) ? 'disabled' : '' }} title="Delete">
                                                        <i class="fa-solid fa-x"></i>
                                                    </button>
                                                 @endif
                                                 <button class="action-button action-edit" data-toggle="modal"
                                                 data-target="#punchedit{{ @$Fpunchl->id }}" 
                                                 {{ (@$punchappsts == 1) ? 'disabled' : '' }} title="Edit">
                                                 <i class="fa-regular fa-pen-to-square"></i>
                                             </button>
                                             

                                                 <!-------------Edit task ----------->
                                                 <div class="modal fade" id="punchedit{{ @$Fpunchl->id }}" tabindex="-1"
                                                     role="dialog" aria-labelledby="modalLabel1"
                                                     aria-hidden="true">
                                                     <div class="modal-dialog" role="document">
                                                         <div class="modal-content">
                                                         <div class="modal-header">
                                                             <h5 class="modal-title title-model"
                                                                 id="modalLabel1">Edit Final Punchlist
                                                                 </h5>
                                                             <button type="button" class="btn-close"
                                                                 data-dismiss="modal"
                                                                 aria-label="Close"></button>
                                                         </div>
                                                         <div class="modal-body">
                                                             <div class="bg-white p-3 rounded" >
                                                                 <div>
                                                                     <form method="post" action="{{ route('updatesinglefinalpunchlist') }}" enctype="multipart/form-data">
                                                                         @csrf
                                                                         <div class=" col-md-12 mb-3">
                                                                             <input class="form-control form-control-sm" type="hidden"
                                                                             name="id"  value="{{ @$Fpunchl->id }}">
                                                                             <input class="form-control form-control-sm" type="text"
                                                                                 name="title" placeholder="Task name *" value="{{ @$Fpunchl->title }}" required>
                                                                             <input class="form-control form-control-sm" type="hidden"
                                                                                 name="job_id"  value="{{ @$job_details->first()->id }}">
                                                                         </div>
                                                                         <div class="col-md-12 mb-3">
                                                                             <input type="text" name="room" class="form-control form-control-sm"
                                                                                 placeholder="Room" value="{{ @$Fpunchl->room }}">
                                                                         </div>
                                                                         <div class="col-md-12 mb-3">
                                                                             <select name="priority" class="form-select form-select-sm text-muted" required>
                                                                                 <option selected disabled value="">Priority</option>
                                                                                 <option value="Complete" {{ @$Fpunchl->priority=='Complete'?'selected':' ' }}>Complete</option>
                                                                                 <option value="High" {{ @$Fpunchl->priority=='High'?'selected':' ' }}>High</option>
                                                                                 <option value="Medium" {{ @$Fpunchl->priority=='Medium'?'selected':' ' }}>Medium</option>
                                                                                 <option value="Low" {{ @$Fpunchl->priority=='Low'?'selected':' ' }}>Low</option>

                                                                             </select>
                                                                         </div>
                                                                         <div class="col-md-12  mb-3">
                                                                             <select name="assign_to" class="form-select form-select-sm text-muted" onchange="addnewContact(this.value,{{ @$Fpunchl->id }})"  required>
                                                                                 <option>Assign To</option>
                                                                                 <option value="{{ @$Fpunchl->contact_id }}" selected> {{ @$Fpunchl->contact_name }}</option>
                                                                                 @foreach($allcontact as $alcontact)
                                                                                     <option value="{{ @$alcontact->id }}"> {{ @$alcontact->name }}</option>
                                                                                 @endforeach
                                                                                 <option value="addcontact">+ Add New Contact</option>
                                                                             </select>
                                                                         </div>
                                                                         <div class="col-md-12 mb-3">
                                                                             <div class="row">
                                                                                 <div class="col-md-6 ">
                                                                                     <input type="text" 
                                                                                     id="fpunchstartdate{{ $Fpunchl->id }}" 
                                                                                     name="startdate" 
                                                                                     value="{{ $Fpunchl->startdate ? \Carbon\Carbon::parse($Fpunchl->startdate)->format('M d, Y') : 'No start date' }}"  class="form-control form-control-sm">
                                                                                 </div>
                                                                                 <div class="col-md-6 ">
                                                                                     <input type="text" 
                                                                                     id="fpunchenddate{{ $Fpunchl->id }}" 
                                                                                     name="enddate" 
                                                                                     value="{{ $Fpunchl->enddate ? \Carbon\Carbon::parse($Fpunchl->enddate)->format('M d, Y') : 'No end date' }}" 
                                                                                     class="form-control form-control-sm">
                                                                                 </div>
                                                                             </div>
                                                                         </div>
                                                                         <div class="col-md-12 mb-3">
                                                                             <textarea name="description" rows="4" class="form-control form-control-sm"
                                                                                 id="" placeholder="Description">{{ @$Fpunchl->description }}</textarea>
                                                                         </div>
                                                                         <div class="col-md-12 mb-3">

                                                                         <div id="existing-images-{{ @$Fpunchl->id }}">
                                                                             <div class="row">
                                                                             @foreach($Fpunchl->punchlistimg as $doc)
                                                                             <div class="col-md-3" id="image-{{ $doc->id }}">
                                                                                 <div class="image-item">
                                                                                     <button type="button" class="btn btn-danger btn-sm punchlist_image_btn" data-id="{{ $doc->id }}" data-punchlist-id="{{ $Fpunchl->id }}">Delete</button>
                                                                                     <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$Fpunchl->title) }}">
                                                                                         <img src="{{ asset($doc->image) }}" width="150" height="150" alt="{{ ucwords(@$Fpunchl->title) }}" class="img-fluid"/>
                                                                                     </a>
                                                                                     
                                                                                 </div>
                                                                             </div>
                                                                             @endforeach
                                                                             </div>
                                                                         </div>

                                                                     </div>
                                                                     <div class="col-md-12 mb-3">
                                                                         <input type="file" name="punchlist_images[]" class="form-control form-control-sm" multiple>
                                                                     </div>
                                                                         <div class="col-md-12">
                                                                             <button type="submit" class="btn Stage-submit w-100">Edit
                                                                                 Punchlist Item</button>
                                                                         </div>
                                                                     </form>
                                                                 </div>
                                                             </div>
                                                     
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                                <script>

                                                //  jQuery(function () {
                                                //         jQuery('#fpunchstartdate{{ @$Fpunchl->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                //         jQuery('#fpunchenddate{{ @$Fpunchl->id }}').datetimepicker({format:'MMM DD, YYYY'});
                                                //     });
                                        
                                            $(document).ready(function () {
                                                // Initialize Bootstrap datepickers
                                                $('#fpunchstartdate{{ @$Fpunchl->id }}').datepicker({
                                                    format: 'M d, yyyy', // Month abbreviated, Day, Year
                                                autoclose: true,
                                                todayHighlight: true,
                                                }).on('changeDate', function (e) {
                                                    // Get the selected date
                                                    const startDate = $('#fpunchstartdate{{ @$Fpunchl->id }}').datepicker('getDate');
                                                    if (startDate) {
                                                        // Add one day to the start date
                                                        const nextDay = new Date(startDate);
                                                        nextDay.setDate(startDate.getDate() + 1);

                                                        // Set the new date in the enddate field
                                                        $('#fpunchenddate{{ @$Fpunchl->id }}').datepicker('setDate', nextDay);
                                                    }
                                                });

                                                $('#fpunchenddate{{ @$Fpunchl->id }}').datepicker({
                                                    format: 'M d, yyyy', // Month abbreviated, Day, Year
                                                autoclose: true,
                                                todayHighlight: true,
                                                });
                                            });
                                                </script>
                                             </div>
                                                 <!-------------End tasks---------->

                                                 <!-- View Button Trigger Modal -->
                                                 <button class="action-button action-view" data-toggle="modal"
                                                     data-target="#punchlistModal{{ @$Fpunchl->id }}"  {{ (@$punchappsts == 1) ? 'disabled' : '' }} title="View">
                                                     <i class="fa-regular fa-eye"></i>
                                                 </button>
                                                 <!-- Modal for Row 1 -->
                                                 <div class="modal fade" id="punchlistModal{{ @$Fpunchl->id }}" tabindex="-1"
                                                     role="dialog" aria-labelledby="modalLabel1"
                                                     aria-hidden="true">
                                                     <div class="modal-dialog" role="document">
                                                         <div class="modal-content">
                                                             <div class="modal-header">
                                                                 <h5 class="modal-title title-model"
                                                                     id="modalLabel1">{{ @$job_details->first()->name }} </h5>
                                                                 <button type="button" class="btn-close"
                                                                     data-dismiss="modal"
                                                                     aria-label="Close"></button>
                                                             </div>
                                                             <div class="modal-body">
                                                                 <table class="table">
                                                                     <tr>
                                                                         <th>Task Name</th>
                                                                         <td class=" title-model-table text-end">
                                                                             {{ ucwords(@$Fpunchl->title) }}</td>
                                                                     </tr>
                                                                     <tr>
                                                                         <th>Priority</th>
                                                                         <td class="text-end">@if($Fpunchl->priority == 'High')
                                                                             <span class="badge badge-fixed bg-high">{{ @$Fpunchl->priority }}</span>
                                                                         @elseif(@$taskde->priority == 'Medium')
                                                                             <span class="badge badge-fixed bg-medium">{{ @$Fpunchl->priority }}</span>
                                                                         @elseif(@$taskde->priority == 'Complete')
                                                                             <span class="badge badge-fixed bg-complete">{{ @$Fpunchl->priority }}</span>
                                                                          @else
                                                                             <span class="badge badge-fixed bg-low">{{ @$Fpunchl->priority }}</span>
                                                                          @endif
                                                                     
                                                                         </td>
                                                                     </tr>
                                                                     <tr>
                                                                         <th>Room</th>
                                                                         <td class="text-end title-model-table">
                                                                             {{ ucwords(@$Fpunchl->room) }}</td>
                                                                     </tr>
                                                                     <tr>
                                                                         <th>Assign To</th>
                                                                         <td class="text-end title-model-table">
                                                                             {{ @$Fpunchl->contact_name }}</td>
                                                                     </tr>
                                                                     <tr>
                                                                         <th>Start Date</th>
                                                                         <td class="text-end title-model-table">
                                                                             {{ \Carbon\Carbon::parse(@$Fpunchl->startdate)->format('M d, Y') }}</td>
                                                                     </tr>
                                                                 </table>
                                                                 <div>
                                                                     <h6 class="title-model-table">Description</h6>
                                                                     <p class="text-justify">{{ @$Fpunchl->description }}</p>
                                                                 </div>
                                                                 <hr>
                                                                 <div>
                                                                     <h6 class="title-model-table">Document</h6>
                                                                     
                                                                     @foreach($Fpunchl->punchlistimg as $doc)
                                                                     <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$Fpunchl->title) }}">
                                                                         <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$Fpunchl->title) }}" class="img-fluid"/>
                                                                     </a>
                                                                     @endforeach
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </td>
                                         </tr>
                                        
                                         @endforeach
                                         @else
                                         <tr><td colspan="8">There is no record found.</td></tr>
                                         @endif
                                        @endforeach

                                         <!-- Repeat similar structure for other rows, making sure each row has a unique modal ID and button trigger -->
                                     </tbody>

                                 </table>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <h5 class="text-white">Add New Punchlist Item </h5>
                             <div class="bg-white p-3 rounded mt-4" >
                                 <div>
                                     <form method="post" action="{{ route('addfinalpunchlist') }}" enctype="multipart/form-data">
                                         @csrf
                                         <div class=" col-md-12 mb-3">
                                             <input class="form-control form-control-sm" type="hidden" name="job_id"  value=" {{ @$job_details->first()->id }}">
                                             <input  type="text" name="title" value="" placeholder="Task name *" class="form-control form-control-sm" required>
                                         </div>
                                         <div class="col-md-12 mb-3">
                                             <input type="text" name="room" value="" class="form-control form-control-sm"
                                                 placeholder="Room">
                                         </div>
                                         <div class="col-md-12 mb-3">
                                             <select name="priority" class="form-select form-select-sm text-muted" required>
                                                 <option selected disabled value="">Priority</option>
                                                 <option value="High">High</option>
                                                 <option value="Medium">Medium</option>
                                                 <option value="Low">Low</option>
                                             </select>
                                         </div>
                                         <div class="col-md-12  mb-3">
                                             <select class="form-select form-select-sm text-muted" name="assign_to" value="" onchange="addnewContactinaddtask(this.value)" required>
                                                 <option selected value="">Assign To</option>
                                                     @foreach($allcontact as $alcontact)
                                                     <option value=" {{ @$alcontact->id }}"> {{ @$alcontact->name }}</option>
                                                  @endforeach
                                                 <option value="addcontact">+ Add New Contact</option>
                                             </select>
                                         </div>
                                         <div class="col-md-12 mb-3">
                                             <div class="row">
                                                 <div class="col-md-6 ">
                                                     <input type="text" 
                                                     id="startdatepunchlist" 
                                                     name="startdate"  placeholder="StartDate"
                                                     value=""  class="form-control form-control-sm">
                                                 </div>
                                                 <div class="col-md-6 ">
                                                     <input type="text" 
                                                     id="enddatepunchlist" 
                                                     name="enddate" 
                                                     value="" 
                                                     placeholder="EndDate"
                                                     class="form-control form-control-sm">
                                                 </div>
                                             </div>
                                         </div>
                                         <div class="col-md-12 mb-3">
                                             <textarea name="description" rows="4" class="form-control form-control-sm" id="" placeholder="Description"></textarea>
                                         </div>

                                         <div class="col-md-12 mb-3">
                                             <input type="file" name="new_images[]" class="form-control form-control-sm" multiple>
                                         </div>
                                         <div class="col-md-12">
                                             <button type="submit" class="btn Stage-submit w-100">Save New
                                                 Punchlist Item</button>
                                         </div>
                                     </form>
                                 </div>
                             </div>
                         </div>
                     </div>
                       {{--  End Final Punch List   --}}
                     </div>
                     <div class="tab-pane fade show {{ (session('activeTab') == 'stage')?'active':'' }}" id="stage" role="tabpanel" aria-labelledby="stage-tab">
                         <div class="row my-3">
                             <div class="col-md-8">
                                 <h5 class="text-white my-3">Stages Details</h5>
                                 <div class="bg-white">
                                     <table class="table" style="width: 100%;" id="stagesTable">
                                         <tbody>
                                             @foreach($stages as $stg)
                                             @foreach($stg->jobstage as $jobstages)
                                             @if(@$jobstages->stage->id)
                                             {{-- @php
                                             echo'hello-->'. $jobstages->stage->id;
                                             @endphp --}}
                                             <tr draggable="true" data-id="{{ @$jobstages->stage->id }}">
                                                 <td style="width: 80%;">
                                                     <div class="d-flex align-items-center">
                                                         <!-- Skill Circle -->
                                                         <div class="skill" style="margin-right: 15px;">
                                                             <div class="outer-circle" data-percentage="{{ @$jobstages->stage->progress_status }}">
                                                                 <div class="inner-circle">
                                                                     <div class="percentage" id="html">0%</div>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                         <!-- Stage Label -->
                                                         <div class="stage_text">
                                                             <p>{{ @$jobstages->stage->name }}</p>
                                                         </div>
                                                     </div>
                                                 </td>
                                     
                                                 <td style="width: 20%; vertical-align: middle;">
                                                     <div style="display: flex; justify-content:end; align-items: center;">
                                                         <button class="action-button action-edit mx-2" onclick="getjobstagebyid({{ @$jobstages->stage->id }});" title="Edit">
                                                             <i class="fa-regular fa-pen-to-square"></i>
                                                         </button>
                                                         @if($job_details->first()->user_id == auth()->id())
                                                            <button class="action-button action-delete mx-2 jobstagedelete" data-id="{{ @$jobstages->stage->id }}" title="DELETE">
                                                                <i class="fa-solid fa-x"></i>
                                                            </button>
                                                         @endif
                                                     </div>
                                                 </td>
                                             </tr>
                                             @endif
                                             @endforeach
                                         @endforeach
                                             <!-- Text Row -->
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                             <div class="col-md-4">
                                 <h5 class="text-white my-3">Add New Stage</h5>
                                 <div class="row bg-white py-3">
                                     <div>
                                         <form action="{{ route('addJobStage') }}" method="POST">
                                             @csrf
                                             <div class=" col-md-12 mb-3">
                                                 <input class="form-control form-control-sm" type="hidden" name="job_id"  value=" {{ @$job_details->first()->id }}">
                                                 <input class="form-control form-control-sm" type="text"
                                                     name="name" placeholder="Stage Name *" required="">
                                             </div>
                                             <div class="col-md-12">
                                                 <button type="submit" class="btn Stage-submit  w-100">Add
                                                     Stage</button>
                                             </div>
                                         </form>
                                     </div>
                                 </div>
                                 <div id="editstageform"></div>
                                 
                             </div>
                         </div>
                     </div>
                     <div class="tab-pane fade {{ (session('activeTab') == 'document')?'show active':'' }}" id="document" role="tabpanel" aria-labelledby="document-tab">
                         <div class="row my-3">
                             <div class="col-md-8">
                                 <h5 class="text-white my-3">Documents </h5>

                                 <div class="row my-3">
                                     @foreach($jobdocument as $jobdoc)
                                     @foreach($jobdoc->jobmedia as $jobmedia)
                                       
                                     @if($jobmedia->media->type==1)
                                     @php
                                        $fileUrl = asset($jobmedia->media->image);
                                     @endphp
                                     <div class="col-md-4 mb-4">
                                         <div class="bg-white2">
                                             <table>
                                           
                                                     <tr class="document_info">
                                                             <td>
                                                             <a href="{{ $fileUrl }}" download="{{ @$jobmedia->media->image }}">
                                                                 @php
                                                                 // Get the file extension
                                                                 $filename = $jobmedia->media->name;
                                                                 $extension = pathinfo($filename, PATHINFO_EXTENSION);
                                                                @endphp
                                                                
                                                                @if($extension == 'docx')
                                                                    <img src="{{ asset('assets/images/docx.png') }}" title="{{ $jobmedia->media->name }}" alt="{{ $jobmedia->media->name }}" class="img-fluid" width="30" height="28">
                                                                @elseif($extension == 'pdf')
                                                                    <img src="{{ asset('assets/images/pdf.png') }}" title="{{ $jobmedia->media->name }}" alt="{{ $jobmedia->media->name }}" class="img-fluid" width="30" height="28">
                                                                @elseif($extension == 'csv')
                                                                    <img src="{{ asset('assets/images/csv.png') }}" title="{{ $jobmedia->media->name }}" alt="{{ $jobmedia->media->name }}" class="img-fluid" width="30" height="28">
                                                                @elseif($extension == 'xlsx')
                                                                    <img src="{{ asset('assets/images/xlsx.png') }}" title="{{ $jobmedia->media->name }}" alt="{{ $jobmedia->media->name }}" class="img-fluid" width="30" height="28">
                                                                @else
                                                                    <img src="{{ asset('assets/images/defultfile.png') }}" title="{{ $jobmedia->media->name }}" alt="{{ $jobmedia->media->name }}" class="img-fluid" width="30" height="28">

                                                                @endif
                                                             </a>
                                                             </td>
                                                             <td>
                                                                 <div class="doc-info">
                                                                    <a href="{{ $fileUrl }}" download="{{ @$jobmedia->media->image }}">
                                                                     <h4> @if(strlen(@$jobmedia->media->name) >= 15)
                                                                         {{ ucwords(substr(@$jobmedia->media->name, 0, 15)) }} 
                                                                     @else
                                                                         {{ ucwords(@$jobmedia->media->name) }}
                                                                     @endif</h4>
                                                                     <span>
                                                                         @php
                                                                            
                                                                             $createdTime = \Carbon\Carbon::parse($jobmedia->media->created_at);
                                                                         @endphp

                                                                         {{ $createdTime->diffForHumans() }} 
                                                                     
                                                                     </span>
                                                                    </a>
                                                                 </div>
                                                             </td>
                                                             <td width="10%">
                                                                 <div id="docmentselect">
                                                                     <span>
                                                                        
                                                                         <a href="{{ $fileUrl }}" download="{{ @$jobmedia->media->image }}"> <i class="fa-regular fa-circle-down" title="Download"></i></a>
                                                                     </span>
                                                                     @if($job_details->first()->user_id == auth()->id())
                                                                        <span>
                                                                            <i class="fa-regular fa-trash-can deletedoc" data-id="{{ $jobmedia->media->id }}" style="cursor: pointer;" title="Delete"></i>
                                                                        </span>
                                                                     @endif
                                                                 </div>
                                                             
                                                             </td>
                                                     </tr> 
                                             

                                             </table>

                                         </div>
                                     </div>
                                     @endif 
                                     @endforeach
                                    @endforeach
                                   
                                 </div>

                             </div>

                             <div class="col-md-4">
                                 <h5 class="text-white my-3">Add Documents </h5>
                                 <div class="bg-white p-3 rounded">
                                   
                                         <form method="post" action="{{ route('addjobdocument') }}" enctype="multipart/form-data">
                                             @csrf
                                             <input class="form-control form-control-sm" type="hidden" name="job_id" value="{{ @$job_details->first()->id }}">

                                             <div class="col-md-12 mb-3 mt-3">
                                                 <div class="file-input" id="fileInputContainer">
                                                     <!-- Image Preview -->
                                                     <img src="" alt="File Preview" class="file-preview" id="filePreview" style="display: none; max-width: 100%; height: auto;">
                                                     
                                                     <!-- PDF Preview -->
                                                     <iframe id="pdfPreview" style="display: none; width: 100%; height: 150px;" frameborder="0"></iframe>
                                                 
                                                     <!-- Text or Icon Preview for Other Files -->
                                                     <div id="fileTypePreview" style="display: none;">
                                                         <p style="text-align:center"> <i class="fa-regular fa-square-check" aria-hidden="true" style="color:#49B030;font-size: 30px;"></i></p>
                                                         <p id="fileName"></p>
                                                         <p id="fileType"></p>
                                                     </div>
                                                 
                                                     <div id="uploadText">
                                                         <p class="m-0" style="color: #286FAC; font-size: 25px;"><i class="fa-solid fa-cloud-arrow-up"></i></p>
                                                         <p class="m-0">Upload Document</p>
                                                         <button type="button" class="btn bg-286FAC mt-2">Browse File</button>
                                                     </div>
                                                     <input type="file" name="file_name" id="fileUpload">
                                                 </div>
                                             </div>
                                             <div class="col-md-12 mb-3">   
                                                 <input type="text" name="name" value="" placeholder="Document Name " class="form-control form-control-sm" id="docfilename">
                                             </div>
                                             <div class="col-md-12">
                                                 <button type="submit" class="btn Stage-submit w-100">Save Document</button>
                                             </div>
                                         </form>

                                     </div>
                                 </div>
                             </div>
                         </div>
                     <div class="tab-pane fade {{ (session('activeTab') == 'pictures')?'show active':'' }}" id="pictures" role="tabpanel" aria-labelledby="pictures-tab">
                         <div class="row my-3">
                             <div class="col-md-8">
                                 <h5 class="text-white my-3">Pictures </h5>

                                 <div class="row my-3">
                                     @foreach($jobpictures as $jobpic)
                                     @foreach($jobpic->jobmedia as $jobmedia)
                                       
                                     @if($jobmedia->media->type==2)
                                     <div class="col-md-4 mb-4">
                                         @php
                                         $fileUrl = asset($jobmedia->media->image);
                                         @endphp

                                         <div class="bg-white12">
                                             <div class="image-container" id="image-container-{{ @$jobmedia->media->id }}">
                                                 <span class="zoom-icon">
                                                     <i class="fa fa-search-plus zoom-image" data-index="{{ $loop->index }}"></i>
                                                 </span>
                                                 <img src="{{ @$fileUrl }}" alt="{{ $jobmedia->media->name }}"  title="{{ $jobmedia->media->name }}" class="gallery-image" id="image-{{ @$jobmedia->media->id }}" data-index="{{ $loop->index }}"/>
                                                 @if($job_details->first()->user_id == auth()->id())
                                                    <span class="icon delete-icon delete-pics" title="Delete" data-id="{{ @$jobmedia->media->id }}">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </span>
                                                 @endif
                                                 <span class="icon download-icon" title="Download">
                                                     <a href="{{ @$fileUrl }}" download>
                                                         <i class="fa-regular fa-circle-down"></i>
                                                     </a>
                                                 </span>
                                                 
                                             </div>
                                            

                                         </div>
                                     </div>
                                     @endif 
                                     @endforeach
                                    @endforeach
                                   
                                 </div>

                             </div>

                             <div class="col-md-4">
                                 <h5 class="text-white my-3">Add Picture </h5>
                                 <div class="bg-white p-3 rounded">
                                   
                                         <form method="post" action="{{ route('addjobpicture') }}" enctype="multipart/form-data">
                                             @csrf
                                             <input class="form-control form-control-sm" type="hidden" name="job_id" value="{{ @$job_details->first()->id }}">

                                             <div class="col-md-12 mb-3 mt-3">
                                                 <div class="file-input" id="fileInputContainer1">
                                                     <!-- Image Preview -->
                                                     <img src="" alt="File Preview" class="file-preview" id="filePreview1" style="display: none; max-width: 100%; height: auto;">
                                         
                                                     <div id="uploadText">
                                                         <p class="m-0" style="color: #286FAC; font-size: 25px;"><i class="fa-solid fa-cloud-arrow-up"></i></p>
                                                         <p class="m-0">Upload picture</p>
                                                         <button type="button" class="btn bg-286FAC mt-2">Browse File</button>
                                                     </div>
                                                     <input type="file" name="file_name" id="fileUploadpicture" accept="image/*">
                                                 </div>
                                             </div>
                                             <div class="col-md-12 mb-3">   
                                                 <input type="text" name="name" value="" placeholder="Picture Name " class="form-control form-control-sm" id="picfilename">
                                             </div>
                                             <div class="col-md-12">
                                                 <button type="submit" class="btn Stage-submit w-100">Save picture</button>
                                             </div>
                                         </form>

                                     </div>
                                 </div>
                             </div>
                     </div>
                     <div class="tab-pane fade {{ (session('activeTab') == 'contacts')?'show active':'' }}" id="contacts" role="tabpanel"  aria-labelledby="contacts-tab">
                         <div class="row supreme-container">
                             <div class="col-md-12 col-lg-8">
                                 <h5 class="text-white">Contacts</h5>
                                 <div class="row">
                                     <!-- Card 1 -->

                                     @foreach($jobcontacts as $jobconts)
                                        @foreach($jobconts->jobcontact as $contact)
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card p-2">
                                                    <img src="{{ asset($contact->contact->profile_pic?$contact->contact->profile_pic:'no-user.png') }}" class="card-img-top mx-auto"
                                                        alt="Profile Picture">
                                                    <div class="card-body">
                                                        <h5 class="card-title username">{{ $contact->contact->name }}</h5>
                                                        <p class="card-text Designation">{{ $contact->contact->type_name }}</p>
                                                        <div class="d-flex justify-content-between">
                                                            <button type="button" class="btn w-100 mx-1 btn-details "
                                                                data-bs-toggle="modal" data-bs-target="#contact{{ $contact->contact->id }}">
                                                                View Details
                                                            </button>
                                                            @if($job_details->first()->user_id == auth()->id())
                                                                <button class="btn  btn-close-custom removecontactfromjob" data-contact_id="{{ $contact->contact->id }}" data-job_id="{{ @$jobconts->id }}" title="DELETE">X</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade fullscreen" id="contact{{ $contact->contact->id }}" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered contact_modal">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title model-head" id="detailsModalLabel">Contact Details
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ asset($contact->contact->profile_pic?$contact->contact->profile_pic:'no-user.png') }}" alt="Profile Image" class="contact-img" width="48" height="48">
                                                                <div class="model-title-container">
                                                                    <h5 class="modal-title title-model" id="contactModalLabel">{{ $contact->contact->name }}
                                                                    </h5>
                                                                    <p class="mb-0">{{ $contact->contact->type_name }}</p>
                                                                </div>
                                                                <div class="action-buttons-model ms-auto">
                                                                    <button class="btn socal-button" title="Call">
                                                                       <a href="tel:{{ $contact->contact->mobile }}"> <i class="fas fa-phone"></i></a>
                                                                    </button>
                                                                
                                                                    <button class="btn socal-button" title="Mail">
                                                                        <a href="mailto:{{ $contact->contact->email }}"><i class="fas fa-envelope"></i></a>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="row my-3">
                                                                <div class="col-md-6" style="border: 1px solid #E3E3E3;">
                                                                    <p class="mb-1 note-color"><strong>Address:</strong></p>
                                                                    <p class="address-color">{{ $contact->contact->address }}<br>{{ $contact->contact->city }} , {{ $contact->contact->state }}
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-6" style="border: 1px solid #E3E3E3;">
                                                                    <p class="mb-1 note-color"><strong>Notes:</strong></p>
                                                                    <p class="note-color">{{ $contact->contact->contact_notes }}</p>
                                                                </div>
                                                            </div>
                                                            <form  action="{{ route('updatecontactsharedpermission') }}" class="contactsharedpr" method="POST">
                                                                @csrf
                                                                <div class="row mt-3">
                                                                    <div class="col-md-6">
                                                                        <p>Shared with this contact</p>
                                                                    </div>
                                                                    <div class="col-md-6 text-end">
                                                                        <input type="hidden" name="contact_id" value="{{ @$contact->contact->id }}">
                                                                        <input type="hidden" name="job_id"  value="{{ @$jobconts->id }}">
                                                                        <button class="btn contactshrdedit" style="display:none">
                                                                            Update
                                                                        </button>
                                                                        <button class="action-button action-edit contactsharededitbtn" title="Edit">
                                                                            <i class="fa-regular fa-pen-to-square"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                <div class="col-md-12">
                                                                  
                                                                    <table class="table  table-bordered  bg-white jobs-table mt-4">
                                                                        <thead class="jobs-thead">
                                                                            <tr>
                                                                                <th>Name</th>
                                                                                <th>All</th>
                                                                                <th>Assign/Own</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="jobs-table-body">
                                                                            @php
                                                                             $notepad = $contact->contact->contactshared->jobnotepad;
                                                                             $punchlist = $contact->contact->contactshared->punchlist;
                                                                            @endphp
                                                                            <tr>
                                                                                
                                                                                <td>Task Assignment</td>
                                                                            <div class="inputGroup">
                                                                                
                                                                                <td> 
                                                                                    <input type="radio"  name="jobnotepad"  value="1"  {{ $notepad == "1" ? 'checked' : '' }}  disabled/>
                                                                                   
                                                                                </td>
                                                                                <td class="text-end"> 
                                                                                    
                                                                                    <input type="radio" name="jobnotepad" value="0"  {{ $notepad == "0" ? "checked" : '' }}  disabled/>
                                                                                </td>
                                                                            </div>
                                                                            </tr>
                                                                           
                                                                            <tr>
                                                                                <td>Final PunchList</td>
                                                                                <td> 
                                                                                    <input  type="radio" name="punchlist" value="1" {{ ($contact->contact->contactshared->punchlist == 1) ? 'checked' : '' }} disabled/>
                                                                                </td>
                                                                                <td class="text-end"> 
                                                                                    <input  type="radio" name="punchlist" value="0" {{ ($contact->contact->contactshared->punchlist == 0) ? 'checked' : '' }}  disabled/>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">General</td>
                                                                                <td class="text-end">
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="general" value="{{ $contact->contact->contactshared->general}}" {{ (@$contact->contact->contactshared->general == 1) ? 'checked' : '' }} disabled> 
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Calendar</td>
                                                                                <td class="text-end"> 
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="calendar" value="{{ $contact->contact->contactshared->calendar}}" {{ (@$contact->contact->contactshared->calendar == 1) ? 'checked' : '' }} disabled>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Stage</td>
                                                                                <td class="text-end"> 
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="stage"  value="{{ $contact->contact->contactshared->stage}}" {{ (@$contact->contact->contactshared->stage == 1) ? 'checked' : '' }} disabled></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Documents</td>
                                                                                <td class="text-end"> 
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="document" value="{{ $contact->contact->contactshared->document}}" {{ (@$contact->contact->contactshared->document == 1) ? 'checked' : '' }} disabled>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Pictures</td>
                                                                                <td class="text-end">
                                                                                     <input class="form-check-input checkbox-toggle" type="checkbox" name="pictures"  value="{{ $contact->contact->contactshared->pictures}}" {{ (@$contact->contact->contactshared->pictures == 1) ? 'checked' : '' }} disabled></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Contacts</td>
                                                                                <td class="text-end"> 
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="contact" value="{{ $contact->contact->contactshared->contact}}" {{ (@$contact->contact->contactshared->contact == 1) ? 'checked' : '' }} disabled></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">Todo</td>
                                                                                <td class="text-end"> 
                                                                                    <input class="form-check-input checkbox-toggle" type="checkbox" name="todo" value="{{ $contact->contact->contactshared->todo}}" {{ (@$contact->contact->contactshared->todo == 1) ? 'checked' : '' }} disabled></td>
                                                                            </tr>

                                                                        </tbody>
                                                                    </table>
                                                                
                                                                </div>
                                                            </div>
                                                            <!-- Add more content here if needed -->
                                                        </form>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                     @endforeach
                                 
                    
                                 </div>
                             </div>
                             <div class="col-md-12 col-lg-4 ">
                                @if($job_details->first()->user_id == auth()->id())
                                 <h5 class="text-white">Add Contact</h5>
                                 <div class="row bg-white p-3 border">
                                    <div class="col-md-12 mb-3 p-0">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" id="contactSearch" class="form-control" placeholder="Search contact"
                                                aria-label="Search" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                
                                    <div id="contactList">
                                        @foreach(@$allcontact as $jobconts)
                                           
                                                
                                                    <div class="row align-items-center m-auto mt-2 p-2 add-contact-user-row contact-row">
                                                        <div class="col-md-3 col-sm-3 col-lg-3 text-center">
                                                            <img src="{{ asset(@$jobconts->profile_pic ? $jobconts->profile_pic : 'no-user.png') }}" class="rounded-circle"
                                                                alt="{{ @$jobconts->name }}" style="width: 40px; height: 40px;">
                                                        </div>
                                                        <div class="col-md-7 col-sm-7 col-lg-7">
                                                            <div class="media-body">
                                                                <h6 class="mt-0 mb-0 user-name">{{ ucwords(@$jobconts->name) }}</h6>
                                                                <small class="user-des">{{ @$jobconts->type_name }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-2 col-lg-2">
                                                            <form method="Post" action="{{ route('addjobcontactsbyjobid') }}">
                                                                @csrf
                                                                <input type="hidden" name="contact_id" value="{{ @$jobconts->id }}"/>
                                                                <input type="hidden" name="job_id" value="{{ @$job_details->first()->id }}"/>
                                                                <button type="submit" class="btn btn-add addcontactinjob" data-id="{{ @$jobconts->id }}" data-job-id="{{ @$job_details->first()->id }}">+</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                              
                                           
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                             </div>
                         </div>
                         <script>
                            //Contact shared search 
                                document.getElementById('contactSearch').addEventListener('keyup', function() {
                                    var searchTerm = this.value.toLowerCase();
                                    var contactRows = document.querySelectorAll('.contact-row');

                                    contactRows.forEach(function(row) {
                                        var userName = row.querySelector('.user-name').textContent.toLowerCase();
                                        var userDes = row.querySelector('.user-des').textContent.toLowerCase();

                                        if (userName.includes(searchTerm) || userDes.includes(searchTerm)) {
                                            row.style.display = ''; // Show the row
                                        } else {
                                            row.style.display = 'none'; // Hide the row
                                        }
                                    });
                                });
                         </script>
                     </div>
                    
                 </div>
             </div>
         </div>
     </div>
<!-----------add new contact ----------------------------->
<div class="row">
 <div class="modal fade fullscreen" id="add_contact" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title model-head">Create New Contact </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <form method="POST" id="create_contact" action="{{ route('addContact') }}" enctype="multipart/form-data">
                      @csrf
                     <div class="row">
                         <div class="col-md-12">
                            
                             <div class="form-group ">
                                  <div class="add-pic">
                                     <input type="file" name="profile_pic" id="add-pic">
                                     <label for="add-pic"><img class="pro-pic" src="{{ asset('pro-pic.png') }}"></label>
                                     <span>Upload a Picture</span>
                                 </div>
                             </div>
                             <div class="form-group mt-3">
                                 <select name="type" class="form-select form-select-sm form-control form-control-sm" id="contact_type" required>
                                     <option selected disabled value="">Select Contact type</option>
                                     <option value="1"> Client </option>
                                     <option value="4"> General Contractor </option>
                                     <option value="2"> Sub Contractor </option>
                                     <option value="5"> Architect/ Engineer </option>
                                     <option value="6"> Interior Designer </option>
                                     <option value="3"> Employee </option>
                                     <option value="7"> Inspector </option>
                                 
                                 </select>
                             </div>

                             <div class="form-group mt-3">
                              <input id="phone" class="form-control form-control-sm " type="tel" name="mobile" placeholder="Phone Number *" required />
                             </div>
                             <div class="form-group mt-3">
                                 <input class="form-control form-control-sm " type="text" name="name" placeholder="Name*" required />
                             </div>
                             <div class="additionalfield"></div>
                             <div class="form-group mt-3">
                                 <input class="form-control form-control-sm " type="email" name="email" placeholder="Email*" required />
                             </div>
                         </div>
                         <div class="col-md-12 mt-4">
                             <h5 class="Client-Address">Client’s Address</h5>
                         </div>
                         <div class="col-md-12 mt-3">
                             <input class="form-control form-control-sm" type="text" name="address"
                                 placeholder="Street address "  >
                         </div>
                         <div class="col-md-12 mt-3">
                             <input class="form-control form-control-sm" type="text" name="city"
                                 placeholder="Town/City "  >
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
                                 <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3" value="submit">Create</button>
                         </div>
                     </div>
                 </form>
                 <!-- Add more content here if needed -->
             </div>
             
         </div>
     </div>
 </div>
</div>
</div>
<!-----------------end new contact ----------------------->
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@section('script')
<script>

// $(function () {
//     $('#startdatepunchlist').datetimepicker({format:'MMM DD, YYYY'});
//     $('#enddatepunchlist').datetimepicker({format:'MMM DD, YYYY'});
// });
    //calendar
$(document).ready(function() {
         var date = new Date();
         var dd = date.getDate();
         var mm = date.getMonth() + 1;
         var yyyy = date.getFullYear();
         var fullmonth = date.toLocaleString('default', { month: 'long' });
         if(mm < 10)
         {
             mm = '0'+mm;
         }
         if(dd < 10)
         {
             dd = '0'+dd;
         }
         newDate = yyyy + '-' + mm + '-' + dd;
            if(newDate){
                jQuery.ajax(
                {
                type: "POST",
                url: "{{route('gettasksandpunchlistbydate')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    'event_date': newDate,
                    'job_id' : {{ @$job_details->first()->id }},

                    },
                    success: function(res) 
                    {
                        
                        $('#taskdata').html('');
                        var sn = 1;
                        if((res.data.length > 0) || (res.punchlist.length > 0)){
                                if(res.data.length > 0)
                                {
                                    for (var i = 0; i < res.data.length; i++) {
                                            // Accessing each task
                                            var task = res.data[i];
                                            // Loop through each task assignment (if applicable)
                                            
                                            for (var j = 0; j < task.taskassignment.length; j++) {
                                                var taskAssignment = task.taskassignment[j];
                                                // Example: Display task assignment details

                                                var taskname = taskAssignment.title;
                                                var assignto = taskAssignment.contact_name;
                                                $('#taskdata').append('<tr><td>'+ sn +'</td><td>'+ taskname +'</td><td>'+ assignto +'</td></tr>');
                                            sn++;
                                            }
                                        } 
                                }
                                if(res.punchlist.length > 0)
                                {
                                    for (var i = 0; i < res.punchlist.length; i++) {
                                            // Accessing each task
                                            var punchl = res.punchlist[i];
                                            // Loop through each task assignment (if applicable)
                                            
                                            for (var j = 0; j < punchl.punchlist.length; j++) {
                                                var PunchList = punchl.punchlist[j];
                                                // Example: Display task assignment details

                                                var punchlistname = PunchList.title;
                                                var Assignto = PunchList.contact_name;
                                                $('#taskdata').append('<tr><td>'+sn+'</td><td>'+ punchlistname +'</td><td>'+ Assignto +'</td></tr>');
                                            sn++;  
                                            }
                                        } 
                                }
                        
                            }else{

                                $('#taskdata').append('<tr><td colspan="3">There are no tasks for this date.</td></tr>');
                            
                            }

                        
                    }
                });

            }

     jQuery('#taskcalendar').fullCalendar({
         firstDay: 1,
         businessHours: false,
         defaultView: 'month',
         showNonCurrentDates:true,
         fixedWeekCount:false,
         contentHeight:"auto",
         handleWindowResize:true,
         themeSystem:'bootstrap4',
         // event dragging & resizing
         editable: false,
         // header
         header: {
             center:'title',
             left:'prev',
             right: 'next'
         },
         events: [
                 @foreach($tasks as $task)
                     @foreach($task->taskassignment as $taskde)
                         @php
                             $startDate = \Carbon\Carbon::parse($taskde->startdate);
                             $endDate = \Carbon\Carbon::parse($taskde->enddate);
                             $dates = [];
                             
                             // Generate all dates from start to end
                             while ($startDate->lte($endDate)) {
                                 $dates[] = $startDate->format('Y-m-d');
                                 $startDate->addDay();
                             }
                         @endphp

                         @foreach($dates as $date)
                             {
                                 title: '{{ @$taskde->title }}',
                                 start: '{{ $date }}',  
                                 icon: 'circle',
                             },
                         @endforeach
                     @endforeach
                 @endforeach

                 // punch list data represent

                 @foreach(@$punchlists as $punchlis)
                     @foreach (@$punchlis->punchlist as $punchdata)
                         @php
                             $startDate = \Carbon\Carbon::parse($punchdata->startdate);
                             $endDate = \Carbon\Carbon::parse($punchdata->enddate);
                             $dates = [];
                             while ($startDate->lte($endDate)) {
                                     $punchdates[] = $startDate->format('Y-m-d');
                                     $startDate->addDay();
                                 }
                         @endphp
                                 @if(@$punchdates)
                                    @foreach($punchdates as $date1)
                                        {
                                            title: '{{ @$punchdata->title }}',
                                            start: '{{ $date1 }}',  
                                            icon: 'circle',
                                        },
                                    @endforeach
                                @endif

                     @endforeach
                   @endforeach


             ],
             eventRender: function(event, element) {
                 if(event.icon) {
                     element.find(".fc-content").prepend("<i class='fa fa-" + event.icon + "'></i>");
                     element.find(".fc-title").hide();
                 }
                 var currentEvents = jQuery('#taskcalendar').fullCalendar('clientEvents', function(ev) {
                 return ev.start.format('YYYY-MM-DD') === event.start.format('YYYY-MM-DD') && ev.rendering !== 'background';
             });
             // If there's more than one visible event for this date, hide this event
             if (currentEvents.length > 1 && event._id !== currentEvents[0]._id) {
                 currentEvents.slice(1).forEach(function(ev) {
                     jQuery('#taskcalendar').fullCalendar('removeEvents', ev._id);
                 });
             }
             },
           
             // Handle event clicks
             eventClick: function(event, jsEvent, view) {
                // alert('Event: ' + event.start+'title->' + event.title);
                $('.fc-event').removeClass('highlighted-event');
                    
                    // Add highlight to the clicked event
                    $(this).addClass('highlighted-event');
                
                var date1 = event.start.format();
                var title = event.title;
                
                jQuery.ajax(
                 {
                     type: "POST",
                     url: "{{route('gettasksandpunchlistbydate')}}",
                     data: {
                         _token: "{{ csrf_token() }}",
                         'event_date': date1,
                         'job_id' : {{ @$job_details->first()->id }},

                     },
                     success: function(res) 
                     {
                         //$("#showdlt").html(res);
                         $('#taskdata').html('');
                         var pq = 1;
                         if((res.data.length > 0) || (res.punchlist.length > 0)){
                         if(res.data.length > 0)
                         {
                             for (var i = 0; i < res.data.length; i++) {
                                    
                                     var task = res.data[i];
                                    
                                     for (var j = 0; j < task.taskassignment.length; j++) {
                                         var taskAssignment = task.taskassignment[j];
                                        var taskname = taskAssignment.title;
                                        var assignto = taskAssignment.contact_name;
                                         $('#taskdata').append('<tr><td>'+ pq +'</td><td>'+ taskname +'</td><td>'+ assignto +'</td></tr>');
                                         
                                         pq++; 
                                     }
                                 } 
                         }
                         if(res.punchlist.length > 0)
                         {
                             for (var i = 0; i < res.punchlist.length; i++) {
                                     // Accessing each task
                                     var punchl = res.punchlist[i];
                                    
                                     for (var j = 0; j < punchl.punchlist.length; j++) {
                                         var PunchList = punchl.punchlist[j];
                                        

                                        var punchlistname = PunchList.title;
                                        var Assignto = PunchList.contact_name;
                                         $('#taskdata').append('<tr><td>'+  pq +'</td><td>'+ punchlistname +'</td><td>'+ Assignto +'</td></tr>');
                                         pq++; 
                                     }
                                 } 
                         }
                         
                        }else{

                            $('#taskdata').append('<tr><td colspan="3">There are no tasks for this date.</td></tr>');

                            }

                         
                     }
                 });

             }
             
         });


 });

 // end of calendar
    // pictures zoom functions
$(document).ready(function(){
 $('.zoom-image').on('click', function() {
     var currentIndex = $(this).data('index');
     var imageElements = $('.gallery-image').map(function() {
         return {
             src: $(this).attr('src'),
             opts: { thumb: $(this).attr('src') }
         };
     }).get();

     $.fancybox.open(imageElements, {
         loop: true, 
         arrows: true, 
     }, currentIndex); 
 });

});
//Deletejobcontact
$(document).on('click', '.removecontactfromjob', function(e) {
    e.preventDefault();
    
    // Get the contact ID and job ID from the button's data attributes
    var contact_id = $(this).data('contact_id');
    var job_id = $(this).data('job_id');
    var button= $(this);
    // Confirm the action before proceeding
    if (confirm('Are you sure you want to remove this contact from the job?')) {
        $.ajax({
            url: '{{ route("deletejobcontact") }}', // Replace with your route
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',  // CSRF token for security
                contact_id: contact_id,
                job_id: job_id
            },
            success: function(response) {
                // Handle success
                if(response.success) {
                    alert('Contact removed successfully.');
                    button.closest('.col-md-6').fadeOut(500, function() {
                     $(this).remove();
                 });
                } else {
                    alert('Failed to remove contact.');
                }
            },
            error: function(xhr, status, error) {
                // Handle error
                alert('An error occurred. Please try again.');
            }
        });
    }
});




//contact shared
$(document).ready(function () {
    $(document).on('change', '.checkbox-toggle', function () {
        // Toggle the checkbox value based on its checked state
        if ($(this).is(':checked')) {
            $(this).val(1); // Set value to 1 when checked
        } else {
            $(this).val(0); // Set value to 0 when unchecked
        }
    });

});

$(document).on('click', '.contactsharededitbtn', function(e) {
    e.preventDefault();  
    var $form = $(this).closest('form');
    $form.find('input').removeAttr('disabled');
    $form.find('.contactshrdedit').show();
    
});

// upload job pictures

$(document).on('click', '.delete-pics', function() {
 var mediaId = $(this).data('id'); 
 var button = $(this);
 if (confirm('Are you sure you want to delete this file picture?')) {
     $.ajax({
         url: '{{ route('deletejobattachment' )}}', 
         type: 'POST',
         data: {
             _token: '{{ csrf_token() }}', 
             media_id: mediaId
         },
         success: function(response) {
             if (response.success) {
                 alert('File deleted successfully');
                 button.closest('.col-md-4').fadeOut(500, function() {
                     $(this).remove();
                 });
             } else {
                 alert('Error deleting file.');
             }
         },
         error: function(xhr) {
             alert('An error occurred: ' + xhr.statusText);
         }
     });
 }
});

document.getElementById('fileUploadpicture').addEventListener('change', function(event) {
 const file = event.target.files[0];
 const filePreview = document.getElementById('filePreview1');
 const fileName = document.getElementById('fileName');
 const fileType = document.getElementById('fileType');
 
 // Hide all previews initially
 filePreview.style.display = 'none';
 pdfPreview.style.display = 'none';
 fileTypePreview.style.display = 'none';
 $('#picfilename').val( file.name);
 if (file) {
     const fileType = file.type;
     const fileURL = URL.createObjectURL(file);

     
     if (fileType.startsWith('image/')) {
         filePreview.src = fileURL;
         filePreview.style.display = 'block';
     }
 }
});

$(document).on('click', '.deletedoc', function() {
 var mediaId = $(this).data('id'); // Get the media ID from data-id attribute
 var button = $(this);
 //if (confirm('Are you sure you want to delete this file?')) {
     $.ajax({
         url: '{{ route('deletejobattachment' )}}', 
         type: 'POST',
         data: {
             _token: '{{ csrf_token() }}', 
             media_id: mediaId
         },
         success: function(response) {
             if (response.success) {
                // alert('File deleted successfully');
                 button.closest('.col-md-4').fadeOut(500, function() {
                     $(this).remove();
                 });
             } else {
                 alert('Error deleting file.');
             }
         },
         error: function(xhr) {
             alert('An error occurred: ' + xhr.statusText);
         }
     });
 //}
});

document.getElementById('fileUpload').addEventListener('change', function(event) {
 const file = event.target.files[0];
 const filePreview = document.getElementById('filePreview');
 const pdfPreview = document.getElementById('pdfPreview');
 const fileTypePreview = document.getElementById('fileTypePreview');
 const fileName = document.getElementById('fileName');
 const fileType = document.getElementById('fileType');
 
 // Hide all previews initially
 filePreview.style.display = 'none';
 pdfPreview.style.display = 'none';
 fileTypePreview.style.display = 'none';
 $('#docfilename').val( file.name);
 if (file) {
     const fileType = file.type;
     const fileURL = URL.createObjectURL(file);

     // Check if the file is an image
     if (fileType.startsWith('image/')) {
         filePreview.src = fileURL;
         filePreview.style.display = 'block';
     } 
     // Check if the file is a PDF
     else if (fileType === 'application/pdf') {
         pdfPreview.src = fileURL;
         pdfPreview.style.display = 'block';
     } 
     // For other file types (e.g., DOC, DOCX)
     else {
         fileName.textContent = 'File Name: ' + file.name;
         fileType.textContent = 'File Type: ' + fileType;
         fileTypePreview.style.display = 'block';
     }
 }
});


$(document).on('click', '.cancel_edit_stage', function() {
 $('#editstageform').hide();
});
$('.jobstagedelete').click(function() {
 var button = $(this);
 var itemId = button.data('id');  
 
 // Confirm deletion
 //if (confirm('Are you sure you want to delete this job stage?')) {
     $.ajax({
         url: '{{ route('deleteStage') }}',  
         method: 'POST',
         data: {
             _token: '{{ csrf_token() }}',  
             id: itemId
         },
         success: function(response) {
             //alert(response.message);
             button.closest('tr').remove();
         },
         error: function(xhr) {
             console.error('Error deleting item');
         }
     });
 //}
});
//drap and drop order table row
document.addEventListener('DOMContentLoaded', (event) => {
 const table = document.getElementById('stagesTable');
 let draggedRow = null;

 table.addEventListener('dragstart', function (e) {
     if (e.target.tagName === 'TR') {
         draggedRow = e.target;  // Save the dragged row
         e.target.style.opacity = 0.5;  // Add some visual feedback
     }
 });

 table.addEventListener('dragend', function (e) {
     e.target.style.opacity = ''; 
 });

 table.addEventListener('dragover', function (e) {
     e.preventDefault(); 
 });

 table.addEventListener('drop', function (e) {
     e.preventDefault();
     let targetRow = e.target.closest('tr');
     if (draggedRow && targetRow && draggedRow !== targetRow) {
         const tbody = table.querySelector('tbody');  
         if (tbody.contains(targetRow)) {
             tbody.insertBefore(draggedRow, targetRow);
             saveNewOrder();
         }
     }
 });
});


// Function to save the new order after dragging
function saveNewOrder() {
 const rows = document.querySelectorAll('#stagesTable tr');
 const newOrder = [];

 rows.forEach((row, index) => {
     newOrder.push({
         stage_id: row.getAttribute('data-id'),
         position: index + 1
     });
 });
 $.ajax({
     url: '{{route('stageorder')}}',
     method: 'POST',
     data: {  rearrangeorder: newOrder,
              job_id: {{ @$job_details->first()->id }},
             _token: '{{ csrf_token() }}' 
         },
     success: function(response) {
        
         //alert(response.message);
     }
 });
 
}


// Stage progress Slider 
$(document).on('input', '#progressRange', function() {
 var value = $(this).val();  
 $('#progress-value').text(value + '%');  
 $('#progress-hidden-input').val(value);  
});
// check all punch list by Client

function getjobstagebyid(id)
{
 $('#editstageform').show();
var stage_id = id; 
 $.ajax({
         url: "{{route('getjobstageByid')}}", 
         method: 'POST',
         data: {
              id:stage_id,
              job_id: {{ @$job_details->first()->id }},
             _token: '{{ csrf_token() }}'  
         },
         success: function(response) {
            
             var name= response.name;
             var id = response.id;
             var progress_status =  response.progress_status;

             // Assuming progress_status, id, and name are already defined

                 $('#editstageform').html(`
                     <h5 class="text-white my-3">Edit Stage "${name}"</h5>
                     <div class="row bg-white py-3">
                         <div>
                             <form action="{{route('updateStage')}}" method="POST">
                                 @csrf
                                 <div class="col-md-12">
                                     <div class="text-center mb-3">
                                         <p class="font-495057 fw-600">Use the slider to change stage progress</p>
                                         <div id="progress-value" class="progress-value">${progress_status}%</div>
                                         <input type="hidden" name="id" value="${id}">
                                         <input type="hidden" id="progress-hidden-input" name="progress_status" value="${progress_status}">
                                     </div>
                                     <div class="text-center mb-3">
                                         <input type="range" class="custom-range w-100" id="progressRange" value="${progress_status}" min="0" max="100">
                                     </div>
                                     <div class="form-group mt-4">
                                         <input type="text" name="stage_name" value="${name}" class="form-control" placeholder="Stage">
                                     </div>
                                     <div class="col-md-12 mt-3">
                                         <div class="row">
                                             <div class="col-md-6">
                                                 <button type="button" class="btn btn-outline-danger w-100 cancel_edit_stage">Cancel</button>
                                             </div>
                                             <div class="col-md-6">
                                                 <button type="submit" class="btn bg-286FAC w-100">Update</button>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </form>
                         </div>
                     </div>
                 `);

         },
         error: function(xhr, status, error) {
             console.error('Error:', error);
         }
     });
}

$(document).ready(function() {

 $('#punchlistallchecksbox').on('click', function() {
     sendCheckedValues();
 });


 $('.punchlist-checkbox').on('change', function() {
     var allChecked = $('.punchlist-checkbox').length === $('.punchlist-checkbox:checked').length;
     sendCheckedValues();
 });

 // Function to send checked checkbox values via AJAX
 function sendCheckedValues() {
     var checkedValues = [];
     $('.punchlist-checkbox:checked').each(function() {
         checkedValues.push($(this).val());
     });

     // Perform the AJAX request
     $.ajax({
         url: "{{route('updateallPunchlist')}}",  
         method: 'POST',
         data: {
             job_id: {{ @$job_details->first()->id }},
             _token: '{{ csrf_token() }}'  
         },
         success: function(response) {
             if(response.message){
                 $("#punchlistallchecksbox span").html("");
                 $("#punchlistallchecksbox span").html(response.message);
             }

         },
         error: function(xhr, status, error) {
             console.error('Error:', error);
             // Handle the error here
         }
     });
 }
});


// End all punch list by client

 
 $(document).ready(function() {
    //  $('#editgeneral').on('click', function() {
    //      $('.update_job_general').show(); 
    //  });

    $('#editgeneral').on('click', function() {
    // Check the permission data attribute
        var permission = $(this).data('permission');

        if (permission === 'allowed') {
            // Show the update job form if the user has permission
            $('.update_job_general').show(); 
        } else {
            // Alert or display a message if the user does not have permission
            alert('You have no permission to make changes');
        }
    });
     $('#cancel_general').on('click', function() {
         $('.update_job_general').hide();  
     });
 });
 jQuery(function () {
        jQuery('#startdate').datetimepicker({format:'MMM DD, YYYY'});
        jQuery('#enddate').datetimepicker({format:'MMM DD, YYYY'});
    });

</script>
<script>



function addnewContactinaddtask(value) {
 if (value === 'addcontact') {
     var credit_contact = @json(Auth::user()->credit_contact);
     if (credit_contact > 0) {
             setTimeout(function() {
                 $('#add_contact').modal('show');
             }, 300);
         } else {
             alert('You do not have enough credits. Please buy more contacts.');
             $('#Buycredit').modal('show');
         }
 }
}

function addnewContact(value,id) {
 if (value === 'addcontact') {
     $('#edit' + id + ' .btn-close').click();
     var credit_contact = @json(Auth::user()->credit_contact);
     if (credit_contact > 0) {
             setTimeout(function() {
                 $('#add_contact').modal('show');
             }, 300);
         } else {
             alert('You do not have enough credits. Please buy more contacts.');
             $('#Buycredit').modal('show');
         }
 }
}

// add fields in add contact form

$(document).ready(function() {
 $('#contact_type').change(function() {
     // Get the selected value
     var selectedValue = $(this).val();

     // Select the container where the input fields will be added
     var container = $('.additionalfield');

     // Clear the container before adding new input fields
     container.html('');

     // Logic to add input fields based on the selected value
     if (selectedValue == '2' || selectedValue == '4' || selectedValue == '5' || selectedValue == '6'){ // Sub Contractor
         container.append(`
             <div class="col-md-12 mt-3">
               <input class="form-control form-control-sm" type="text" name="business_name"
                         placeholder="Business name " >
             </div>
             <div class="row">
                 <div class="col-md-6 mt-3">
                     <input class="form-control form-control-sm" type="text" name="license_no"
                         placeholder="License no" >
                 </div>
                 <div class="col-md-6 mt-3">
                     <input class="form-control form-control-sm" type="text" name="trade"
                         placeholder="Trade" >
                 </div>
             </div>

         `);
     } else if (selectedValue == '3') { // Architect/Engineer
         container.append(`
              <div class="row">
                 <div class="col-md-6 mt-3">
                     <input class="form-control form-control-sm" type="text" name="social_security_no"
                         placeholder="Social Security num" >
                 </div>
                 <div class="col-md-6 mt-3">
                     <input class="form-control form-control-sm" type="text" name="trade"
                         placeholder="Trade" >
                 </div>
             </div>
         `);
     } 
     // Add more conditions for other contact types as needed
 });
});


$(document).ready(function() {

 //punchlist task handle
 $('.punchlist-checkbox').change(function() {
         var checkbox = $(this);
         var isChecked = checkbox.is(':checked');
         var punchlistId = checkbox.val();
         
         // AJAX request
         $.ajax({
             url: '{{ route('updatefinalpunchlist') }}', 
             method: 'POST',
             data: {
                 _token: '{{ csrf_token() }}',  
                 punchlist_id: punchlistId,
                 status: isChecked ? 1 : 0
             },
             success: function(response) {
                 //alert(response.message);
                 //reloadPageWithDelay();

             },
             error: function(xhr) {
                 console.error('Error updating status');
             }
         });
     });

 //End of punchlist task handle

 // Handle image deletion
 $(document).on('click', '.punchlist_image_btn', function() {
     var imageId = $(this).data('id');
     var punchlistId  =  $(this).data('punchlist-id');
     if (confirm('Are you sure you want to delete this image?')) {
         $.ajax({
             url: '{{ route("deletesinglepunchlistAttachment") }}',  
             method: 'POST',
             data: {
                 _token: '{{ csrf_token() }}',
                 id: imageId,
                 punch_id: punchlistId
             },
             success: function(response) {
                 if (response.success) {
                     $('#image-' + imageId).remove();  
                 } else {
                     //alert(response.message);  
                 }
             }
         });
     }
 });
 $(document).on('click', '.delete-image-btn', function() {
     var imageId = $(this).data('id');
     var taskId  =  $(this).data('task-id');
     if (confirm('Are you sure you want to delete this image?')) {
         $.ajax({
             url: '{{ route("deletetaskassignmentattachement") }}',  
             method: 'POST',
             data: {
                 _token: '{{ csrf_token() }}',
                 id: imageId,
                 taskassignment_id: taskId
             },
             success: function(response) {
                 if (response.success) {
                     $('#image-' + imageId).remove();  
                 } else {
                     //alert(response.message);  
                 }
             }
         });
     }
 });
     // Event listener for checkbox change
     $('.taskcheckbox').change(function() {
         var checkbox = $(this);
         var isChecked = checkbox.is(':checked');
         var taskId = checkbox.val();
         
         // AJAX request
         $.ajax({
             url: '{{ route('aprovesingletask') }}',  
             method: 'POST',
             data: {
                 _token: '{{ csrf_token() }}', 
                 taskassignment_id: taskId,
                 status: isChecked ? 1 : 0
             },
             success: function(response) {
                 //alert(response.message);
                 reloadPageWithDelay();

             },
             error: function(xhr) {
                 console.error('Error updating status');
             }
         });
     });
     function reloadPageWithDelay() {
         setTimeout(function() {
             // Ensure reload is triggered
             window.location.href = window.location.href;
         }, 1000);  // 2000 milliseconds = 2 seconds
     }

     $('.dsingletask').click(function() {
         var button = $(this);
         var itemId = button.data('id'); 
         
         // Confirm deletion
         //if (confirm('Are you sure you want to delete this item?')) {
             $.ajax({
                 url: '{{ route('deletesingletask') }}', 
                 method: 'POST',
                 data: {
                     _token: '{{ csrf_token() }}',  
                     id: itemId
                 },
                 success: function(response) {
                     // Display a success message
                     //alert(response.message);
                     button.closest('tr').remove();
                 },
                 error: function(xhr) {
                     //console.error('Error deleting item');
                 }
             });
        // }
     });

     $('.action-delete-punchlist').click(function() {
         var button = $(this);
         var itemId = button.data('id'); 
         
         // Confirm deletion
         //if (confirm('Are you sure you want to delete this item?')) {
             $.ajax({
                 url: '{{ route('deletesinglepunchlist') }}',  
                 method: 'POST',
                 data: {
                     _token: '{{ csrf_token() }}', 
                     id: itemId
                 },
                 success: function(response) {
                     //alert(response.message);
                     button.closest('tr').remove(); 
                 },
                 error: function(xhr) {
                     //console.error('Error deleting item');
                 }
             });
         //}
     });

 });

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
 
</script>
<script>
    const input = document.querySelector("#phone");
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
            document.querySelector("#phone").value = fullNumber;
    
            // Now, submit the form
            this.submit();
        });
    </script>

{{-- TODO SECTION AND TASKS --}}
<script>
$('#addSectionBtn').on('click', function() {
    let sectionName = $('#newSection').val();
    if(sectionName) {
        $.ajax({
            url: '{{ route('AddToDoSection') }}',
            method: 'POST',
            data: {  _token: '{{ csrf_token() }}', sec_name: sectionName,job_id:{{ @$job_details->first()->id }} },
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
            //alert('Failed to delete section: ' + response.message);
        }
    },
    error: function(xhr) {
       // alert('An error occurred. Please check the console for details.');
        console.error('Error:', xhr.status, xhr.statusText, xhr.responseText);
    }
});

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
   // if (confirm("Are you sure you want to complete this task?")) {
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
   // }
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
                //alert('Task deleted successfully');
                $(`#task-${taskId}`).remove(); // Remove task from DOM
            } else {
                alert('Failed to delete task');
            }
        },
        error: function(xhr) {
            //alert('An error occurred. Please try again.');
            console.error(xhr.responseText);
        }
    });
//}
});

</script>
<script>
$(document).ready(function () {
    // Initialize Bootstrap datepickers
    $('#taskstartd').datepicker({
        format: 'M d, yyyy', // Month abbreviated, Day, Year
    autoclose: true,
    todayHighlight: true,
    }).on('changeDate', function (e) {
        // Get the selected date
        const startDate = $('#taskstartd').datepicker('getDate');
        if (startDate) {
            // Add one day to the start date
            const nextDay = new Date(startDate);
            nextDay.setDate(startDate.getDate() + 1);

            // Set the new date in the enddate field
            $('#taskendd').datepicker('setDate', nextDay);
        }
    });

    $('#taskendd').datepicker({
        format: 'M d, yyyy', // Month abbreviated, Day, Year
    autoclose: true,
    todayHighlight: true,
    });
});

$(document).ready(function () {
    // Initialize Bootstrap datepickers
    $('#startdatepunchlist').datepicker({
        format: 'M d, yyyy', // Month abbreviated, Day, Year
    autoclose: true,
    todayHighlight: true,
    }).on('changeDate', function (e) {
        // Get the selected date
        const startDate = $('#startdatepunchlist').datepicker('getDate');
        if (startDate) {
            // Add one day to the start date
            const nextDay = new Date(startDate);
            nextDay.setDate(startDate.getDate() + 1);

            // Set the new date in the enddate field
            $('#enddatepunchlist').datepicker('setDate', nextDay);
        }
    });

    $('#enddatepunchlist').datepicker({
        format: 'M d, yyyy', // Month abbreviated, Day, Year
    autoclose: true,
    todayHighlight: true,
    });
});

</script>
@stop