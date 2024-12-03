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
            <div class="bg-white ">
                <div class="row">
                  <div class="col-sm-12 col-md-12 col-lg-12" style="padding:20px;">
                   
                     Coming Soon...........
                    </div>
                </div>
            </div>
        </div>
@endsection
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @section('script')
@stop