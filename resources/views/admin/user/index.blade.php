@extends('layouts.app')

@section('content')
  <div class="header">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">User Information</h6>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid mt--6">
  
    <div class="row">
      <div class="col">
		
        <div class="card bg-default shadow">
          <div class="card-header bg-transparent border-0">
            @if(session('success'))
            <div class="alert alert-success">{{session('success')}}</div>
            @endif
          </div>
		<div class="row">
			<div class="col-md-6 col-sm-12"></div>
			<div class="col-md-6 col-sm-12">
			  <form action="{{ route('user.index') }}" method="GET" class="mb-4" style="width: 96%;">
				<div class="input-group">
					<input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request()->get('search') }}">
					<div class="input-group-append">
						<button class="btn btn-success" type="submit">Search</button>
				   <a href="{{ route('user.index') }}">
                    <input type="button" class="btn btn-success ml-2" value="Clear">
                  </a>
					</div>
				</div>
			</form>
			</div>
		</div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">id</th>
                  <th scope="col">Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Verify Code</th> 
				  <th scope="col">Status</th>
				  <th scope="col">Payment Status</th>
                 <th scope="col">Created at</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
               
              <tbody class="list">
                  @if(count($data)> 0)
                    @foreach($data as $key =>$datas)
                      <tr>
                        <td class="budget">{{ $datas->id }}</td>
                        <td>{{ $datas->name }}</td>
                        <td>{{ $datas->email }}</td>
						<td>{{ $datas->register_otp }}</td>
                        <td>{{ ($datas->status==1)?'Active':'Inactive' }}</td>
						<?php 
							
							$user_id = $datas->id;
							$user_subscription = \App\Models\SelectedPlan::where('user_id', $user_id)->latest()->first();
							if($user_subscription){
								if($user_subscription->plan_id==1 OR $user_subscription->plan_id==2){
									echo '<td><span style="color:#008000;font-weight: 700;">Paid</span></td>';
								}else{
									echo '<td><span style="color:#000;font-weight: 700;">Free</span></td>';
								}
							}else{
								echo '<td><span style="color:#000;font-weight: 700;">Free</span></td>';
							}		
								
							
						?>
                        <td>{{ $datas->created_at->format('F j, Y h:i A') }}</td>

                        <td>
                          <a href="{{ route('user.edit', $datas->id) }}" class="text-white">
                            <span class="mr-2"><i class="fa fa-edit" title="Edit User"></i></span>
                          </a>
                          <span> 
                            {!! Form::open([
                              'method'=>'DELETE',
                              'route' => ['user.destroy', $datas->id],
                              'style' => 'display:inline'
                              ]) !!}
                                {!! Form::button('<i class="fa fa-trash text-danger" aria-hidden="true"></i>', array(
                              'type' => 'submit',
                              'class' => 'btn',
                              'title' => 'Delete User',
                              'onclick'=>'return confirm("Are you sure about deleting User?")'
                              )) !!}
                              {!! Form::close() !!}
                          </span>

                          <a href="{{ route('user.show', $datas->id) }}" class="text-white">
                            <span class="mr-2"><i class="fa fa-id-card" title="User Subscription"></i></span>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  @else
                    <tr><td colspan="7" class="text-center">No Record Found</td></tr>
                  @endif
              </tbody>
            </table>
          </div>
			
          <div class="card-footer py-4">
               
               <!-- Pagination Links -->
				
					 {{ $data->appends(request()->except('page'))->links('vendor.pagination.admin') }}
				
            
          </div>
        </div>
      </div>
    </div>
  </div>
  
@endsection