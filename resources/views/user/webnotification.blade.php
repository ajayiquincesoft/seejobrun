@extends('user.layout.userdashboard')
@section('content')
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
    </div>
    <div class="row">
        <div class="col-md-12 ">
            <h5 class="text-white">Notifications</h5>
            
                <div class="list-group">
                    @forelse ($notifications as $notification)
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start mt-3">
                        <div class="me-auto">
                            <a  href="#" onclick="changeStatus({{ $notification->id }}, this)"> <h5 class="mb-1 noti-title">@if($notification->status == 0)
                                <i class="bi bi-bell-fill text-warning" data-status="0"></i> <!-- Unread Icon -->
                            @else
                                <i class="bi bi-check-circle-fill text-success" data-status="1"></i> <!-- Read Icon -->
                            @endif{{ $notification->title }}</h5></a>
                            <p class="mb-1">{{ $notification->body}}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                       
                    </div>
                @empty
                    <p class="text-muted">No notifications available.</p>
                @endforelse
                  </div>
                  <!-- Pagination Links -->
            <div class="card-footer py-4">
                {{ $notifications->links('vendor.pagination.admin') }}
            </div>

        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('script')


@stop