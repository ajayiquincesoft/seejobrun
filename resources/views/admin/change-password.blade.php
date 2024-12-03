@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="header pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Admin Change Password</h6>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-lg-12 order-xl-1">
            <div class="card">
                <div class="card-body">
                    @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger">

                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="post" action="{{ route('admin.password') }}" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Current Password</label>
                                    <input type="password" name="currentPassword" class="form-control" value="{{ old('currentPassword') }}">
                                    @error('currentPassword')<div class="text-danger">{{ $message }}*</div>@enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">New Password</label>
                                    <input type="password" name="newPassword" class="form-control" value="{{ old('newPassword') }}">
                                    @error('newPassword')<div class="text-danger">{{ $message }}*</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label">Confirm Password</label>
                                    <input type="password" name="confirmPassword" class="form-control" value="{{ old('confirmPassword') }}">
                                    @error('confirmPassword')<div class="text-danger">{{ $message }}*</div>@enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" style="margin-top:32px;">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
