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
                <div class="col-md-8 ">
                    <h5 class="text-white">Contacts</h5>
                    <div class="bg-white p-3">
                        <div class="search-bar mt-4">
                            <form method="get" action="{{ route('GetAllMyContactfilter') }}">
									
                                <div class="input-group mb-3">
                                    <button type="submit" class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></button>
                                    <input type="text" name="search" class="form-control" placeholder="Search Contacts" aria-label="search" aria-describedby="basic-addon1" value="{{ request('search') }}">
                                </div>
                                <!-- Filter Buttons -->
                                <div class="btn-container">
                                <input type="hidden" name="filter" id="filterInput" value="{{ request('filter') }}">
                                <a href="{{ route('GetAllMyContact') }}">
                                    <input type="button" class="btn btn-outline-primary mt-2 btn-custom px-4 filter-button {{ request('filter') === null ? 'btn-custom-active' : '' }}" value="All">
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Pending' ? 'btn-custom-active' : '' }}" value="Pending">Pending</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === '3' ? 'btn-custom-active' : '' }}" value="3">Employee</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === '4' ? 'btn-custom-active' : '' }}" value="4">Contractor</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === '2' ? 'btn-custom-active' : '' }}" value="2">Sub-Contractor</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === '7' ? 'btn-custom-active' : '' }}" value="7">Inspector</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === '1' ? 'btn-custom-active' : '' }}" value="1" >Client</button>
                                <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Archived' ? 'btn-custom-active' : '' }}" value="Archived">Archived</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive" >
                        <table class="table table-bordered  bg-white jobs-table mt-4">
                            <thead class="jobs-thead">
                                <tr>
                                    <th scope="col">Contact Name</th>
                                    <th scope="col">Relationship to me</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Phone No.</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="jobs-table-body">
                            @if($allContacts->isNotEmpty())
                                @foreach($allContacts as $allContact)
                                    <tr>
                                        <th  width="30%">
                                            <div class="row">
                                                <div class="col-md-3">
                                                 <img src="{{ asset($allContact->profile_pic ? $allContact->profile_pic : '././no-user.png') }}" class="rounded-circle" width="40" height="40" alt="">
                                                </div>
                                                <div class="col-md-9" >
                                                    <span class="color_black"> {{ $allContact->name }}<span>
                                                    <p style="color:red">
                                                    {{ ($allContact->status == 0) ? 'Pending' : '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </th>
                                        <td style="color: {{ $allContact->type_name == 'Employee' ? '#49b030' : ($allContact->type_name == 'Client' ? '#f4374b' : ($allContact->type_name == 'Sub-Contractor' ? '#286fac' : ($allContact->type_name == 'Inspector' ? '#286fac' : ''))) }}">{{ $allContact->type_name }}</td>
                                        <td width="25%">{{ $allContact->address }}<br/>{{ $allContact->city }}, {{ $allContact->state }} {{ $allContact->pincode }}</td>
                                        <td>{{ $allContact->mobile }}</td>
                                        <td width="16%">
                                            <button class="action-button action-delete delete_contact" data-contact_id="{{ $allContact->id }}"  title="Delete Contact">
                                                <i class="fa-solid fa-x"></i>
                                            </button>
                                            {{-- <button class="action-button action-edit">
                                                <i class="fa-solid fa-greater-than"></i>
                                            </button> --}}
                                            <button class="action-button action-edit " data-toggle="modal" data-target="#edit{{ $allContact->id }}" title="Edit Contact">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>


                                            <button class="action-button action-view" data-toggle="modal" data-target="#viewModal{{ $allContact->id }}"  title="View Contact">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    {{-- // EDit Modal  --}}

                                    <div class="modal fade" id="edit{{ $allContact->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                    
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title title-model" id="modalLabel1">Edit Contact </h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                             
                                                <div class="modal-body">
                                                    <div class="bg-white p-3 rounded" >
                                                        <div>
                                                            <div class="row">
                                                            <form method="POST" id="create_contact" action="{{ route('UpdateContact') }}" enctype="multipart/form-data">
                                                                @csrf
                                                               
                                                                    <div class="col-md-12">
                                                                    
                                                                        <input type="hidden" name="id"  value="{{ $allContact->id }}">
                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <img src="{{ asset( $allContact->profile_pic ? $allContact->profile_pic : '././no-user.png') }}" class="rounded-circle" width="80" height="80" />
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <input type="file" id="image" name="profile_pic" class="form-control"  style="margin-top: 17px;" accept="image/*">
                                                                            </div>
                                                                        </div> 
                                                                        <div class="form-group mt-3">
                                                                        <input  class="form-control form-control-sm " type="tel" name="mobile" placeholder="Phone Number *" value="{{ $allContact->mobile }}" />
                                                                        </div>
                                                                        <div class="form-group mt-3">
                                                                            <input class="form-control form-control-sm " type="text" name="name" placeholder="Name*"  value="{{ $allContact->name }}" />
                                                                        </div>
                                
                                                                        @if($allContact->type==2 || $allContact->type==4 || $allContact->type==5 || $allContact->type==6)
                                                                        <div class="col-md-12 mt-3">
                                                                            <input class="form-control form-control-sm" type="text" name="business_name" placeholder="Business name " value="{{ $allContact->business_name }}">
                                                                        </div>
                                                                        <div class="row">
                                                                                <div class="col-md-6 mt-3">
                                                                                    <input class="form-control form-control-sm" type="text" name="license_no" placeholder="License no" value="{{ $allContact->license_no }}">
                                                                                </div>
                                                                                <div class="col-md-6 mt-3">
                                                                                    <input class="form-control form-control-sm" type="text" name="trade" placeholder="Trade" value="{{ $allContact->trade }}">
                                                                                </div>
                                                                        </div>

                                                                        @endif
                                                                        @if($allContact->type==3)
                                                                        <div class="row">
                                                                            <div class="col-md-6 mt-3">
                                                                                <input class="form-control form-control-sm" type="text" name="social_security_no" placeholder="Social Security num" value="{{ $allContact->social_security_no }}">
                                                                            </div>
                                                                            <div class="col-md-6 mt-3">
                                                                             <input class="form-control form-control-sm" type="text" name="trade" placeholder="Trade" value="{{ $allContact->trade }}">
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                        <div class="form-group mt-3">
                                                                            <input class="form-control form-control-sm " type="email"  value="{{ $allContact->email }}" readonly/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 mt-4">
                                                                        <h5 class="Client-Address">Client’s Address</h5>
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <input class="form-control form-control-sm" type="text" name="address" placeholder="Street address" value="{{ $allContact->address }}" >
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <input class="form-control form-control-sm" type="text" name="city" placeholder="Town/City"  value="{{ $allContact->city }}">
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <input class="form-control form-control-sm" type="text" name="state" placeholder="State" value="{{ $allContact->city }}" >
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <input class="form-control form-control-sm" type="text" name="pincode" placeholder="Zipcode" value="{{ $allContact->pincode }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <textarea class="form-control form-control-sm" name="contact_notes" placeholder="Contract Notes"> {{ $allContact->contact_notes }}</textarea>
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                            <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3" value="submit">Update</button>
                                                                    </div>
                                                               
                                                            </form>
                                                            <!-- Add more content here if needed --> 
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- view contact modal --}}
                                    <div class="modal fade" id="viewModal{{ $allContact->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel1" aria-hidden="true">
                                        <style>hr:not([size]) {
                                            height: 1px;
                                            color: #827a7a;
                                        }</style>
                                        <div class="modal-dialog" role="document" style="width:30%">
                                            <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title title-model" id="modalLabel1">Contact Details</h5>
                                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                         
                                                    <div class="modal-body">
                                                        <div class="bg-white p-3 rounded" >
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ asset( $allContact->profile_pic ? $allContact->profile_pic : '././no-user.png') }}" alt="Profile Image" class="contact-img" width="48" height="48">
                                                                <div class="model-title-container">
                                                                    <h5 class="modal-title title-model" id="contactModalLabel"> {{ $allContact->name }}
                                                                    </h5>
                                                                    <p class="mb-0"  style="font-size: 14px;"> {{ $allContact->type_name }}</p>
                                                                </div>
                                                                <div class="action-buttons-model ms-auto">
                                                                    <button class="btn socal-button" title="Call">
                                                                    <a href="tel:{{ $allContact->mobile }}"> <i class="fas fa-phone"></i></a>
                                                                    </button>
                                                                
                                                                    <button class="btn socal-button" title="Mail">
                                                                        <a href="mailto:{{ $allContact->email }}"><i class="fas fa-envelope"></i></a>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <hr/>
                                                            @if($allContact->type==2 || $allContact->type==4 || $allContact->type==5 || $allContact->type==6)
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <p class="blck_color">Business Name</p>
                                                                        <p class="bl_color">{{ $allContact->business_name }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr/>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="blck_color">License No </p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="bl_color text-end">{{ $allContact->license_no }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr/>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="blck_color">Trade</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="bl_color text-end">{{ $allContact->trade }}</p>
                                                                    </div>
                                                                </div>
                                                                <hr/>
                                                            @endif
                                                            @if($allContact->type==3)
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="blck_color">Social Security No.</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="bl_color text-end">{{ $allContact->social_security_no }}</p>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="blck_color">Trade</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="bl_color text-end">{{ $allContact->trade }}</p>
                                                                </div>
                                                            </div>
                                                            <hr/>
                                                            @endif
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="blck_color">Address</p>
                                                                    <p class="bl_color">{{ $allContact->address }}  {{ $allContact->city }}, {{ $allContact->state }} {{ $allContact->pincode }}</p>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="blck_color">Contact Notes</p>
                                                                    <p class="bl_color">{{ $allContact->contact_notes }} </p>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"> 
                                                                    <form action="{{ route('ArchOrUnArchContact')}}" method="Post">
                                                                        @csrf
                                                                        <input type="hidden" name="contact_id" value="{{ $allContact->id }}">
                                                                        @if($allContact->status==1 || $allContact->status==0)
                                                                          <input type="hidden" name="status" value="2">
                                                                          <button type="submit" class="btn w-100 custom-color"> Archive </button>
                                                                         @endif
                                                                         @if($allContact->status==2)
                                                                         <input type="hidden" name="status" value="1">
                                                                         <button type="submit" class="btn w-100 custom-color"> Unarchive </button>
                                                                        @endif
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                 
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="5"> There is no contact found. </td>  
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                
                        {{ $allContacts->links('vendor.pagination.admin') }}
                 
                </div>
                <div class="col-md-4">
                    <h5 class="text-white">Add New Contact</h5>
                    <div class="bg-white" style="border-radius: 9px;">
                        <div class="reletion-text text-center">
                            <h6 class="text-034078 m-0">Relationship to me</h6>
                        </div>
                        <div class="relationship-container text-center">
                            <div class="relationship-button client-side" id="client-side">Client</div>
                            <div class="relationship-button general-contractor-side" id="general-contractor-side">General-Contractor</div>
                            <div class="relationship-button sub-contractor-side" id="sub-contractor-side">Sub-Contractor</div>
                            <div class="relationship-button architect-engineer-side" id="architect-engineer-side">Architect/Engineer</div>
                            <div class="relationship-button interior-designer-side" id="interior-designer-side">Interior Designer</div>
                            <div class="relationship-button employee-side" id="employee-side">Employee</div>
                            <div class="relationship-button inspector-side" id="inspector-side">Inspector</div>
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
                            <form method="POST" id="create_contact1" action="{{ route('addContact') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                    
                                        <div class="form-group ">
                                            <div id="user_types"></div>
                                            <div class="add-pic">
                                                <input type="file" name="profile_pic" id="add-pic">
                                                <label for="add-pic"><img class="pro-pic" src="{{ asset('pro-pic.png') }}"></label>
                                                <span>Upload a Picture</span>
                                            </div>
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
                                            placeholder="Street address"  >
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <input class="form-control form-control-sm" type="text" name="city"
                                            placeholder="Town/City"  >
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
                                        <textarea class="form-control form-control-sm" placeholder="Contract Notes" name="contact_notes"></textarea>
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
@endsection


@section('script')
<script>

$(document).on('click', '.delete_contact', function(e) {
    e.preventDefault();
    
    // Get the contact ID and job ID from the button's data attributes
    var contact_id = $(this).data('contact_id');
    var button= $(this);
    // Confirm the action before proceeding
    if (confirm('Are you sure you want to remove this contact ?')) {
        $.ajax({
            url: '{{ route("DeleteContact") }}', // Replace with your route
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',  // CSRF token for security
                id: contact_id,
            },
            success: function(response) {
                // Handle success
                if(response.success) {
                    alert('Contact removed successfully.');
                    button.closest('tr').fadeOut(500, function() {
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

$(document).ready(function () {
    var user_2456 ='<div class="col-md-12 mt-3"><input class="form-control form-control-sm" type="text" name="business_name" placeholder="Business name " ></div><div class="row"><div class="col-md-6 mt-3"><input class="form-control form-control-sm" type="text" name="license_no" placeholder="License no" ></div><div class="col-md-6 mt-3"><input class="form-control form-control-sm" type="text" name="trade" placeholder="Trade" ></div></div>';
    var user_3 ='<div class="row"><div class="col-md-6 mt-3"><input class="form-control form-control-sm" type="text" name="social_security_no" placeholder="Social Security num" ></div><div class="col-md-6 mt-3"> <input class="form-control form-control-sm" type="text" name="trade" placeholder="Trade" ></div></div>';
    $('#client-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:red">Client</span><input type="hidden" name="type" value="1"/>');
                $('.additionalfield').html('');
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#general-contractor-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#008000">Contractor</span><input type="hidden" name="type" value="4"/>');
                $('.additionalfield').html('');
                $('.additionalfield').html(user_2456);
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#sub-contractor-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#286fac;">Sub-Contractor</span><input type="hidden" name="type" value="2"/>');
                $('.additionalfield').html('');
                $('.additionalfield').html(user_2456);
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#architect-engineer-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#034078">Architect/Engineer</span><input type="hidden" name="type" value="5"/>');
                $('.additionalfield').html('');
                $('.additionalfield').html(user_2456);
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#interior-designer-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#0042cd">Interior Designer</span><input type="hidden" name="type" value="6"/>');
                $('.additionalfield').html('');
                $('.additionalfield').html(user_2456);
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#employee-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#49b030">Employee</span><input type="hidden" name="type" value="3"/>');
                $('.additionalfield').html('');
                $('.additionalfield').html(user_3);
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
    });
    $('#inspector-side').on('click', function () {
        var credit_contact = @json(Auth::user()->credit_contact);
        if (credit_contact > 0) {
                $('#user_types').html('');
                $('#user_types').html('<span style="color:#f2911b">Inspector</span><input type="hidden" name="type" value="7"/>');
                $('.additionalfield').html('');
                setTimeout(function() {
                    $('#add_contact').modal('show');
                }, 300);
        } else {
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
        }
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

document.querySelectorAll('.filter-button').forEach(button => {
    button.addEventListener('click', function() {
        // Set the value of the hidden input field
        document.getElementById('filterInput').value = this.value;
        // Submit the form
        this.closest('form').submit();
    });
}); 


</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
const input = document.querySelector("#phone");
    const iti = window.intlTelInput(input, {
        initialCountry: "us", // Set the initial country to India
	  separateDialCode: true, // If you want the dial code visually separated
	  strictMode: true
    });
    document.querySelector('#create_contact1').addEventListener('submit', function (event) {
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
@stop