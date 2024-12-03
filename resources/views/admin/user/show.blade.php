
@extends('layouts.app')

@section('content')

<div class="header pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-md-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Show User Subscription</h6>
                </div>

                <div class="col-lg-6 col-md-7">
                    <a class="btn btn-primary" href="{{ route('user.index') }}" style="float:right;"> Back</a>
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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="post" action="" enctype="multipart/form-data">
                        @csrf
						{{ method_field('PUT') }}
                        <div class="col-md-6 pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-control-label">Plan Name</label>
                                            </div>

                                            <div class="col-md-9">
                                                <input type="text" name="name" class="form-control" value="{{ @$data->plan->name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-control-label">Start Date</label>
                                            </div>

                                            <div class="col-md-9">
                                                <input type="text" name="email" class="form-control" value=" <?php if(@$data->plan->name){?>{{ \Carbon\Carbon::parse(@$data->start_date)->format('F j, Y') }}<?php } ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-control-label">End Date</label>
                                            </div>

                                            <div class="col-md-9">
                                                <input type="text" name="email" class="form-control" value="<?php if(@$data->plan->name){?>{{ \Carbon\Carbon::parse(@$data->end_date)->format('F j, Y') }} <?php } ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <h6 class="h2" style="color:#0081a0;">Payment History</h6><br>

                    <div class="table-responsive">
                        <table class="table align-items-center table-dark table-flush">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Sr No.</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Transaction Id</th>
                                    <th scope="col">Payment Date</th>
                                </tr>
                            </thead>
                           
                            <tbody class="list">
                                @if(count($payment_history)> 0)
                                    @foreach($payment_history as $key =>$history)
                                        <tr>
                                            <td class="budget">{{ ++$key }}</td>
                                            <td>${{ $history->amount }}</td>
                                            <td>{{ $history->transaction_id }}</td>
                                            <td> {{ \Carbon\Carbon::parse($history->payment_date)->format('F j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7" class="text-center">No Record Found</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
