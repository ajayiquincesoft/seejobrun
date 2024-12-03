@extends('layouts.app')

@section('content')

<div class="header pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Job Details</h6>
        </div>

        <div class="col-lg-6 col-7">
          <a class="btn btn-primary" href="{{ route('job.index') }}" style="float:right;"> Back</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid mt--6">
  <div class="row">
    <div class="col-xl-12 order-xl-1">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <ul class="nav nav-tabs">
                <li>
                  <a class="nav-link active" data-toggle="tab" href="#job"><b>Job Information</b></a>
                </li>

                <li>
                  <a class="nav-link" data-toggle="tab" href="#stage"><b>Stage</b></a>
                </li>

                <li>
                  <a class="nav-link" data-toggle="tab" href="#document"><b>Document</b></a>
                </li>

                <li>
                  <a class="nav-link" data-toggle="tab" href="#picture"><b>Picture</b></a>
                </li>

                <li>
                  <a class="nav-link" data-toggle="tab" href="#contact"><b>Contact</b></a>
                </li>

                <li>
                  <a class="nav-link" data-toggle="tab" href="#punch"><b>Punch List</b></a>
                </li>
              </ul><br>
                    
              <div class="tab-content">
                <div class="tab-pane active" id="job">
                  <form class="form" action="##" method="post" id="registrationForm">
                    <div class="row">
                      <div class="col-md-2">
                        Name
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="name" class="form-control" value="{{ $data->name }}" readonly=""> 
                      </div>

                      <div class="col-md-2">
                        Mobile No
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="mobile" class="form-control" value="{{ $data->mobile }}" readonly=""> 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Permit No
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="permit_no" class="form-control" value="{{ $data->permit_no }}" readonly=""> 
                      </div>

                      <div class="col-md-2">
                        Gate No
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="gate_no" class="form-control" value="{{ $data->gate_no }}" readonly=""> 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Client Name
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_name" class="form-control" value="{{ $data->contact->name }}" readonly=""> 
                      </div>

                      <div class="col-md-2">
                        Client Mobile
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_mobile" class="form-control" value="{{ $data->contact->mobile }}" readonly="">  
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Client Email
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_email" class="form-control" value="{{ $data->contact->email }}" readonly="">  
                      </div>

                      <div class="col-md-2">
                        Client Address
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_address" class="form-control" value="{{ $data->contact->address }}" readonly="">   
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Client City
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_city" class="form-control" value="{{ $data->contact->city }}" readonly=""> 
                      </div>

                      <div class="col-md-2">
                        Client State
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_state" class="form-control" value="{{ $data->contact->state }}" readonly=""> 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Client Pincode
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_pincode" class="form-control" value="{{ $data->contact->pincode }}" readonly=""> 
                      </div>

                      <div class="col-md-2">
                        Job Type
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="client_jobtype" class="form-control" value="{{ $data->job_type }}" readonly=""> 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Address
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="address" class="form-control" value="{{ $data->address }} " readonly=""> 
                      </div>

                      <div class="col-md-2">
                        City
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="city" class="form-control" value="{{ $data->city }}" readonly=""> 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        State
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="state" class="form-control" value="{{ $data->state }} " readonly="">  
                      </div>

                      <div class="col-md-2">
                        Pincode
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="pincode" class="form-control" value="{{ $data->pincode }}" readonly="">  
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-2">
                        Contract Status
                      </div>

                      <div class="col-md-4">
                        <input type="type" name="contract_status" class="form-control" value="{{ ($data->contract_status==1)?'Yes':'No' }}" readonly="">  
                      </div>
                    </div>
                  </form>
                </div>
              
                <div class="tab-pane" id="stage">
                  <form class="form" action="##" method="post" id="registrationForm">
                    @php
                      foreach($data->jobstage as $jobstagekey)
                      { 
                    @endphp
                        <div class="row" style="margin-top:10px">
                          <div class="col-md-2">
                            Stage Name
                          </div>

                          <div class="col-md-4">
                            <input type="type" name="name" class="form-control" value="{{ $jobstagekey->stage->name }}" readonly=""> 
                          </div>

                          <div class="col-md-2">
                            Stage Progress
                          </div>

                          <div class="col-md-4">
                            <input type="type" name="name" class="form-control" value="{{ $jobstagekey->stage->progress_status }}" readonly=""> 
                          </div>
                        </div>
                    @php
                      }
                    @endphp
                  </form>  
                </div>
                
                <div class="tab-pane" id="document">
                  <form class="form" action="##" method="post" id="registrationForm">
                    <div class="row" style="margin-top:10px;">
                      @php
                        foreach($data->jobmedia as $jobmediakey)
                        {
                          $filename = $jobmediakey->media->image;
                          $exp = explode(".",$filename);

                          if($exp[1] != "JPEG" && $exp[1] != "jpeg" && $exp[1] != "PNG" && $exp[1] != "png")
                          {
                      @endphp
                            <div class="col-md-2">
                              <a href="{{asset('/')}}{{ $jobmediakey->media->image }}" title="ImageName" download>
                                <img class="logo" src="{{asset('/document-icon1.jpg')}}" width="100" style="padding-bottom:6px;">{{ str_replace('media/','',$jobmediakey->media->image); }}
                              </a>
                            </div>
                      @php
                          }
                        }
                      @endphp
                    </div>
                  </form>
                </div>

                <div class="tab-pane" id="picture">
                  <form class="form" action="##" method="post" id="registrationForm">
                    <div class="row" style="margin-top:10px;">
                      @php
                        foreach($data->jobmedia as $jobmediakey)
                        {
                          $filename = $jobmediakey->media->image;
                          $exp = explode(".",$filename);

                          if($exp[1] == "JPEG" || $exp[1] == "jpeg" || $exp[1] == "PNG" || $exp[1] == "png")
                          {
                      @endphp
                            <div class="col-md-2">
                              <img class="logo" src="{{asset('/')}}{{ $jobmediakey->media->image }}" width="100" style="padding-bottom:6px;">
                            </div>
                      @php
                          }
                        }
                      @endphp
                    </div>
                  </form>
                </div>

                <div class="tab-pane" id="contact">
                  <form class="form" action="##" method="post" id="registrationForm">
                    @php
                      foreach($data->jobcontact as $jobcontactkey)
                      {
                    @endphp
                        <div class="row" style="margin-top:10px;">
                          <div class="col-md-2">
                            Contact Name
                          </div>

                          <div class="col-md-4">
                            <input type="type" name="name" class="form-control" value="{{ $jobcontactkey->contact->name }}" readonly=""> 
                          </div>

                          <div class="col-md-2">
                            Contact Mobile
                          </div>

                          <div class="col-md-4">
                            <input type="type" name="name" class="form-control" value="{{ $jobcontactkey->contact->mobile }}" readonly=""> 
                          </div>
                        </div>
                    @php
                      }
                    @endphp
                  </form>
                </div>  

                <div class="tab-pane" id="punch">
                  <form class="form" action="##" method="post" id="registrationForm">
                    <table class="table align-items-center table-dark table-flush">
                      <thead class="thead-dark">
                        <tr>
                          <th>Title</th>
                          <th>Date</th>
                          <th>Priority</th>
                          <th>Assign To</th>
                          <th>Description</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      
                      <tbody class="list">
                        @php
                          foreach($data->punchlist as $punchlistkey)
                          {
                        @endphp
                            <tr>
                              <td>{{ $punchlistkey->title }}</td>
                              <td>{{ date('D d-m-Y',strtotime($punchlistkey->when)) }}</td>
                              <td>{{ $punchlistkey->priority }}</td>
                              <td>{{ $punchlistkey->punchcontact->name }}</td>
                              <td>{{ $punchlistkey->description }}</td>
                              <td>
                                <a href="" class="text-white" id="editCompany" data-toggle="modal" data-target="#modal-id{{$punchlistkey->id}}">
                                  <span class="mr-2"><i class="fa fa-eye" title="View User"></i></span>
                                </a>

                                <div class="modal fade" id="modal-id{{$punchlistkey->id}}">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h4 class="modal-title">Punch List Images</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>

                                      <div class="modal-body" id="showimg">
                                        <div class="row">
                                          @php
                                            foreach($punchlistkey->punchlistimg as $punchlistimgkey)
                                            {
                                          @endphp
                                              <div class="col-md-4">
                                                <img class="logo" src="{{asset('/')}}{{ $punchlistimgkey->image }}" style="padding-bottom:6px; width:100%">
                                              </div>
                                          @php
                                            }
                                          @endphp
                                        </div>
                                      </div>

                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </td>
                              <!-- <td>
                                @php
                                  foreach($punchlistkey->punchlistimg as $punchlistimgkey)
                                  {
                                @endphp
                                    <img class="logo" src="{{asset('/')}}{{ $punchlistimgkey->image }}" width="100" style="padding-bottom:6px;">
                                @php
                                  }
                                @endphp
                              </td> -->
                            </tr>
                        @php
                          }
                        @endphp
                      </tbody>
                    </table>
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

@endsection

@section('script')
  
  <script>
    $('body').on('click', '#editCompany', function (event) 
    {
      alert('yes');
      event.preventDefault();
      var id = $(this).data('id');
       
      $.get(store+'/'+ id+'/edit', function (data) 
      {
        $('#modal-id').modal('show');
        $('#showimg').html(data.data.image);
      })
    });
  </script>

@stop