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
                    <h5 class="text-white">Leads</h5>
                    <div class="bg-white p-3">
							<div class="search-bar mt-4">
								<form method="get" action="{{ route('leads') }}">
									
									<div class="input-group mb-3">
										<button type="submit" class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></button>
										<input type="text" name="search" class="form-control" placeholder="Search leads" aria-label="search" aria-describedby="basic-addon1" value="{{ request('search') }}">
									</div>
                                    <a href="{{ route('leads') }}"><input type="button" class="btn btn-outline-primary mt-2 btn-custom px-4 filter-button {{ request('filter') === null ? 'btn-custom-active' : '' }}" value="All Leads"></a>
								</form>
							</div>

							
                    </div>

                    <div class="" style=" ">
                        <table class="table  table-bordered  bg-white jobs-table mt-4 job_list" id="job_lists">
                            <thead class="jobs-thead">
                                <tr>
                                    <th scope="col">Job Name</th>
                                    <th scope="col">Client Name</th>
                                    <th scope="col">Client Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="jobs-table-body">
								@if(count($leads)>0)
										@foreach($leads as $lead)
											
												<tr>
													<td><a href="{{ route('LeadDetails', $lead->id) }}" class="color_black">{{ ucwords($lead->deal_name) }}</a></td>
													<td><a href="{{ route('LeadDetails', $lead->id) }}">{{ $lead->name }}</a></td>
													<td><a href="{{ route('LeadDetails', $lead->id) }}">{{ $lead->lead_email ?? '' }}</a></td> <!-- Directly accessing user name -->
													<td><a href="{{ route('LeadDetails', $lead->id) }}">{{ $lead->mobile ?? '' }}</a></td> <!-- Directly accessing contact name -->
													
													<td>
														<a href="{{ route('LeadDetails', $lead->id) }}"><button class="btn btn-action border border-primary">View Details -></button></a>
													</td>
												</tr>
											
										@endforeach
									@else
										<tr>
											<td colspan="6">There is no lead available.</td>
										</tr>
									@endif

							 
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-md-4">
                    <h5 class="text-white">Add New Lead</h5>
                    <div>
                        <div class="bg-white p-3">
                            <form action="{{ route('AddLead') }}" method="POST" id="create_lead">
							@csrf
                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="deal_name" placeholder="Job name *" value="" required>
                                </div>

                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="text" name="name" value="" placeholder="Client Name *" required>
                                </div>

                                <div class="col-md-12 my-3">
                                    <input class="form-control form-control-sm" type="email" name="lead_email" value="" placeholder="Email">
                                </div>

                                <div class="col-md-12  my-3">
                                    <input id="phone" class="form-control form-control-sm" type="text" name="mobile" value="" placeholder="Phone Number">
                                 </div>

								<div id="client_data">
									<div class="col-md-12">
										<h5 class="Client-Address">Address</h5>
									</div>
									<div class="col-md-12 my-3">
										<input class="form-control form-control-sm" type="text" name="address" value="" placeholder="Street address">
									</div>
									<div class="col-md-12 my-3">
										<input class="form-control form-control-sm" type="text" name="city" value="" placeholder="Town/City ">
									</div>
									<div class="col-md-12  my-3">
										<div class="row">
											<div class="col-md-6">
												<input class="form-control form-control-sm" type="text" name="state" value="" placeholder="State">
											</div>
											<div class="col-md-6">
												<input class="form-control form-control-sm" type="text" name="pincode" value="" placeholder="Zipcode">
											</div>
										</div>
									</div>
								</div>
                                <div class="col-md-12 my-3">
                                    <textarea name="description" class="form-control" placeholder="Description"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">Submit</button>
                                </div>
								
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('script')

    <script>
         const input = document.querySelector("#phone");
            const iti = window.intlTelInput(input, {
            initialCountry: "us", // Set the initial country to India
            separateDialCode: true, // If you want the dial code visually separated
            strictMode: true
          });
         document.querySelector('#create_lead').addEventListener('submit', function (event) {
            event.preventDefault();
            const fullNumber = iti.getNumber();
            document.querySelector("#phone").value = fullNumber;
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