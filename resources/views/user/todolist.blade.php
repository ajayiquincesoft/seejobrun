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
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>
  <div class="row">
  
   <div class="col-md-12">
    
    <div class="todlist">
       
        <div>
            <h5 class="text-white">To Do List</h5>
           
        </div>
        <br/>
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
                                @if($allsection->general_todo_task->count() > 0)
                                @foreach($allsection->general_todo_task as $task)
                                    <li class="list-group-item d-flex justify-content-between" id="task-{{ $task->id }}" data-task-id="{{ $task->id }}">
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input task-status" type="checkbox" data-task-id="{{ $task->id }}" {{ ($task->status == 1) ? 'checked' : '' }}>
                                                <label class="form-check-label {{ ($task->status == 1) ? 'generaltaskcompleted' : '' }}">{{ $task->task_name }}</label>
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
                                                            <form method="POST" action="{{ route('updategenetodotask') }}">
                                                                @csrf
                                                                <div class="form-group mt-2">
                                                                    <label for="task-name-{{ $task->id }}">Task Name</label>
                                                                    <input type="hidden" name="todotask_id" class="form-control"  value="{{ $task->id }}">
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
  </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@section('script')
<script>
    // Add new section
    $('#addSectionBtn').on('click', function() {
            let sectionName = $('#newSection').val();
            if(sectionName) {
                $.ajax({
                    url: '{{ route('AddGenTodoSection') }}',
                    method: 'POST',
                    data: {  _token: '{{ csrf_token() }}', sec_name: sectionName },
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

        // Add a new task
        
$(document).on('click', '.addTaskBtn', function() {
    let button = this; // Store reference to this button
    let sectionId = $(button).data('section-id');
    let taskName = $(button).closest('.input-group').find('.newTaskInput').val();
    let taskEndDate = $(button).closest('.input-group').find('.newTaskEndDate').val();
    let taskDescription = $(button).closest('.input-group').find('.newTaskDescription').val();

    if (taskName) {
        $.ajax({
            url: '{{ route('AddGenToDotask') }}',
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

    

 // Edit section name
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
            url: '{{ route('UpdateTodogeneralsection') }}',
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
            url: '{{ route('DeleteTodogeneralsection') }}',
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

$(document).on('change', '.task-status', function() {
            let taskId = $(this).data('task-id');
            let isChecked = $(this).is(':checked');
            //if (confirm("Are you sure you want to complete this task?")) {
                $.ajax({
                    url: '{{ route('updategenetodotaskstatus') }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', todotask_id: taskId, status: isChecked ? 1 : 0 },
                    success: function() {
                        if(isChecked) {
                            $(this).closest('.list-group-item').addClass('task-completed');
                            $(this).closest('.list-group-item').find('label').addClass('generaltaskcompleted');
                            
                        } else {
                            $(this).closest('.list-group-item').removeClass('task-completed');
                            $(this).closest('.list-group-item').find('label').removeClass('generaltaskcompleted');
                        }
                    }.bind(this)
                });
           // }
});

$(document).on('click', '.delete-task-btn', function() {
    let taskId = $(this).data('task-id');
       // if (confirm("Are you sure you want to delete this task?")) {
            $.ajax({
                url: '{{ route('deletegenetodotask') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                    todotask_id: taskId
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
                    alert('An error occurred. Please try again.');
                    console.error(xhr.responseText);
                }
            });
        //}
});



</script>
@stop