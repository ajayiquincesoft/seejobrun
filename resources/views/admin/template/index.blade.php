@extends('layouts.app')

@section('content')
  <div class="header pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">Template Information</h6>
          </div>

          <div class="col-lg-6 col-7">
            <a class="btn btn-primary" href="{{ route('template.create') }}" style="float:right;"> Add Template</a>
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
          <div class="table-responsive">
            <table class="table align-items-center  table-flush">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">id</th>
                  <th scope="col">Type</th>
                  <th scope="col">Created at</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
               
              <tbody class="list">
                  @if(count($data)> 0)
                    @foreach($data as $key =>$datas)
                      <tr>
                        <td class="budget">{{ ++$key }}</td>
                        <td>
						<?php 
						
							if($datas->type==1) echo 'Contact Success';
							if($datas->type==2) echo 'Email Verify';
							if($datas->type==3) echo 'Confirmation Code';
							if($datas->type==4) echo 'Forget Password';
							if($datas->type==5) echo 'Add Job Email';
							if($datas->type==6) echo 'Job injured';
							if($datas->type==7) echo 'Appointment Notification';
							if($datas->type==8) echo 'Add Contact to Job';
							if($datas->type==9) echo 'Add Contact Invitation';
							if($datas->type==10) echo 'Add Contact Invitation for not registered user';
							if($datas->type==11) echo 'Signup Email Verify';
							if($datas->type==12) echo 'Add Contact Invitation for registered user new version';
							if($datas->type==13) echo 'Add Contact Invitation for not registered user new version';
							
						?>
						<!--{{ ($datas->type==1)? "Contact Success" :($datas->type==2)? "Email Verify" : ($datas->type==3)? "Confirmation Code":($datas->type==4)? "Forget Password":($datas->type==5)? "Add Job Email":($datas->type == 6)? "Job injured":'' }}-->
						</td>
                        <td>{{ $datas->created_at->format('d M Y h:i A') }}</td>

                        <td>
                          <a href="{{ route('template.edit', $datas->id) }}" class="text-white">
                            <span class="mr-2"><i class="fa fa-edit" title="View User"></i></span>
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
              
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection