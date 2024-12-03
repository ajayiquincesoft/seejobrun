@extends('user.layout.userdashboard')
@section('content')
    <div class="container-fluid content ">

        <div class="row">
            <div class="col-md-12 d-flex justify-content-between">
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
                <div class="alert alert-success" style="width: 100%;">{{session('success')}}</div>
                @endif
                @if(session('error'))
                   <div class="alert alert-danger" style="width: 100%;">
                       {{ session('error') }}
                   </div>
               @endif
            </div>
        </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-between">
                    <div>
                        <h5 class="text-white">My Daily Tasks</h5>
                    </div>
                    <div >
                        @if(isset($hiddentasks))
                        <a href="{{ route('MyDailyTasks') }}"> <div  class="btn active-btn-details"><< Back</div></a>
                        @endif
                           
                           <a href="{{ route('HiddenTasks') }}"> <div  class="btn active-btn-details">Hidden Tasks</div></a>
                       
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-3 col-sm-12">
                    
                    <div class="mt-2 ">
                        <div class="calendar-container shadow rounded table-responsive">
                            <div id="taskcalendar"></div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-9 col-sm-12">
                   @if(isset($hiddentasks)) 
                    <div>
                        <h5 class="text-white" id="todaydate">Hidden Tasks</h5>
                        <div class="table-responsive">
                            <table class="table  table-bordered  bg-white jobs-table mt-4" id="myhiddentask">
                                <thead class="jobs-thead">
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Room</th>
                                        <th>Assign To</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body" id="datewisetask">
                            @if(@$hiddentasks) 
                          
                                @foreach($hiddentasks as $hitask)
                                    @foreach(@$hitask->taskassignment as $task)
                                   
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="task{{ @$task->id }}" name="taskid" value="{{ @$task->id }}" class="form-check-input big-checkbox task_checkbox" {{ ($task->status==1)?'checked':'' }}>
                                                <label class="form-check-label checkbox-label th-ontime" for="task1">
                                                    {{ ucwords(@$task->title) }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{  $task->room }}</td>
                                       <td class="type_name_task">{{ $task->contact_name }} <br/>
                                        <span>{{ $task->type_name }}</span>
                                      </td>
                                        <td>{{ \Carbon\Carbon::parse(@$task->startdate)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse(@$task->enddate)->format('M d, Y') }}</td>
                                        <td data-sort="{{ $task->priority }}">
                                            @if($task->priority=='Complete' )
                                            <span class="badge badge-fixed bg-Complete">Complete</span>
                                            @elseif($task->priority=='High')
                                            <span class="badge badge-fixed bg-High">High</span>
                                            @elseif($task->priority=='Medium')
                                            <span class="badge badge-fixed bg-medium">Medium</span>
                                            @else
                                            <span class="badge badge-fixed bg-Low">Low</span>
                                            @endif
                                        </td>
                                        <td data-sort="actions">
                                            <button class="btn btn-sm btn-outline-secondary text-286FAC view-task-btn" data-toggle="modal"
                                            data-target="#viewModal{{ @$task->id }}" data-task-id="{{ @$task->id }}" title="View">
                                                View
                                            </button>
                                            <!-- Modal for Row 1 -->
                                    <div class="modal fade" id="viewModal{{ @$task->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title title-model"
                                                        id="modalLabel1">{{ @$hitask->name }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <tr>
                                                            <th>Task Name</th>
                                                            <td class=" title-model-table text-end">
                                                                {{ ucwords(@$task->title) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Priority</th>
                                                            <td class="text-end">@if($task->priority == 'High')
                                                                <span class="badge badge-fixed bg-high">{{ @$task->priority }}</span>
                                                            @elseif($task->priority == 'Medium')
                                                                <span class="badge badge-fixed bg-medium">{{ @$task->priority }}</span>
                                                            @elseif($task->priority == 'Complete')
                                                                <span class="badge badge-fixed bg-complete">{{ @$task->priority }}</span>
                                                            @else
                                                                <span class="badge badge-fixed bg-low">{{ @$task->priority }}</span>
                                                            @endif
                                                        
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Room</th>
                                                            <td class="text-end title-model-table">
                                                                {{ ucwords(@$task->room) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Assign To</th>
                                                            <td class="text-end title-model-table">
                                                                {{ @$task->contact_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Start Date</th>
                                                            <td class="text-end title-model-table">
                                                                {{ \Carbon\Carbon::parse(@$task->startdate)->format('M d, Y') }}</td>
                                                        </tr>
                                                    </table>
                                                    <div>
                                                        <h6 class="title-model-table">Description</h6>
                                                        <p class="text-justify">{{ @$task->description }}</p>
                                                    </div>
                                                    <hr>
                                                    <div>
                                                        <h6 class="title-model-table">Document</h6>
                                                        
                                                        @foreach($task->taskassignmentimages as $doc)
                                                        <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$task->title) }}">
                                                            <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$task->title) }}" class="img-fluid"/>
                                                        </a>
                                                        @endforeach
                                                    </div>

                                                    @if($task->status==1)
                                                      <div class="row mt-4">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-8">
                                                            <form method="post" action="{{ route('ShowAndHideTask')}}">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ @$task->id }}" >
                                                                <input type="hidden" name="show_and_hide" value="{{ @$task->show_and_hide  }}" >
                                                                <button type="submit" class="btn Stage-submit w-100">{{ ($task->show_and_hide==1)?'Show':'Hide' }}</button>
                                                            </form>
                                                        </div>
                                                      </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    </td>
                                        
                                    </tr>
                                    @endforeach
                                    @endforeach
                                    @else
                                        <td colspan="6">There is no task for the day </td>
                                    @endif
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                  
                   @else
                    <div>
                        <h5 class="text-white" id="todaydate">Today's Tasks</h5>
                        <div class="table-responsive">
                            <table class="table  table-bordered  bg-white jobs-table mt-4" id="todaytask">
                                <thead class="jobs-thead">
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Job Name</th>
                                        <th>Room</th>
                                        <th>Assign To</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body" id="datewisetask">
                                  @if($todayTasks) 
                                  @foreach($todayTasks as $task)
                                 
                                  
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="task{{ @$task['task']->id }}" name="taskid" value="{{ @$task['task']->id }}" class="form-check-input big-checkbox task_checkbox">
                                                <label class="form-check-label checkbox-label th-ontime" for="task1"> 
                                                    {{ ucwords(@$task['task']->title) }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{ $task['jobname'] }}</td>
                                        <td>{{ $task['task']->room }}</td>
                                        <td class="type_name_task">{{ $task['task']->contact_name }} <br/>
                                            <span>{{ $task['task']->type_name }}</span>
                                          </td>
                                        <td>{{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse(@$task['task']->enddate)->format('M d, Y') }}</td>
                                        <td>
                                            @if($task['task']->priority=='Complete' )
                                            <span class="badge badge-fixed bg-Complete">Complete</span>
                                            @elseif($task['task']->priority=='High')
                                            <span class="badge badge-fixed bg-High">High</span>
                                            @elseif($task['task']->priority=='Medium')
                                            <span class="badge badge-fixed bg-medium">Medium</span>
                                            @else
                                            <span class="badge badge-fixed bg-Low">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary text-286FAC" data-toggle="modal"
                                            data-target="#viewModal{{ @$task['task']->id }}" title="View">
                                                View
                                            </button>
                                             <!-- Modal for Row 1 -->
                                     <div class="modal fade" id="viewModal{{ @$task['task']->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title title-model"
                                                        id="modalLabel1">{{ @$task['jobname'] }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <tr>
                                                            <th>Task Name</th>
                                                            <td class=" title-model-table text-end">
                                                                {{ ucwords(@$task['task']->title) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Priority</th>
                                                            <td class="text-end">@if($task['task']->priority == 'High')
                                                                <span class="badge badge-fixed bg-high">{{ @$task['task']->priority }}</span>
                                                            @elseif($task['task']->priority == 'Medium')
                                                                <span class="badge badge-fixed bg-medium">{{ @$task['task']->priority }}</span>
                                                            @elseif($task['task']->priority == 'Complete')
                                                                <span class="badge badge-fixed bg-complete">{{ @$task['task']->priority }}</span>
                                                             @else
                                                                <span class="badge badge-fixed bg-low">{{ @$task['task']->priority }}</span>
                                                             @endif
                                                        
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Room</th>
                                                            <td class="text-end title-model-table">
                                                                {{ ucwords(@$task['task']->room) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Assign To</th>
                                                            <td class="text-end title-model-table">
                                                                {{ @$task['task']->contact_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Start Date</th>
                                                            <td class="text-end title-model-table">
                                                                {{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                                        </tr>
                                                    </table>
                                                    <div>
                                                        <h6 class="title-model-table">Description</h6>
                                                        <p class="text-justify">{{ @$task['task']->description }}</p>
                                                    </div>
                                                    <hr>
                                                    <div>
                                                        <h6 class="title-model-table">Document</h6>
                                                        
                                                        @foreach($task['task']->taskassignmentimages as $doc)
                                                        <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$task['task']->title) }}">
                                                            <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$task['task']->title) }}" class="img-fluid"/>
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                    @if($task['task']->status==1)
                                                    <div class="row mt-4">
                                                        <div class="col-md-2"></div>
                                                        <div class="col-md-8">
                                                            <form method="post" action="{{ route('ShowAndHideTask')}}">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ @$task['task']->id }}" >
                                                                <input type="hidden" name="show_and_hide" value="{{ @$task['task']->show_and_hide  }}" >
                                                                <button type="submit" class="btn Stage-submit w-100">{{ ($task['task']->show_and_hide==1)? 'Show':'Hide' }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                     </div>
                                    </td>
                                        
                                    </tr>
                                   
                                    @endforeach
                                    @else
                                        <td colspan="6">There is no task for the day </td>
                                    @endif
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h5 class="th-pastdue" style="margin-top:40px;">Past Due Tasks</h5>
                        <div class="table-responsive">
                            <table class="table  table-bordered  bg-white pastdue mt-0" id="pastdue_task">
                                <thead class="pastdue-thead">
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Job Name</th>
                                        <th>Room</th>
                                        <th>Assign To</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    <!-- First Row -->
                                @if($pastDueTasks) 
                                  @foreach($pastDueTasks as $task)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" id="task{{ @$task['task']->id }}" name="taskid" value="{{ @$task['task']->id }}" class="form-check-input big-checkbox task_checkbox">
                                                <label class="form-check-label checkbox-label th-pastdue" for="task1"> 
                                                    {{ ucwords(@$task['task']->title) }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{ $task['jobname'] }}</td>
                                        <td>{{ $task['task']->room }}</td>
                                        
                                        <td class="type_name_task">{{ $task['task']->contact_name }} <br/>
                                            <span>{{ $task['task']->type_name }}</span>
                                          </td>
                                        <td>{{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse(@$task['task']->enddate)->format('M d, Y') }}</td>
                                        <td>
                                            @if($task['task']->priority=='Complete' )
                                            <span class="badge badge-fixed bg-Complete">Complete</span>
                                            @elseif($task['task']->priority=='High')
                                            <span class="badge badge-fixed bg-High">High</span>
                                            @elseif($task['task']->priority=='Medium')
                                            <span class="badge badge-fixed bg-medium">Medium</span>
                                            @else
                                            <span class="badge badge-fixed bg-Low">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary text-286FAC"  data-toggle="modal"
                                            data-target="#viewModal{{ @$task['task']->id }}" title="View">
                                                View
                                            </button>
                                            <div class="modal fade" id="viewModal{{ @$task['task']->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title title-model"
                                                                id="modalLabel1">{{ @$task['jobname'] }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Task Name</th>
                                                                    <td class=" title-model-table text-end">
                                                                        {{ ucwords(@$task['task']->title) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Priority</th>
                                                                    <td class="text-end">@if($task['task']->priority == 'High')
                                                                        <span class="badge badge-fixed bg-high">{{ @$task['task']->priority }}</span>
                                                                    @elseif($task['task']->priority == 'Medium')
                                                                        <span class="badge badge-fixed bg-medium">{{ @$task['task']->priority }}</span>
                                                                    @elseif($task['task']->priority == 'Complete')
                                                                        <span class="badge badge-fixed bg-complete">{{ @$task['task']->priority }}</span>
                                                                     @else
                                                                        <span class="badge badge-fixed bg-low">{{ @$task['task']->priority }}</span>
                                                                     @endif
                                                                
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Room</th>
                                                                    <td class="text-end title-model-table">
                                                                        {{ ucwords(@$task['task']->room) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Assign To</th>
                                                                    <td class="text-end title-model-table">
                                                                        {{ @$task['task']->contact_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Start Date</th>
                                                                    <td class="text-end title-model-table">
                                                                        {{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                                                </tr>
                                                            </table>
                                                            <div>
                                                                <h6 class="title-model-table">Description</h6>
                                                                <p class="text-justify">{{ @$task['task']->description }}</p>
                                                            </div>
                                                            <hr>
                                                            <div>
                                                                <h6 class="title-model-table">Document</h6>
                                                                
                                                                @foreach($task['task']->taskassignmentimages as $doc)
                                                                <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$task['task']->title) }}">
                                                                    <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$task['task']->title) }}" class="img-fluid"/>
                                                                </a>
                                                                @endforeach
                                                            </div>

                                                            @if($task['task']->status==1)
                                                                <div class="row mt-4">
                                                                    <div class="col-md-2"></div>
                                                                    <div class="col-md-8">
                                                                        <form method="post" action="{{ route('ShowAndHideTask')}}">
                                                                            @csrf
                                                                            <input type="hidden" name="id" value="{{ @$task['task']->id }}" >
                                                                            <input type="hidden" name="show_and_hide" value="{{ @$task['task']->show_and_hide  }}" >
                                                                            <button type="submit" class="btn Stage-submit w-100">{{ ($task['task']->show_and_hide==1)? 'Show':'Hide' }}</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                    </tr>
                                    <!-- Modal for Row 1 -->
                                   
                                    @endforeach
                                    @else
                                        <td colspan="6">There is no task for the day </td>
                                    @endif
                                
                                </tbody>
                            </table>
                        </div>
                        <h5 class="text-white" style="margin-top:20px;">Completed Tasks</h5>
                        <div class="table-responsive">
                            <table class="table  table-bordered  bg-white Completed mt-4" id="completed_task">
                                <thead class="Completed-thead">
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Job Name</th>
                                        <th>Room</th>
                                        <th>Assign To</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="jobs-table-body">
                                    <!-- First Row -->
                                   
                                    @if($completedTasks) 
                                    @php
                                     //print_r($completedTasks);
                                    @endphp
                                    @foreach($completedTasks as $task)
                                   
                                      <tr>
                                          <td>
                                              <div class="form-check">
                                                  <input type="checkbox" id="task{{ @$task['task']->id }}" name="taskid" class="form-check-input big-checkbox task_checkbox" value="{{ $task['task']->id }}" {{ ($task['task']->status==1)? 'checked' :' ' }}>

                                                  <label class="form-check-label checkbox-label th-Completed">
                                                    {{ ucwords(@$task['task']->title) }}
                                                      </label>
                                              </div>
                                          </td>
                                          <td>{{ $task['jobname'] }}</td>
                                          <td>{{ $task['task']->room }}</td>
                                          <td class="type_name_task">{{ $task['task']->contact_name }} <br/>
                                            <span>{{ $task['task']->type_name }}</span>
                                          </td>
                                          <td>{{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                          <td>{{ \Carbon\Carbon::parse(@$task['task']->enddate)->format('M d, Y') }}</td>
                                          <td>
                                              @if($task['task']->priority=='Complete' )
                                              <span class="badge badge-fixed bg-Complete">Complete</span>
                                              @elseif($task['task']->priority=='High')
                                              <span class="badge badge-fixed bg-High">High</span>
                                              @elseif($task['task']->priority=='Medium')
                                              <span class="badge badge-fixed bg-medium">Medium</span>
                                              @else
                                              <span class="badge badge-fixed bg-Low">Low</span>
                                              @endif
                                          </td>
                                          <td>
                                              <button class="btn btn-sm btn-outline-secondary text-286FAC" data-toggle="modal"
                                              data-target="#viewModal{{ @$task['task']->id }}" title="View">
                                                  View
                                              </button>
                                                 <!-- Modal for Row 1 -->
                                       <div class="modal fade" id="viewModal{{ @$task['task']->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title title-model"
                                                        id="modalLabel1">{{ @$task['jobname'] }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <tr>
                                                            <th>Task Name</th>
                                                            <td class=" title-model-table text-end">
                                                                {{ ucwords(@$task['task']->title) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Priority</th>
                                                            <td class="text-end">@if($task['task']->priority == 'High')
                                                                <span class="badge badge-fixed bg-high">{{ @$task['task']->priority }}</span>
                                                            @elseif($task['task']->priority == 'Medium')
                                                                <span class="badge badge-fixed bg-medium">{{ @$task['task']->priority }}</span>
                                                            @elseif($task['task']->priority == 'Complete')
                                                                <span class="badge badge-fixed bg-complete">{{ @$task['task']->priority }}</span>
                                                             @else
                                                                <span class="badge badge-fixed bg-low">{{ @$task['task']->priority }}</span>
                                                             @endif
                                                        
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Room</th>
                                                            <td class="text-end title-model-table">
                                                                {{ ucwords(@$task['task']->room) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Assign To</th>
                                                            <td class="text-end title-model-table">
                                                                {{ @$task['task']->contact_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Start Date</th>
                                                            <td class="text-end title-model-table">
                                                                {{ \Carbon\Carbon::parse(@$task['task']->startdate)->format('M d, Y') }}</td>
                                                        </tr>
                                                    </table>
                                                    <div>
                                                        <h6 class="title-model-table">Description</h6>
                                                        <p class="text-justify">{{ @$task['task']->description }}</p>
                                                    </div>
                                                    <hr>
                                                    <div>
                                                        <h6 class="title-model-table">Document</h6>
                                                        
                                                        @foreach($task['task']->taskassignmentimages as $doc)
                                                        <a href="{{ asset($doc->image) }}" data-fancybox="gallery" data-caption="{{ ucwords(@$task['task']->title) }}">
                                                            <img src="{{ asset($doc->image) }}" width="100" height="100" alt="{{ ucwords(@$task['task']->title) }}" class="img-fluid"/>
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                @if($task['task']->status==1)
                                                   <div class="row mt-4">
                                                    <div class="col-md-2"></div>
                                                    <div class="col-md-8">
                                                        <form method="post" action="{{ route('ShowAndHideTask')}}">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ @$task['task']->id }}" >
                                                            <input type="hidden" name="show_and_hide" value="{{ @$task['task']->show_and_hide  }}" >
                                                            <button type="submit" class="btn Stage-submit w-100">{{ ($task['task']->show_and_hide==1)? 'Show':'Hide' }}</button>
                                                        </form>
                                                    </div>
                                                  </div>
                                                @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                          </td>
                                         
                                      </tr>
                                    
                                      @endforeach
                                      @else
                                          <td colspan="6">There is no task for the day </td>
                                      @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @endif

                </div>
            </div>
        </div>

 @endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@section('script')
<script>
 $(document).ready(function() {
      $('#taskcalendar').fullCalendar({
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
            dayClick: function(date, jsEvent, view) {
                var clickedDate = date.format();
                $('.fc-day').removeClass('highlighted');  // Get the clicked date in 'YYYY-MM-DD' format
                $(this).addClass('highlighted');

                $.ajax({
                    url: '{{ route('gettaskBydate') }}',
                    method: 'POST',
                    data: {
                        clickdate: clickedDate,
                        _token: '{{ csrf_token() }}'  // If you're using Laravel
                    },
                    success: function(res) {
                         //$("#showdlt").html(res);
                         $('#datewisetask').html(''); // Clear the HTML content
                            var pq = 1;
                            var currentDate = new Date().toISOString().split('T')[0];
                        if (clickedDate === currentDate) {
                           $('#todaydate').html("Today's Tasks");
                        }else{
                            var clickedDate1 = date.format('YYYY-MM-DD'); // Ensure only date without time
                            var clickd = new Date(clickedDate1 + 'T00:00:00Z'); // Force UTC by adding 'Z' at the end

                            var clickdates = clickd.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short', // 'short' for 3-letter month abbreviation
                                day: 'numeric'
                            });
                            $('#todaydate').html(clickdates);
                        }

                         if (res.tasks.length > 0) {
                            for (var i = 0; i < res.tasks.length; i++) {
                                var task = res.tasks[i];  // Access the task object at index `i`
                                var jobname = task.name;
                                if (task.taskassignment && task.taskassignment.length > 0) {
                                    for (var j = 0; j < task.taskassignment.length; j++) {
                                        var taskAssignment = task.taskassignment[j];
                                        var taskname = taskAssignment.title;
                                        var assignto = taskAssignment.contact_name;
                                        var assignto_type = taskAssignment.type_name;
                                        var priority = taskAssignment.priority;
                                        var room = taskAssignment.room;
                                        var status = taskAssignment.status;
                                        if(status==1){
                                            var csk="checked";
                                        }else{
                                            var csk="";  
                                        }
                                        var startdate = taskAssignment.startdate;
                                        var dateObj = new Date(startdate);
                                        // Format the date to "MMM DD, YYYY"
                                        var startdatenew = dateObj.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short', // 'short' for 3-letter month abbreviation
                                            day: 'numeric'
                                        });
                                        var enddate= taskAssignment.enddate;
                                        var enddateObj = new Date(enddate);
                                        // Format the date to "MMM DD, YYYY"
                                        var enddatenew = enddateObj.toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short', // 'short' for 3-letter month abbreviation
                                            day: 'numeric'
                                        });

                                        var task_id = taskAssignment.id;
                                        var prt ='';
                                        if (priority == 'Complete') {
                                                prt = '<span class="badge badge-fixed bg-Complete">Complete</span>';
                                            } else if (priority == 'High') {
                                                prt = '<span class="badge badge-fixed bg-High">High</span>';
                                            } else if (priority == 'Medium') {
                                                prt = '<span class="badge badge-fixed bg-medium">Medium</span>';
                                            } else {
                                                prt = '<span class="badge badge-fixed bg-Low">Low</span>';
                                            }
                                        
                                        // Append task assignment data to the table
                                        $('#datewisetask').append(`
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <input type="checkbox" id="tsk${task_id}" name="taskid" value="${task_id}" class="form-check-input big-checkbox taskchecked" onclick="myfunction(${task_id})" ${csk}>
                                                                            <label class="form-check-label checkbox-label th-ontime" for="task${task_id}">${taskname}</label>
                                                                        </div>
                                                                    </td>
                                                                    <td>${jobname}</td>
                                                                    <td>${room}</td>
                                                                    <td class="type_name_task">
                                                                        ${assignto} <br/><span>${assignto_type}</span>
                                                                        </td>
                                                                    <td>${startdatenew}</td>
                                                                     <td>${enddatenew}</td>
                                                                    <td>${prt}</td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-outline-secondary text-286FAC" data-toggle="modal" data-target="#viewModal${task_id}" title="View">View</button>
                                                                    </td>
                                                                </tr>
                                                                  
                                                            `); 
                                        pq++;
                                    }
                                }
                            }
                        } else {
                            $('#datewisetask').append('<tr><td colspan="6">No Task For the Day .</td></tr>');
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }
        });

     

    });

    function myfunction(taskId){
        var checkbox = $(this);
       var isChecked = $('#tsk'+ taskId +'').is(':checked');
           
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

    }   

    $('.task_checkbox').change(function() {
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
                // alert(response.message);
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
  </script>
  <script>
  $(document).ready(function() {
    // Initialize DataTable
        $('#myhiddentask').DataTable({
            paging: false, // Enable pagination
            searching: false, // Enable search
            ordering: true, // Enable sorting
            info: false, // Show information
            lengthChange: false,
            columnDefs: [
            {
                targets: 5, // Index of the priority column
                orderDataType: 'dom-data-sort', // Custom sorting using data attribute
                type: 'string' // Treat content as a string for sorting
            },
            {
                targets: 6, // Index of the action column
                orderable: false // Disable sorting for the action column if needed
            }
        ] // Disable the "Show X entries" dropdown
        });

        $('#completed_task').DataTable({
            paging: false, // Enable pagination
            searching: false, // Enable search
            ordering: true, // Enable sorting
            info: false, // Show information
            lengthChange: false,
            columnDefs: [
            {
                targets: 5, // Index of the priority column
                orderDataType: 'dom-data-sort', // Custom sorting using data attribute
                type: 'string' // Treat content as a string for sorting
            },
            {
                targets: 6, // Index of the action column
                orderable: false // Disable sorting for the action column if needed
            }
        ] // Disable the "Show X entries" dropdown
        });
        $('#pastdue_task').DataTable({
            paging: false, // Enable pagination
            searching: false, // Enable search
            ordering: true, // Enable sorting
            info: false, // Show information
            lengthChange: false,
            columnDefs: [
            {
                targets: 5, // Index of the priority column
                orderDataType: 'dom-data-sort', // Custom sorting using data attribute
                type: 'string' // Treat content as a string for sorting
            },
            {
                targets: 6, // Index of the action column
                orderable: false // Disable sorting for the action column if needed
            }
        ] // Disable the "Show X entries" dropdown
        });
        $('#todaytask').DataTable({
            paging: false, // Enable pagination
            searching: false, // Enable search
            ordering: true, // Enable sorting
            info: false, // Show information
            lengthChange: false,
            columnDefs: [
            {
                targets: 5, // Index of the priority column
                orderDataType: 'dom-data-sort', // Custom sorting using data attribute
                type: 'string' // Treat content as a string for sorting
            },
            {
                targets: 6, // Index of the action column
                orderable: false // Disable sorting for the action column if needed
            }
        ] // Disable the "Show X entries" dropdown
        });
       
    });
    </script>
@stop