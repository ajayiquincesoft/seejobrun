@extends('user.layout.userdashboard')

@section('content')
 <?php //echo "heyy".print_r($data);?>
    
        <div class="container-fluid content dashb">
            <div class="row">
                <div class="col-md-12">
                    <div id="notification-prompt" style="display: none;">
                        <p>Stay updated! Enable notifications to get important updates.</p>
                        <button id="enable-notifications">Enable Notifications</button>
                    </div>
                </div>
                <div class="col-md-3 px-3 py-3">
					<a href="{{ route('leads') }}">
						<div class="row align-items-center contentdiv">
							<div class="col-md-4 p-0 d-flex justify-content-center">
								<img src="{{ asset('assets') }}/images/Lead.png" alt="" class="img-fluid">
							</div>
							<div class="col-md-3 p-0 d-flex justify-content-center">
								<div class="overlay-text ">Leads</div>
							</div>
							<div class="col-md-7 position-absolute lead-section  p-0">
								<div class="jobs-lead position-absolute top-0 end-0">{{ $data['total_leads']}}</div>
								{{-- <div class="leads position-absolute bottom-0 end-0">{{ $data['total_leads']}} Leads</div> --}}
							</div>
						</div>
					</a>
                </div>
                <div class="col-md-3 px-3 py-3">
					<a href="{{ route('user.jobs') }}">
						<div class="row align-items-center contentdiv">
							<div class="col-md-4 p-0 d-flex justify-content-center">
								<img src="{{ asset('assets') }}/images/Group 8136.png" alt="" class="img-fluid">
							</div>
							<div class="col-md-3 p-0 d-flex justify-content-center">
								<div class="overlay-text ">Jobs</div>
							</div>
							<div class="col-md-7 position-absolute lead-section  p-0">
								<div class="jobs-lead position-absolute top-0 end-0">{{ $data['job'] }}</div>
								{{-- <div class="leads position-absolute bottom-0 end-0">{{ $data['total_leads']}} Leads</div> --}}
							</div>
						</div>
					</a>
                </div>
                <div class="col-md-3  px-3  py-3">
                    <a class="n-link" href="{{ route('getTodolist') }}">
                    <div class="row align-items-center contentdiv">
                        <div class="col-md-4 p-0 d-flex justify-content-center">
                            <img src="{{ asset('assets') }}/images/Group 8137.png" alt="" class="img-fluid">
                        </div>
                        <div class="col-md-6 p-0 d-flex justify-content-center">
                            <div class="overlay-text ">To Do List</div>
                        </div>
                        <div class="col-md-2  position-relative p-0">
                            <!-- <div class="jobs-lead position-absolute top-0 end-0">6</div>
                            <div class="leads position-absolute bottom-0 end-0">2 Leads</div> -->
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3  px-3  py-3">
                    <a href="{{ route('getEvents') }}">
                        <div class="row align-items-center contentdiv">
                            <div class="col-md-4 p-0 d-flex justify-content-center">
                                <img src="{{ asset('assets') }}/images/Group 8138.png" alt="" class="img-fluid">
                            </div>
                            <div class="col-md-6 p-0 d-flex justify-content-center">
                                <div class="overlay-text ">My Appointments</div>
                            </div>
                            <div class="col-md-2  position-relative p-0" >
                                <!-- <div class="jobs-lead position-absolute top-0 end-0">6</div>
                                <div class="leads position-absolute bottom-0 end-0">2 Leads</div> -->
                            </div>
                        </div>
                    </a> 
                </div>
            </div>
            <div class="row">
                <div class="col-md-3  px-3  py-3">
                    <a href="{{ route('gettimecard') }}">
                        <div class="row align-items-center contentdiv">
                            
                            <div class="col-md-4 p-0 d-flex justify-content-center">
                                <img src="{{ asset('assets') }}/images/Group 8139.png" alt="" class="img-fluid">
                            </div>
                            <div class="col-md-4 p-0 d-flex justify-content-center">
                                <div class="overlay-text ">Time Card</div>
                            </div>
                        
                            <div class="col-md-7 position-absolute lead-section  p-0">
                                <div class="jobs-lead position-absolute top-0 end-0">{{ $data['time_card']}}</div>
                            </div>
                    
                        </div>
                    </a>
                </div>
                <div class="col-md-3  px-3  py-3">
                    <a href="{{ route('changeorders') }}">
                    <div class="row align-items-center contentdiv">
                        <div class="col-md-4 p-0 d-flex justify-content-center">
                            <img src="{{ asset('assets') }}/images/Group 8141.png" alt="" class="img-fluid">
                        </div>
                        <div class="col-md-6 p-0 d-flex justify-content-center">
                            <div class="overlay-text ">Change Order</div>
                        </div>
                        <div class="col-md-2  position-relative p-0" >
                            <!-- <div class="jobs-lead position-absolute top-0 end-0">6</div>
                            <div class="leads position-absolute bottom-0 end-0">2 Leads</div> -->
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3  px-3  py-3">
                    
                        <div class="row align-items-center contentdiv">
                            
                            <div class="col-md-4 p-0 d-flex justify-content-center">
                            <a href="{{ route('GetAllMyContact') }}">
                                <img src="{{ asset('assets') }}/images/Group 8142.png" alt="" class="img-fluid">
                            </a>
                            </div>
                            <div class="col-md-4 p-0 d-flex justify-content-center">
                                <a href="{{ route('GetAllMyContact') }}">
                                    <div class="overlay-text ">My Contacts</div>
                                </a>
                            </div>
                       
                            <div class="col-md-7 position-absolute lead-section  p-0">
                                <div class="contacts position-absolute top-0 end-0">{{ $user->credit_contact }} Credits</div>
                                <div class="leads position-absolute bottom-0 end-0" data-toggle="modal" data-target="#Buycredit">Buy More</div>
                            </div>
                        </div>
                  
                </div>
                <div class="col-md-3  px-3  py-3">
                    <a href="{{ route('MyDailyTasks') }}">
                    <div class="row align-items-center contentdiv">
                        <div class="col-md-4 p-0 d-flex justify-content-center">
                            <img src="{{ asset('assets') }}/images/Group 8143.png" alt="" class="img-fluid">
                        </div>
                        <div class="col-md-6 p-0 d-flex justify-content-center">
                            <div class="overlay-text ">My Daily Tasks</div>
                        </div>
                        <div class="col-md-2  position-relative p-0" >
                            <!-- <div class="jobs-lead position-absolute top-0 end-0">6</div>
                            <div class="leads position-absolute bottom-0 end-0">2 Leads</div> -->
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
		
		
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('script')

