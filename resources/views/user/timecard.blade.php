@extends('user.layout.userdashboard')
@section('content')
<div class="container-fluid content ">
            <div class="row supreme-container">
                <div class="col-md-12">
                    <h5 class="text-white">Time Card</h5>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="bg-white py-4 rounded px-4">
                        <form method="get" action="{{ route('gettimecard') }}">
                            <div class="input-group">
                                <button type="submit" class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></button>
                                <input type="text" name="search" class="form-control" placeholder="Search employee" value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="row">
                    @if($getemployees->count()>0)
                        @foreach($getemployees as $getemployee)
                            <div class="col-md-6  col-lg-3 my-2">
                                <div class="card ">
                                    <div class="card-header-custom">
                                        <img src="{{ asset($getemployee->profile_pic ? $getemployee->profile_pic : '././no-user.png') }}" class="rounded-circle" alt="{{ $getemployee->name }}" width="48" height="48">
                                        <p class="mt-2 m-0 fw-bold username">{{ $getemployee->name }}</p>
                                        <p class="Designation m-0">Employee</p>
                                        @php
                                        $latestRecord = DB::table('clocktimes')
                                                            ->where('user_id', 296)
                                                            ->orderBy('created_at', 'desc')
                                                            ->first();
                                        @endphp
                                        @if($getemployee->contact_user_id==1)
                                        <div class="clock-status clock-in">
                                            Clock In                 
                                        </div>
                                        @else
                                        <div class="clock-status clock-out">Clock Out</div>
                                        @endif 
                                    </div>
                                    <div class="text-center icon-arrage p-0">
                                        <a href="tel:{{ $getemployee->mobile }}" class="contact-icons">
                                            <i class="fas fa-phone success-icon"></i>
                                            <span class="font-12">Call Now</span>
                                        </a>
                                        <a href="sms:{{ $getemployee->mobile }}" class="contact-icons">
                                            <i class="fas fa-comment success-icon"></i>
                                            <span class="font-12">Message</span>
                                        </a>
                                        <a href="mailto:{{ $getemployee->email }}" class="contact-icons">
                                            <i class="fas fa-envelope danger-icon"></i>
                                            <span class="font-12">Email us</span>
                                        </a>
                                    </div>
                                    <div class="card-footer-custom">
                                        <a href="{{ route('getEmployeeTimeCard', $getemployee->id) }}" class="btn normal-button-for-time-card ">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                      @else
                      <div class="col-md-12  col-lg-12  my-2"> 
                        <div class="card pt-4 pb-4">
                        <div class="text-center icon-arrage p-0">There is no record found </div>
                        </div>
                      </div>
                      @endif
                   
                    </div>
                </div>
            </div>
        </div>

@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @section('script')
@stop