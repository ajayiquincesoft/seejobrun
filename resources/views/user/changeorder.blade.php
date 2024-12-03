@extends('user.layout.userdashboard')
@section('content')
    <div class="container-fluid content ">
            <div class="row supreme-container">
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
                <div class="col-md-12 col-sm-12 col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-primary text-center  add-new-job-btn" id="addchangeOrderbtn">
                        Add Change Order</button>
                </div>
                @php
                    //print_r($jobdata);
                    //die;
                @endphp
                    
               
                <div class="col-md-4 col-lg-4">
                    <h5 class="text-white">Change Order</h5>
                    <div class="bg-white rounded-top ">
                        <div class="col-md-12 py-4 px-1">
                            <select class="form-select text-muted" id="jobSelect" onchange="fetchJobchangeOrderDetails()">
                                <option selected disabled value="">Select Job</option>
                                @if($jobdata)
                                    @foreach($jobdata as $jobs)
                                       @foreach($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                        @endforeach
                                    @endforeach
                                @endif 
                            </select>
                        </div>
                        <div>
                            <div>
                                <div class="table-responsive-order-task">
                                    <table class="table table-borderless">
                                        <tbody id="changeOrderContainer">
                                        
                                            <!-- Repeat rows as needed -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-lg-8">
                 <div id="add_changeorder">
                    <div class="row"> 
                        <div class="col-md-12 col-lg-12">
                            <form Method="POST" action="{{ route('AddchangeOrder') }}">
                                @csrf
                                <h5 class="text-white">Add New Change Order</h5>
                                <div class="row py-4 px-2 bg-white">
                                    <div class="col-md-12 mb-3">
                                        <select class="form-select text-muted" name="job_id" required>
                                            <option selected disabled value="">Select Job</option>
                                            @if($jobdata)
                                                @foreach($jobdata as $jobs)
                                                   @foreach($jobs as $job)
                                                        <option value="{{ $job->id }}">{{ $job->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            @endif 
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <input type="number" name="receiptNo" class="form-control form-control-sm" placeholder="Change Order # *" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <input type="text" name="date" class="form-control form-control-sm" id="changeorderDate" placeholder="change order date" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Order Tittle *" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <select name="clientId[]"  class="form-control form-control-sm" multiple>
                                            <option disabled>Select Recipients</option>
                                           @foreach(@$recepeints as $recepeint)
                                           <option value="{{ $recepeint->id }}">  {{ $recepeint->name  }}</option>
                                              
                                           @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="col-md-12 text-center mb-4 myitemerow">
                                        <div class="add_item">
                                            <div class="row item-row pt-2">
                                                <div class="col-md-2 mb-2">
                                                    <input class="form-control form-control-sm" type="text" name="ItemAll[0][code]"
                                                        placeholder="Item code *" required="">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <input class="form-control form-control-sm" type="text" name="ItemAll[0][name]"
                                                        placeholder="Item Title" required="">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <textarea class="form-control form-control-sm" rows="1"
                                                        name="ItemAll[0][desc]" placeholder="Description" required=""></textarea>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" name="ItemAll[0][amount]" class="form-control cost-input" id="cost" placeholder="Cost" step="0.01" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-success add-item-btn">Add Item</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-md-12 text-center ">
                                        <button class="btn call-Inspection w-100">
                                            Send to Client</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>

                    <div id="changeorderDetails">


                    </div>
                </div>
            </div>
        </div>

@endsection
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@section('script')
{{-- //scrip here --}}
<script>
    $(document).ready(function() {
        let itemCount = 1; // Counter to keep track of items

        // Function to add a new item row
        $('.myitemerow').on('click', '.add-item-btn', function() {
            let newRow = `
                <div class="row item-row">
                    <div class="col-md-2 mb-2">
                        <input class="form-control form-control-sm" type="text" name="ItemAll[${itemCount}][code]"
                            placeholder="Item code *" required="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input class="form-control form-control-sm" type="text" name="ItemAll[${itemCount}][name]"
                            placeholder="Item Title" required="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <textarea class="form-control form-control-sm" rows="1"
                            name="ItemAll[${itemCount}][desc]" placeholder="Description" required=""></textarea>
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="ItemAll[${itemCount}][amount]" class="form-control cost-input" id="cost" placeholder="Cost" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                    </div>
                </div>`;
            $('.add_item').append(newRow);
            itemCount++; // Increment item count
        });

        // Function to remove an item row
        $('.myitemerow').on('click', '.remove-item-btn', function() {
            $(this).closest('.item-row').remove();
        });
        $('.myitemerow').on('blur', '.cost-input', function() {
        let value = parseFloat($(this).val()).toFixed(2);
        if (!isNaN(value)) {
            $(this).val(value);
        }
    });
    });
</script>
<script>
function fetchJobchangeOrderDetails() {
    var jobId = $('#jobSelect').val(); // Get the selected job ID
    if (jobId) {
        $.ajax({
            url: '{{ route('getChangeOrderListByJobid') }}', // Replace with your server-side endpoint
            type: 'GET',
            data: { job_id: jobId },
            success: function(response) {
                // Handle the response data from the server
                if (response.success) {
                    // Handle the change order data
                    displayChangeOrderData(response.changeOrder);
                } else {
                    console.error('Error: Unable to fetch change order data.');
                }

            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
}
// Function to display the change order data in the UI
function displayChangeOrderData(changeOrderData) {
    let output = '';
    if (changeOrderData && changeOrderData.length > 0) {
        changeOrderData.forEach(function(order) {
        const statusClass = order.status === 'Approved' ? 'approved-badge' : 'new-badge';
        //const formattedDate = formatDate(new Date(order.date));
        const formattedDate  =  moment(order.date).format('ddd MMM D, YYYY');
            output += `<tr onclick="fetchOrderDetails(${order.job.id}, ${order.id},this)" style="cursor:pointer">
                        <td>
                            <p class="fw-600 text-034078 font-16 m-0">${ order.title }</p>
                            <small class="user-des">${order.job.name}</small>
                        </td>
                        <td class="text-end">
                            <span class="status-badge ${statusClass} text-white">${ order.status }</span><br>
                            <small class="client-label">${ formattedDate }</small>
                        </td>
                       </tr>`;
        });
    } else {
        output = '<tr><td colspan="3">No change order data available.</td></tr>';
    }

    // Display the data in an HTML element, for example, with id="changeOrderContainer"
    $('#changeOrderContainer').html(output);
}


function fetchOrderDetails(jobId, orderId, rowElement) {
    $('#add_changeorder').hide();
    $('#changeorderDetails').show();
    const allRows = document.querySelectorAll('tr.tr-active');
    allRows.forEach(row => row.classList.remove('tr-active'));
    // Add 'active' class to the clicked row
    rowElement.classList.add('tr-active');
   
    $.ajax({
        url: '{{ route('SingleChangeOrderdetails') }}', // Replace with your actual endpoint
        method: 'POST', // Or GET depending on your API
        data: {
            _token: "{{ csrf_token() }}",
            job_id: jobId,
            order_id: orderId,
            // Include any additional data if needed
        },
        success: function(response) {
         if (response.success) {
               
          let changeOrderHTML = '';
                    // Loop through the change order data and build the HTML structure
            const changeOrders = response.changeOrder;
             changeOrders.forEach(order => {
                        //console.log('hello->>', JSON.stringify(order, null, 2));
                        var order_id =  order.id;
                        var orderdate = order.date;
                        var formattedorderDate = moment(orderdate).format("MMMM DD, YYYY");
                        var contractorname = order.user.name;
                        const jobname = order.job.name;
                        const job_address = order.job.address;
                        const job_city = order.job.city;
                        const job_state = order.job.state;
                        const job_zipcode = order.job.pincode;
                        const businessName = order.user.meta.find(meta => meta.key === 'Business_name')?.value;
                        const contact_name = order.contact_name;
                        const order_approved_date = order.approved_date;
                        const order_digitalsign = order.digital_sign;
                        const imageUrl = `${window.location.hostname}/${order_digitalsign}`;
                        var formattedorderApproveDate = moment(order_approved_date).format("MMMM DD, YYYY");
                       
                        changeOrderHTML += `
                            <h5 class="text-white">${order.title}</h5>
                            <div class="row bg-white">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex justify-content-between align-items-center order-header px-3 py-2">
                                        <div class="text-034078">
                                            <h6>Change Order #${order.receiptNo}</h6>
                                        </div>
                                        <div class="text-034078">
                                            <h6>${formattedorderDate}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 bg-white">
                                    <div class="row ">
                                        <div class="col-md-4 add-contact-user-row p-3">
                                            <p class="m-0 text-707070 font-12">Contractor</p>
                                            <p class="client-label-value m-0">${contractorname}</p>
                                            <p class="Designation m-0">${businessName}</p>
                                        </div>
                                        <div class="col-md-4 add-contact-user-row p-3">
                                            <p class="m-0 text-707070 font-12">Project</p>
                                            <p class="client-label-value m-0">${jobname}</p>
                                            <p class="Designation m-0">${job_address} ${job_city}, ${job_state} ${job_zipcode}</p>
                                        </div>`;
                const jobClientDetails = response.jobClientDetails;
                jobClientDetails.forEach(jobClientDetail => {
                        
                        //console.log('hello->>', JSON.stringify(jobClientDetail, null, 2));
                        changeOrderHTML += ` <div class="col-md-4 add-contact-user-row p-3">
                                                <p class="m-0 text-707070 font-12">Client</p>
                                                <p class="client-label-value m-0">${jobClientDetail.name}</p>
                                                <p class="Designation m-0">${jobClientDetail.address} ${jobClientDetail.city} ,${jobClientDetail.state} ${jobClientDetail.pincode}</p>
                                            </div>`;
                            });
                        changeOrderHTML += ` </div>
                                                </div>
                                                <div class="col-md-12 p-0">
                                                    <div class="">
                                                        <table class="table table-responsive table-bordered bg-white jobs-table">
                                                            <thead class="jobs-thead">
                                                                <tr>
                                                                    <th>Item ID</th>
                                                                    <th>Item Name</th>
                                                                    <th>Item Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="jobs-table-body">
                        `;

                        // Loop through items within the order
                        let totalCost = 0;
                        order.item.forEach(items => {
                            totalCost += items.cost;
                            const formattedCost = items.cost.toLocaleString('en-US', { 
                                style: 'currency', 
                                currency: 'USD', 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            });
                            changeOrderHTML += `
                                <tr>
                                    <td>${items.item_code}</td>
                                    <td>${items.title}</td>
                                    <td>${formattedCost} <span class="info-icon" data-bs-toggle="tooltip" title="${items.description}">ⓘ</span></td>
                                </tr>
                            `;
                        });

                        // Add the total row
                        const totalformatedcost = totalCost.toLocaleString('en-US', { 
                                style: 'currency', 
                                currency: 'USD', 
                                minimumFractionDigits: 2, 
                                maximumFractionDigits: 2 
                            });
                        changeOrderHTML += `
                                <tr class="total-row">
                                    <td colspan="2" class="text-start fw-600 text-034078">Total</td>
                                    <td class="fw-600 text-034078">${totalformatedcost}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>`;

                const jobContact = response.contact;
                const jobContactCount = jobContact.length;
                const morecontact = jobContactCount-1;
               
               // jobContact.forEach(contacts => {
                   // console.log('hello->>', JSON.stringify(jobContact[0], null, 2));
                    changeOrderHTML += `
                    <div class="col-md-12 text-center">
                        
                       ${order_digitalsign ? `<img src="../${order_digitalsign}" width="100" />` : ''}
                       ${ (jobContactCount>0)? ` <p class="text-707070 font-14"> Shared with:- <span class="text-286FAC fw-600">${jobContact[0][0].name}</span> and ${morecontact} more</p>`:`<p class="text-707070 font-14"> Shared with:- <span class="text-286FAC fw-600">Not Share with anyone</span></p>`}
                        
                    </div>`;

               // });

                changeOrderHTML += `
                    <div class="col-md-12 text-center mb-4">
                            <button class="btn ${order_approved_date !== null ? `approved-badge1` : `call-Inspection`}" 
                                data-toggle="modal" 
                                data-target="${order_approved_date !== null ? '' : `#ApproveOrder${order_id}`}" 
                                onclick="${order_approved_date !== null ? 'event.preventDefault();' : ''}">
                            ${order_approved_date !== null ? `Approved on ${formattedorderApproveDate}` : `Approve Order`}
                        </button>

                         </div>
                         <!-- Modal Structure -->
                            <div class="modal fade" id="ApproveOrder${order_id}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h5 class="modal-title title-model" id="modalLabel1">Do you approve this change order</h5>
                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body">
                                            <form method="POST" action="{{ route('UpdatechangeOrder') }}" enctype="multipart/form-data" id="approveOrderForm${order_id}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="hidden" name="id" value="${order_id}">
                                                        <input type="hidden" name="status" value="Approved">
                                                        <input type="checkbox" class="form-check-input" name="includesignature" id="includesignature${order_id}">
                                                        <label class="form-check-label text-muted fw-600" for="includesignature${order_id}">
                                                            Include Signature
                                                        </label>
                                                    </div>

                                                    <!-- Signature pad container (hidden by default) -->
                                                    <div class="col-md-12 mt-2" id="signature-pad-container${order_id}" style="display: none;">
                                                        <div id="signature-pad${order_id}" class="signature-pad">
                                                            <div id="canvas-wrapper${order_id}" class="signature-pad--body" style="text-align:center;">
                                                                <canvas style="border: 1px solid #ccc; padding: 7px 10px;" ></canvas>
                                                            </div>
                                                            <div class="signature-pad--footer">
                                                    
                                                                <div class="signature-pad--actions">
                                                                    <button type="button" class="button clear clear_sign" data-action="clear">Clear</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="digital_sign" id="signature_image${order_id}">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                                            Approve
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                });

                    // Insert the generated HTML into the #changeorderDetails div
                    document.getElementById('changeorderDetails').innerHTML = changeOrderHTML;
                } else {
                    document.getElementById('changeorderDetails').innerHTML = '<p>No change order details available.</p>';
                }
           
        },
        error: function(xhr, status, error) {
            // Handle any errors here
            console.error(error);
        }
    });
}

</script>

<script>
    $(document).ready(function () {
        $(document).on('shown.bs.modal', function (e) {
            const modalId = e.target.id;
            const order_id = modalId.replace('ApproveOrder', ''); // Extract order_id from modal ID

            const includeSignatureCheckbox = document.getElementById('includesignature' + order_id);
            const signaturePadContainer = document.getElementById('signature-pad-container' + order_id);
            const canvas = document.querySelector('#canvas-wrapper' + order_id + ' canvas');
            let signaturePad;

            // Add event listener to the checkbox to toggle the signature pad visibility
            includeSignatureCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    // Show the signature pad container
                    signaturePadContainer.style.display = 'block';

                    // Initialize the signature pad
                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)'
                    });

                    // Resize the canvas to fit the container size
                    resizeCanvas();
                } else {
                    // Hide the signature pad container and clear the signature if unchecked
                    signaturePadContainer.style.display = 'none';
                    if (signaturePad) {
                        signaturePad.clear();
                    }
                }
            });

            // Function to resize the canvas for high resolution displays
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }

            // Clear the signature when the clear button is clicked
            document.querySelector(`#signature-pad${order_id} .clear`).addEventListener('click', () => {
                if (signaturePad) {
                    signaturePad.clear();
                }
            });

            window.addEventListener('resize', resizeCanvas);

            // Before the form is submitted, convert the signature to an image
            $(`#approveOrderForm${order_id}`).on('submit', function (event) {
                if (signaturePad && !signaturePad.isEmpty()) {
                    const signatureDataUrl = signaturePad.toDataURL(); // Get the signature image as base64
                    document.getElementById('signature_image' + order_id).value = signatureDataUrl; // Set the hidden input value to the signature image
                }
            });
        });
        $('#addchangeOrderbtn').on('click', function() {
            $('#add_changeorder').show(); 
            $('#changeorderDetails').hide();
            // This will hide if visible or show if hidden
        });
    });

    $(function () {
        $('#changeorderDate').datetimepicker({format:'MMM DD, YYYY'});
    });
</script>

@stop