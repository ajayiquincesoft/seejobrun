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
        </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="profile-background">
                        <div class="profile-image-header">
                            <?php if($user->profile_pic){?>
                                <img src="{{ asset('') }}{{ $user->profile_pic }}" alt="{{ $user->name }}" class="rounded profile-img">
                             <?php }else{?>
                                <img src="{{ asset('assets') }}/images/UserImage.png" alt="{{ $user->name }}" class="rounded profile-img">
                             <?php } ?>
                            <button class="btn font-14 btn-align" data-toggle="modal" data-target="#EditProfile">
                                Edit Profile</button>
                        </div>
                    </div>
                    <div class="profile-designation-page">
                        <h5 class="text-white mb-0">{{ $user->name }}</h5>
                        <small class="text-white m-0">{{ $user->timezone}}</small>
                    </div>
                </div>
                <div class="modal fade" id="EditProfile">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title title-model" id="modalLabel1">Profile Update</h5>
                                <button type="button" class="btn-close" data-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form method="post" action="{{ route('PostUpdateProfile') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mt-2">
                                            <div class="file-input" id="fileInputContainer">
                                                <img src="" alt="File Preview" class="file-preview" id="filePreview"
                                                    style="display: none;">
                                                <div id="uploadText">
                                                    <p class="m-0" style="color: #286FAC; font-size: 25px;"><i
                                                            class="fa-solid fa-cloud-arrow-up"></i></p>
                                                    <p class="m-0">Upload profile picture</p>
                                                    <button class="btn  bg-286FAC mt-2">Browse File</button>
                                                </div>
                                                <input type="file" name="profile_pic" id="fileUpload"
                                                    accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <input type="text" class="form-control form-control-sm" name="name"
                                                value="{{ $user->name }}">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <input type="tel" class="form-control form-control-sm" name="mobile" id=""
                                                value="{{ $user->getMeta('Mobile') }}">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                           
                                            <select name="timezone" value="" class="form-control custom-select" required>
                                                <option value="">Select time zone*</option>
                                                <option value="America/Denver" {{ $user->timezone == 'America/Denver' ? 'selected' : '' }}>Mountain time</option>
                                                <option value="America/Los_Angeles" {{ $user->timezone == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific time</option>
                                                <option value="America/Chicago" {{ $user->timezone == 'America/Chicago' ? 'selected' : '' }}>Central time</option>
                                                <option value="America/New_York" {{ $user->timezone == 'America/New_York' ? 'selected' : '' }}>Eastern time</option>
                                            </select>
                                        </div>
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

                <div class="col-sm-12 col-md-12 col-lg-4">
                    <div class="bg-white ">
                        <div class="d-flex justify-content-between bg-gray p-2 bg-light-gradient">
                            <div class="text-034078 fw-600">
                                Profile Details
                            </div>
                            <div class="edit-icon" data-toggle="modal" data-target="#EditProfile">
                                <i class="fas fa-edit status-signed cursor"></i>
                            </div>
                        </div>
                        <div  class="p-3">
                            <div class="mb-2 p-2 border-bottom">
                                <a href="tel:{{ $user->getMeta('Mobile') }}">
                                <i class="fas fa-phone profile-icon"></i> <span class="Designation mx-2"> {{ $user->getMeta('Mobile') }}</span>
                                </a>
                            </div>
                            <div class="mb-2 p-2  border-bottom">
                                <a href="mailto:{{ $user->email }}">
                                <i class="fas fa-envelope profile-icon"></i><span class="Designation mx-2">
                                    {{ $user->email }} </span>
                                </a>
                            </div>
                            <div class="mb-2 p-2  border-bottom">
                                <i class="fas fa-language profile-icon"></i><span class="Designation mx-2"> Language
                                    (English)</span>
                            </div>
                            <div class="mb-2 p-2 border-bottom">
                                <a href="{{ route('PrivacyPolicy') }}">
                                <i class="fas fa-info-circle profile-icon"></i><span class="Designation mx-2"> Privacy
                                    Policy</span>
                                </a>
                            </div>
                            <div class="mb-2 p-2  border-bottom">
                                <a href="{{ route('account_delete') }}">
                                <i class="fas fa-user-times profile-icon"></i><span class="Designation mx-2"> Account
                                    Delete</span>
                                </a>
                            </div>
                            <div class="mb-2 p-2  border-bottom">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($selectedplans->plan_id==1)
                                        <h5>$20/ Monthly</h5>
                                        @elseif($selectedplans->plan_id==2)
                                        <h5>$220/ Annually</h5> 
                                        @else
                                        <h5>FREE TRIAL</h5> 
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <form method="post" action="{{ route('CancelStripePlan')}}">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $selectedplans->plan_id }}">
                                            @php
                                            //print_r($currentgateway);
                                            @endphp
                                            @if($currentgateway['payment_gateway']=='stripe')
                                            
                                            <button type="submit" class="btn btn-danger" style="position:initial">Cancel</button>

                                            @endif
                                        </form>    
                                    </div>
                                    <div class="col-md-12">
                                        @if($currentgateway['payment_gateway'] !='stripe') 
                                            <span style="color:red">You cannot cancel the plan on the website. Please use the app to cancel it.</span>

                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-3">
                           <a href="{{ route('Plans') }}"> <button type="submit" class="btn Stage-submit w-100">Subscription</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>



@endsection
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @section('script')
@stop