@extends('user.layout.userdashboard')
@section('content')

  <!-- Content -->
        <div class="container-fluid content ">
            <div class="row">
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
                <div class="col-md-8 ">
                    <h5 class="text-white">Jobs</h5>
                    <div class="bg-white p-3">
							<div class="search-bar mt-4">
								<form method="get" action="{{ route('user.jobs') }}">
									
									<div class="input-group mb-3">
										<button type="submit" class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></button>
										<input type="text" name="search" class="form-control" placeholder="Search Jobs" aria-label="search" aria-describedby="basic-addon1" value="{{ request('search') }}">
									</div>


									<!-- Filter Buttons -->
									<input type="hidden" name="filter" id="filterInput" value="{{ request('filter') }}">
									<a href="{{ route('user.jobs') }}"><input type="button" class="btn btn-outline-primary mt-2 btn-custom px-4 filter-button {{ request('filter') === null ? 'btn-custom-active' : '' }}" value="All Jobs"></a>
									<button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Residential' ? 'btn-custom-active' : '' }}" value="Residential" >Residential</button>
									<button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Commercial' ? 'btn-custom-active' : '' }}" value="Commercial">Commercial</button>
									{{-- <button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Lead' ? 'btn-custom-active' : '' }}" value="Lead">Leads</button> --}}
									<button type="button" class="btn btn-outline-primary btn-custom px-4 mt-2 filter-button {{ request('filter') === 'Archived' ? 'btn-custom-active' : '' }}" value="Archived">Archived</button>
								</form>
							</div>

							
                    </div>

                    <div class="" style=" ">
                        <table class="table  table-bordered  bg-white jobs-table mt-4 job_list" id="job_lists">
                            <thead class="jobs-thead">
                                <tr>
                                    <th scope="col">Job Name</th>
                                    <th scope="col">Job Type</th>
                                    <th scope="col">Assign by</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="jobs-table-body">
								@if(count($data)>0)
										
										@foreach($data as $job)
											@if($job->user_id == auth()->id())
												<tr>
													<td><a href="{{ route('getJob', $job->id) }}" class="color_black">{{ ucwords($job->name) }}</a></td>
													<td><a href="{{ route('getJob', $job->id) }}">{{ $job->job_type }}</a></td>
													<td><a href="{{ route('getJob', $job->id) }}">{{ $job->user->name ?? 'N/A' }}</a></td> <!-- Directly accessing user name -->
													<td><a href="{{ route('getJob', $job->id) }}">{{ $job->contact->name ?? 'N/A' }}</a></td> <!-- Directly accessing contact name -->
													@if($job->contract_status == 1)
													<td class="status-signed">
														<a href="{{ route('getJob', $job->id) }}"> {{ $job->contract_status == 1 ? 'Signed' : 'Unsigned' }} </a>
													</td>
													@else
													<td class="status-unsigned">
														<a href="{{ route('getJob', $job->id) }}"> {{ $job->contract_status == 1 ? 'Signed' : 'Unsigned' }}</a>
													</td>
													@endif
													<td>
														<a href="{{ route('getJob', $job->id) }}"><button class="btn btn-action border border-primary">View Details -></button></a>
													</td>
												</tr>
												@else
													<tr class="notownjob">
														<td class="color_black"><a href="{{ route('getJob', $job->id) }}" class="color_black">{{ ucwords($job->name) }}</a></td>
														<td><a href="{{ route('getJob', $job->id) }}">{{ $job->job_type }}</a></td>
														<td><a href="{{ route('getJob', $job->id) }}">{{ $job->user->name ?? 'N/A' }}</a></td> <!-- Directly accessing user name -->
														<td><a href="{{ route('getJob', $job->id) }}">{{ $job->contact->name ?? 'N/A' }}</a></td> <!-- Directly accessing contact name -->
														@if($job->contract_status == 1)
														<td class="status-signed">
															<a href="{{ route('getJob', $job->id) }}">{{ $job->contract_status == 1 ? 'Signed' : 'Unsigned' }}</a>
														</td>
														@else
														<td class="status-unsigned">
															<a href="{{ route('getJob', $job->id) }}">{{ $job->contract_status == 1 ? 'Signed' : 'Unsigned' }}</a>
														</td>
														@endif
														<td>
															<a href="{{ route('getJob', $job->id) }}"><button class="btn btn-action border border-primary">View Details -></button></a>
														</td>
													</tr>
												
											@endif
										@endforeach
									@else
										<tr>
											<td colspan="6">There is no job available.</td>
										</tr>
									@endif

							 
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-md-4">
                    <h5 class="text-white">Add New Job</h5>
                    <div>
                        <div class="bg-white p-3">
                            <form action="{{ route('user.addjob') }}" method="POST">
							@csrf
                                <div class="col-md-12 ">
                                    <select class="form-select form-select-sm text-muted" name="job_type" required>
                                        <option value="">Job type</option>
										<option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        {{-- <option value="Lead">Lead</option> --}}
                                       
                                    </select>
                                </div>
                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="name" placeholder="Job name *" value="" required>
                                </div>
								
								 <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="permit_no" value="" placeholder="Permit number">
                                </div>
								
                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="gate_no" value="" placeholder="Gate no">
                                </div>
                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="Lock_box_code" value="" placeholder="Lock box code" >
                                </div>
                                <div class="col-md-12  my-3">
                                    <select class="form-select form-select-sm text-muted" name="inspection" onchange="InspectionContact(this.value)" required>
										<option value="" disabled selected>Inspection Phone Number</option>
										<?php //$All_contacts ?>
										@if(!empty($All_contacts))
											@foreach($All_contacts as $cont)
												@if(($cont->type == 7) && ($cont->status==1))
													<option value="{{ $cont->id }}"> {{ $cont->name }} {{ $cont->mobile }} </option>
												@endif
											@endforeach
										@endif	
										<option value="inspection">+ Add New inspection</option>
                                    </select>
                                </div>
                                <div class="col-md-12   my-3">
                                    <select class="form-select form-select-sm text-muted" name="client_id" onchange="fetchData(this.value)" required>
                                        <option value="">Client’s Name *</option>
										@if(!empty($All_contacts))
											@foreach($All_contacts as $cont)
												@if($cont->type == 1)
													<option value="{{ $cont->id }}"> {{ $cont->name }} {{ $cont->mobile }} </option>
												@endif
											@endforeach
										@endif	
										<option value="add_new_client">+ Add New Client</option>
                                        
                                    </select>
                                </div>
								<div id="client_data">
									<div class="col-md-12  my-3">
									   <input  class="form-control form-control-sm" type="text" name="mobile" value="" placeholder="Client’s Phone Number *" id="client_number" readonly>
									</div>
									<div class="col-md-12 my-3">
										<input class="form-control form-control-sm" type="email" name="client_email" value="" placeholder="Client’s email *" id="client_email" readonly>
									</div>
									<div class="col-md-12">
										<h5 class="Client-Address">Client’s Address</h5>
									</div>
									<div class="col-md-12 my-3">
										<input class="form-control form-control-sm" type="text" name="client_address" value="" placeholder="Street address *" id="client_address" readonly>
									</div>
									<div class="col-md-12 my-3">
										<input class="form-control form-control-sm" type="text" name="client_city" value="" placeholder="Town/City *" id="client_city" readonly>
									</div>
									<div class="col-md-12  my-3">
										<div class="row">
											<div class="col-md-6">
												<input class="form-control form-control-sm" type="text" name="client_state" value="" placeholder="State" id="client_state" readonly>
											</div>
											<div class="col-md-6">
												<input class="form-control form-control-sm" type="text" name="client_pincode" value="" placeholder="Zipcode" id="client_pincode" readonly>
											</div>
										</div>
									</div>
								</div>
                                <div class="com-md-12  my-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="Client-Address">Job Address</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="checkbox" class="form-check-input" id="dropdownCheck2">
                                            <label class="form-check-label text-muted" for="dropdownCheck2">
                                                Same as Clients address
                                            </label>
                                        </div>
                                    </div>
                                </div>
								
							 <div class="col-md-12 my-3">
									<input class="form-control form-control-sm" type="text" name="address" value="" placeholder="Street address *" id="address" required>
								</div>
								<div class="col-md-12 my-3">
									<input class="form-control form-control-sm" type="text" name="city" value="" placeholder="Town/City *" id="city" required>
								</div>
								<div class="com-md-12  my-3">
									<div class="row">
										<div class="col-md-6">
											<input class="form-control form-control-sm" type="text" name="state" value="" placeholder="State" id="state" required>
										</div>
										<div class="col-md-6">
											<input class="form-control form-control-sm" type="text" name="pincode" value="" placeholder="Zipcode" id="pincode" required>
										</div>
									</div>
								</div>
								
                                <div class="col-md-12">
                                    <h5 class="Client-Address">Contract Status</h5>
                                </div>
								
                               
                                   
								<div class="inputGroup radiobtn">
										 <div class="row">
											<div class="col-md-6 d-flex justify-content-center my-2">
												<input id="optionSigned" value="1" name="contract_status" type="radio" />
												<label for="optionSigned">Signed</label>
											</div>
											<div class="col-md-6 d-flex justify-content-center my-2">
												<input id="optionUnsigned" value="0" name="contract_status" type="radio" checked/>
												<label for="optionUnsigned">Not Signed</label>
											</div>
										</div>	
								</div>
                                
								
                                <div class="col-md-12">
                                    <button type="submit"
                                        class="btn btn-primary text-center add-new-job-btn w-100 my-3">Add New
                                        Job</button>
                                </div>
								
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
<div class="row">
<div class="modal fade fullscreen" id="add_contact" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title model-head" id="detailsModalLabel">Create New Contact (<span> </span>)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="POST" id="create_contact" action="{{ route('addContact') }}" enctype="multipart/form-data">
					 @csrf
					<div class="row">
						<div class="col-md-12">
							<div id="add_contact_type">
							
							</div>
							<div class="form-group ">
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
							<div class="form-group mt-3">
								<input class="form-control form-control-sm " type="email" name="email" placeholder="Email*" required />
							</div>
						</div>
						<div class="col-md-12 mt-4">
							<h5 class="Client-Address">Client’s Address</h5>
						</div>
						<div class="col-md-12 mt-3">
							<input class="form-control form-control-sm" type="text" name="address"
								placeholder="Street address *"  >
						</div>
						<div class="col-md-12 mt-3">
							<input class="form-control form-control-sm" type="text" name="city"
								placeholder="Town/City *"  >
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
								<button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">Create</button>
						</div>
					</div>
				</form>
				<!-- Add more content here if needed -->
			</div>
			
		</div>
	</div>
</div>
</div> 
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('script')

<script>
	function InspectionContact(value) {
    // Get user's current credit balance
    var credit_contact = @json(Auth::user()->credit_contact);

    if (value) {
        if (value === 'inspection') {
            // Check if user has enough credits
            if (credit_contact > 0) {
                // Update modal label and input type
                $('#detailsModalLabel span').text('Inspection Profile');
                $('#add_contact_type').find('input[type="hidden"][name="type"]').remove();
                $('#add_contact_type').append('<input type="hidden" name="type" value="7"/>');
                
                // Show the modal to add a new inspection contact
                $('#add_contact').modal('show');
            } else {
                // Alert user about insufficient credits and show "Buy Credit" modal
                alert('You do not have enough credits. Please buy more contacts.');
                $('#Buycredit').modal('show');
            }
        }
        // If an existing contact is selected, no further action is required
    }
}


    function fetchData(value) {
        if (value) {
			if(value !='add_new_client'){
				$.ajax({
					url: "{{ route('getsinglecontact') }}", // Replace with your route
					type: "GET",
					data: {
						contact_id: value
					},
					success: function (response) {
						$('#client_number').val(response.mobile);
						$('#client_email').val(response.email);
						$('#client_address').val(response.address);
						$('#client_city').val(response.city);
						$('#client_state').val(response.state);
						$('#client_pincode').val(response.pincode);
					},
					error: function (xhr, status, error) {
						console.error('Error:', error);
					}
				});
			}else{
				var credit_contact = @json(Auth::user()->credit_contact);
       			if (credit_contact > 0) {
					$('#detailsModalLabel span').text('Client Profile');
					$('#add_contact_type').find('input[type="hidden"][name="type"]').remove();
					$('#add_contact_type').append('<input type="hidden" name="type" value="1"/>');
					
					$('#add_contact').modal('show');
				}else{
					alert('You do not have enough credits. Please buy more contacts.');
					$('#Buycredit').modal('show');
				}
			}
        } else {
            $('#result').html(''); // Clear the result if no value is selected
        }
    }

$(document).ready(function(){

	$('#dropdownCheck2').change(function() {
    // Check if the checkbox is checked
		if ($(this).is(':checked')) {
			// Copy the values from the client fields to the corresponding address fields
			const fields = ['address', 'city', 'state', 'pincode'];

			fields.forEach(function(field) {
				$('#' + field).val($('#client_' + field).val());
			});
		} else {
			// Clear the fields if the checkbox is unchecked
			$('#address, #city, #state, #pincode').val('');
		}
	});

	
});
</script>
<script>
document.querySelectorAll('.filter-button').forEach(button => {
    button.addEventListener('click', function() {
        // Set the value of the hidden input field
        document.getElementById('filterInput').value = this.value;
        // Submit the form
        this.closest('form').submit();
    });
});

</script>
<script type="text/javascript">
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
	<script>
		 $(document).ready(function() {
			$('#job_lists').DataTable({
				paging: false, // Enable pagination
				searching: false, // Enable search
				ordering: true, // Enable sorting
				info: false, // Show information
				lengthChange: false,
				order: [[4, 'asc']]
			// Disable the "Show X entries" dropdown
			});
	});
	</script>
@stop