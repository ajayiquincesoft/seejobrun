@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="header pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Edit User</h6>
                </div>

                <div class="col-lg-6 col-7">
                    <a class="btn btn-primary" href="{{ route('user.index') }}" style="float:right;"> Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page content -->
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
                    <form method="post" action="{{ route('user.update',$data->id) }}" enctype="multipart/form-data">
                        @csrf
						{{ method_field('PUT') }}
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $data->name }}" >
                                        @error('name')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Email</label>
                                        <input type="text" name="email" class="form-control" value="{{ $data->email }}">
                                        @error('email')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
                            </div>

							<div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">User Status</label>
										<select name="user_status" class="form-control">
										<option value="1" {{ ($data->status==1)?"selected":'' }} >Active</option>
										<option value="0" {{ ($data->status==0)?"selected":'' }}>Inactive</option>
										</select>
                                        
                                        @error('password')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
								
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Update Password</label>
                                        <input type="text" name="password" class="form-control" value=""  autocomplete="off">
                                        @error('password')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
                            </div>
							
                            <div class="row">
                                <input type="hidden" id="total_meta" name="total_meta" value="{{ count($data->metarelation) }}">
                                @foreach($data->metarelation as $datas)
    		                        <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-control-label">{{$datas->key}}</label>
                                            <input type="text" name="{{$datas->key}}" class="form-control" value="{{$datas->value}}">
                                            @error('email')<div class="text-danger">{{ $message }}*</div>@enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>
							
							
                            <div class="row">
                                <div class="col-lg-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
