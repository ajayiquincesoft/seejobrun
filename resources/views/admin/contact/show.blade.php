@extends('layouts.app')

@section('content')

<div class="header pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Contact Details</h6>
        </div>

        <div class="col-lg-6 col-7">
          <a class="btn btn-primary" href="{{ route('contact.index') }}" style="float:right;"> Back</a>
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
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-md-2" style="padding-top:35px;">
                <center>
                  <img src="{{asset('/')}}{{$data->profile_pic}}" style="border-radius:50% !important; width:100px; height:100px;">
                </center>
                <p style="text-align:center; margin-top:10px;">{{ $data->name }}</p>
              </div>         

              <div class="col-md-10" style="padding-top:20px; padding-left:100px;">
                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>Mobile No</b>
                  </div>

                  <div class="col-md-9">
                    {{ $data->mobile }} 
                  </div>
                </div>

                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>Email Id</b>
                  </div>

                  <div class="col-md-9">
                    {{ $data->email }} 
                  </div>
                </div>

                @php
                  if($data->type == "2")
                  {
                @endphp
                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>Business Name</b>
                      </div>

                      <div class="col-md-9">
                        {{ $data->business_name }} 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>License No</b>
                      </div>

                      <div class="col-md-9">
                        {{ $data->license_no }} 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>Trade</b>
                      </div>

                      <div class="col-md-9">
                        {{ $data->trade }} 
                      </div>
                    </div>
                @php    
                  }
                  else if ($data->type == "3")
                  {
                @endphp
                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>Social Security No</b>
                      </div>

                      <div class="col-md-9">
                        {{ $data->social_security_no }} 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>Trade</b>
                      </div>

                      <div class="col-md-9">
                        {{ $data->trade }} 
                      </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                      <div class="col-md-3">
                        <b>Gps Tracker</b>
                      </div>

                      <div class="col-md-9">
                        {{ ($data->gps_tracker==1)?'Yes':'No' }}
                      </div>
                    </div>
                @php    
                  }
                @endphp

                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>User Type</b>
                  </div>

                  <div class="col-md-9">
                    {{ ($data->type==1)?'Client':(($data->type==2)?'Sub Contractor':(($data->type==3)?'Employee':(($data->type==4)?'General Contractor':(($data->type==5)?'Architect/Engineer':(($data->type==6)?'Interior Designer':(($data->type==7)?'Inspector':'Bookkeeper')))))) }}
                  </div>
                </div>

                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>Address</b>
                  </div>

                  <div class="col-md-9">
                    {{ $data->address }}, {{ $data->city }}, {{ $data->state }}, {{ $data->pincode }}
                  </div>
                </div>

                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>Shared Contact</b>
                  </div>

                  <div class="col-md-9">
                    {{ $data->shared_contact }}
                  </div>
                </div>

                <div class="row" style="margin-top:10px;">
                  <div class="col-md-3">
                    <b>Contact Notes</b>
                  </div>

                  <div class="col-md-9">
                    {{ $data->contact_notes }}
                  </div>
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