@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="header  pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Template</h6>
                </div>

                <div class="col-lg-6 col-7">
                    <a class="btn btn-primary" href="{{ route('template.index') }}" style="float:right;"> Back</a>
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
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Edit Template </h3>
                        </div>
                        <!--                        <div class="col-4 text-right">-->
                        <!--                            <a href="#!" class="btn btn-sm btn-primary">Settings</a>-->
                        <!--                        </div>-->
                    </div>
                </div>
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
                    <form method="post" action="{{ route('template.update',$data->id) }}" enctype="multipart/form-data">
                        @csrf
						{{ method_field('PUT') }}
                       
						<div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Email type</label>
										<select name="type" class="form-control">
										@foreach($type as $key=>$val)
										@if($key==$data->type)
										<option value="{{$key}}" selected>{{$val}}</option>
										@else
											<option value="{{$key}}" >{{$val}}</option>
										@endif
										@endforeach
										</select>
                                        
                                        @error('status')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
                            </div>
							<div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Status</label>
										<select name="status" class="form-control">
										<option value="1" {{($data->status==1)?"selected":''}}>Active</option>
										<option value="0" {{($data->status==0)?"selected":''}}>InActive</option>
										</select>
                                        
                                        @error('status')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
								 <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Content</label>
										<textarea id="summernote" name="content" class="summernote form-control" >{{ $data->content }}</textarea>
                                        
                                        @error('content')<div class="text-danger">{{ $message }}*</div>@enderror
                                    </div>
                                </div>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@endsection