<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

<script type="text/javascript">
    // Firebase Configuration
    const firebaseConfig = {
        apiKey: "AIzaSyA_IjKrHD9xUpV0NRBuNGjzA5DD0MvCEJE",
        authDomain: "seejobrun.firebaseapp.com",
        projectId: "seejobrun",
        storageBucket: "seejobrun.firebasestorage.app",
        messagingSenderId: "718155099941",
        appId: "1:718155099941:web:3646c79607bd1e4af074cc",
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    //console.log(messaging);

    // Request Notification Permission and Get Token
    async function requestNotificationPermission() {
        try {
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                const token = await messaging.getToken({
                    vapidKey: "BCW3dBfxpOKuqD-V2FGkSCsjjgERRp8XycQdplqMDfiLLOPVPdbbBdc-YX9Sq1vYl_XkfFSAFQQGkrjDy44xxow"
                });
                if (token) {
                   // alert(token);
                    //console.log("FCM Token:", token);
                    $.ajax({
                    url: '{{ route('WebFcmToken') }}', // Replace with your server endpoint
                    type: 'POST',
                    data: {
                        fcmtoken: token,
                        _token: '{{ csrf_token() }}' // Include CSRF token if using Laravel
                    },
                    success: function(response) {
                        console.log("Token saved successfully:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error saving token:", error);
                    }
                });
                    // Send the token to your server
                } else {
                   // console.log("No registration token available.");
                }
            } else {
                //console.log("Notification permission denied.");
            }
        } catch (error) {
           // console.error("Error during notification setup:", error);
        }
    }
    if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js?version=1.0')
        .then(function(registration) {
           // console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch(function(error) {
            //console.error('Service Worker registration failed:', error);
        });
}

    requestNotificationPermission();
</script>


@stop