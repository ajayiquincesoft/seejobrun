<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/mystyle.css?v=1.2.1') }}">
	<link rel="stylesheet" href="{{ asset('assets/custom-style.css?v=1.2.1') }}">
     <link rel="stylesheet" href="{{ asset('assets/bootstrap-datetimepicker.min.css?v=1.2.1') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <title>Dashboard</title>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
	<link rel="stylesheet" href="{{ asset('assets/intl-tel-input-master/build/css/intlTelInput.css?v=1.2.1') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fullcalender.css?v=1.2.1') }}">
    <link rel="stylesheet" href="{{ asset('assets/simple-calendar.css?v=1.2.1') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    

</head>
<?php 

$user = Auth::user(); 
$user['id'];
?>
@php
use App\Models\Contact;
    $contacts = Contact::where('contact_user_id', $user['id'])->first();
@endphp
<body>
    <!-- Sidebar -->
	 <div id="sidebar" class=" text-white p-3">
		 <div class="position-relative ">
            <img class="sidebar-logo" src="{{ asset('assets') }}/images/headerlogo.png" alt="inner logo">
            <button class="close-btn" aria-label="Close">&times;</button>
        </div>
        <ul class="nav custom-fontsize flex-column">
            <a href="{{ route('user.dashboard') }}" class="n-link">
                <li class="nav-item dd1 {{ request()->is('user/dashboard*') ? 'activenavitem' : '' }}">

                    <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.6574 8.24873L10.6574 0.248734C10.4749 0.0884152 10.2403 0 9.99743 0C9.75453 0 9.51992 0.0884152 9.33743 0.248734L0.337433 8.24873C0.184469 8.38405 0.0766013 8.56295 0.0283327 8.76139C-0.0199359 8.95982 -0.00629088 9.16828 0.0674333 9.35873C0.139952 9.54667 0.267538 9.70832 0.433483 9.82252C0.599427 9.93672 0.795992 9.99814 0.997433 9.99873H1.99743V18.9987C1.99743 19.264 2.10279 19.5183 2.29033 19.7058C2.47786 19.8934 2.73222 19.9987 2.99743 19.9987H16.9974C17.2626 19.9987 17.517 19.8934 17.7045 19.7058C17.8921 19.5183 17.9974 19.264 17.9974 18.9987V9.99873H18.9974C19.1989 9.99814 19.3954 9.93672 19.5614 9.82252C19.7273 9.70832 19.8549 9.54667 19.9274 9.35873C20.0012 9.16828 20.0148 8.95982 19.9665 8.76139C19.9183 8.56295 19.8104 8.38405 19.6574 8.24873ZM10.9974 17.9987H8.99743V14.9987C8.99743 14.7335 9.10279 14.4792 9.29033 14.2916C9.47786 14.1041 9.73222 13.9987 9.99743 13.9987C10.2627 13.9987 10.517 14.1041 10.7045 14.2916C10.8921 14.4792 10.9974 14.7335 10.9974 14.9987V17.9987ZM15.9974 17.9987H12.9974V14.9987C12.9974 14.2031 12.6814 13.44 12.1188 12.8774C11.5561 12.3148 10.7931 11.9987 9.99743 11.9987C9.20178 11.9987 8.43872 12.3148 7.87611 12.8774C7.3135 13.44 6.99743 14.2031 6.99743 14.9987V17.9987H3.99743V9.99873H15.9974V17.9987ZM3.62743 7.99873L9.99743 2.33873L16.3674 7.99873H3.62743Z" />
                    </svg>
                <span class="nav-link dd"> Dashboard</span>
                </li>
            </a>
            <a  class="n-link" href="{{ route('leads') }}" >
                <li class="nav-item  dd1 {{ request()->is('user/leads*') || request()->is('user/lead*') ? 'activenavitem' : '' }}">
                    {{-- <svg  viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0 4C0 3.44772 0.407018 3 0.909091 3H19.0909C19.593 3 20 3.44772 20 4V11C20 11.5043 19.6586 11.9297 19.2036 11.9923L11.9309 12.9923L11.7055 11.0077L18.1818 10.1172V5H1.81818V10.1172L8.29455 11.0077L8.06906 12.9923L0.796336 11.9923C0.3414 11.9297 0 11.5043 0 11V4Z"
                           />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M7.27246 9.5C7.27246 8.9477 7.67948 8.5 8.18155 8.5H11.8179C12.32 8.5 12.727 8.9477 12.727 9.5V14.5C12.727 15.0523 12.32 15.5 11.8179 15.5H8.18155C7.67948 15.5 7.27246 15.0523 7.27246 14.5V9.5ZM9.09064 10.5V13.5H10.9088V10.5H9.09064Z"
                             />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M6.36328 2.5C6.36328 1.11929 7.38082 0 8.63601 0H11.3633C12.6185 0 13.636 1.11929 13.636 2.5V4H11.8178V2.5C11.8178 2.22386 11.6143 2 11.3633 2H8.63601C8.38501 2 8.18146 2.22386 8.18146 2.5V4H6.36328V2.5Z"
                             />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.90918 19V11.5H2.72736V18H17.2728V11.5H19.091V19C19.091 19.5523 18.684 20 18.1819 20H1.81827C1.3162 20 0.90918 19.5523 0.90918 19Z" />
                    </svg> --}}
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 82.58 122.88">
                    <path class="cls-1" d="M41.66,95.73c8,0,14.51,2.64,14.51,5.8s-6.62,5.8-14.78,5.8-14.78-2.61-14.78-5.8S33,95.79,41,95.74ZM57.55,16.4c-.59-.94-1.7-2.22-1.7-3.33a1.78,1.78,0,0,1,1.2-1.62C57,10.52,57,9.57,57,8.63c0-.55,0-1.11,0-1.66a5.84,5.84,0,0,1,.18-1,5.9,5.9,0,0,1,2.65-3.37,8.29,8.29,0,0,1,1.44-.69c.91-.33.47-1.86,1.47-1.88,2.33-.06,6.16,2.07,7.65,3.69a5.9,5.9,0,0,1,1.53,3.84l-.09,4.08a1.35,1.35,0,0,1,1,.84c.32,1.29-1,2.89-1.64,3.91s-2.77,4-2.77,4a.85.85,0,0,0,.2.48C72,25.54,82,22.59,82,31.89H46.66c0-9.3,10-6.35,13.39-11,.17-.25.25-.38.25-.49s-2.52-3.62-2.75-4ZM35.13,87.72a2.6,2.6,0,0,1-5.2,0v-18L.78,41.17a2.59,2.59,0,0,1,1.81-4.44H80a2.6,2.6,0,0,1,1.67,4.58L52.29,69.68v18a2.6,2.6,0,0,1-5.19,0V68.59a2.63,2.63,0,0,1,.8-1.86L73.58,41.91H8.94l25.27,24.7a2.61,2.61,0,0,1,.92,2V87.72ZM11.49,16.4c-.59-.94-1.7-2.22-1.7-3.33A1.79,1.79,0,0,1,11,11.45c-.05-.93-.09-1.88-.09-2.82,0-.55,0-1.11,0-1.66a5.85,5.85,0,0,1,.19-1,5.9,5.9,0,0,1,2.65-3.37,8.19,8.19,0,0,1,1.43-.69C16.1,1.55,15.66,0,16.66,0,19-.06,22.82,2.07,24.32,3.69a6,6,0,0,1,1.53,3.84l-.1,4.08a1.31,1.31,0,0,1,1,.84c.32,1.29-1,2.89-1.64,3.91s-2.76,4-2.76,4a.82.82,0,0,0,.19.48c3.4,4.67,13.4,1.72,13.4,11H.59c0-9.3,10-6.35,13.4-11,.17-.25.24-.38.24-.49s-2.52-3.62-2.74-4Zm36.37,83.94v2.08l-1.24,0a15.19,15.19,0,0,1-.24,1.92l-2.4-.43V101.7a1,1,0,0,0-.17-.68,2,2,0,0,0-.9-.18l-.11.91a3.6,3.6,0,0,1-.91,2.31,2.67,2.67,0,0,1-1.84.63,8.22,8.22,0,0,1-2-.22,2.17,2.17,0,0,1-1-.62,3.05,3.05,0,0,1-.69-1.83l-1.57,0V99.86l1.57.06a10.87,10.87,0,0,1,.37-2.22l2.46.42a9.2,9.2,0,0,0-.34,2.53,8.28,8.28,0,0,0,.06,1h.9l.1-.9a3,3,0,0,1,1.09-2.32A3.71,3.71,0,0,1,43,98a4.6,4.6,0,0,1,2.68.64,2.59,2.59,0,0,1,.93,1.71l1.28,0ZM41.6,93.81c9.83,0,17.77,4.26,17.77,9.43s-8.06,9.43-18,9.43-18-4.23-18-9.43,8-9.4,17.78-9.43Zm17.77,19.93c0,14.22-43.08,10.7-35.21-2.7,5.09,8.9,29.37,8.89,34.45,0a5.34,5.34,0,0,1,.76,2.7Z"/></svg>
                    <span class="nav-link dd"> Leads</span>
                </li>
              </a>
            <a  class="n-link" href="{{ route('user.jobs') }}" >
            <li class="nav-item  dd1 {{ request()->is('user/jobs*') || request()->is('user/job*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0 4C0 3.44772 0.407018 3 0.909091 3H19.0909C19.593 3 20 3.44772 20 4V11C20 11.5043 19.6586 11.9297 19.2036 11.9923L11.9309 12.9923L11.7055 11.0077L18.1818 10.1172V5H1.81818V10.1172L8.29455 11.0077L8.06906 12.9923L0.796336 11.9923C0.3414 11.9297 0 11.5043 0 11V4Z"
                       />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.27246 9.5C7.27246 8.9477 7.67948 8.5 8.18155 8.5H11.8179C12.32 8.5 12.727 8.9477 12.727 9.5V14.5C12.727 15.0523 12.32 15.5 11.8179 15.5H8.18155C7.67948 15.5 7.27246 15.0523 7.27246 14.5V9.5ZM9.09064 10.5V13.5H10.9088V10.5H9.09064Z"
                         />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.36328 2.5C6.36328 1.11929 7.38082 0 8.63601 0H11.3633C12.6185 0 13.636 1.11929 13.636 2.5V4H11.8178V2.5C11.8178 2.22386 11.6143 2 11.3633 2H8.63601C8.38501 2 8.18146 2.22386 8.18146 2.5V4H6.36328V2.5Z"
                         />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.90918 19V11.5H2.72736V18H17.2728V11.5H19.091V19C19.091 19.5523 18.684 20 18.1819 20H1.81827C1.3162 20 0.90918 19.5523 0.90918 19Z" />
                </svg>
                <span class="nav-link dd"> Jobs</span>
            </li>
          </a>
          <a class="n-link" href="{{ route('MyDailyTasks') }}">
            <li class="nav-item  dd1 {{ request()->is('user/mydailytask*') || request()->is('user/hiddentasks*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M20 17.0657V6.0448C20 4.42704 18.5542 3.11043 16.776 3.11043H15.8927V2.93437C15.8927 1.31605 14.447 0 12.6688 0H11.478H4.65394H3.22399C1.44628 0 0 1.31605 0 2.93437V13.9552C0 15.573 1.44634 16.8896 3.22399 16.8896H4.10715V17.0657C4.10715 18.684 5.55349 20 7.33119 20H8.76114H15.5852H16.1987H16.776C18.5542 20 20 18.684 20 17.0657ZM15.5852 19.0395H8.76114H7.33119C6.13305 19.0395 5.16226 18.1558 5.16226 17.0657V16.8896V15.929V6.0448C5.16226 4.95469 6.13305 4.07099 7.33119 4.07099H8.76114H14.8377H15.5852H15.8927H16.776C17.9741 4.07099 18.9449 4.95469 18.9449 6.0448V17.0657C18.9449 18.1558 17.9741 19.0395 16.776 19.0395H16.1987H15.5852Z" />
                    <path
                        d="M6.31989 7.4574H13.9293C14.1395 7.4574 14.3097 7.23363 14.3097 6.95737C14.3097 6.68111 14.1395 6.4574 13.9293 6.4574H6.31989C6.1097 6.4574 5.93945 6.68111 5.93945 6.95737C5.93945 7.23363 6.1097 7.4574 6.31989 7.4574Z" />
                    <path
                        d="M7.52246 10.5004C7.52246 10.7764 7.69318 11 7.90337 11H15.0339C15.2441 11 15.4149 10.7764 15.4149 10.5004C15.4149 10.2243 15.2441 10 15.0339 10H7.90337C7.69318 10 7.52246 10.2242 7.52246 10.5004Z" />
                    <path
                        d="M13.3833 13.4997C13.3833 13.2236 13.213 13 13.0029 13H6.31989C6.1097 13 5.93945 13.2236 5.93945 13.4997C5.93945 13.7758 6.10966 14 6.31989 14H13.0029C13.213 14 13.3833 13.7758 13.3833 13.4997Z" />
                    <path
                        d="M15.4146 16.5C15.4146 16.2237 15.2438 16 15.0337 16H8.77106C8.56087 16 8.39062 16.2237 8.39062 16.5C8.39062 16.7762 8.56083 17 8.77106 17H15.0337C15.2439 17 15.4146 16.7762 15.4146 16.5Z" />
                </svg>

                <span class="nav-link dd">  My Daily Tasks</span>
            </li>
          </a>
          <a class="n-link" href="{{ route('getTodolist') }}">
            <li class="nav-item  dd1 {{ request()->is('user/gettodolist*') || request()->is('user/gettodolist*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.85714 9.57143H1.71429C1.31982 9.57143 1 9.25195 1 8.85714V1.71429C1 1.31948 1.31982 1 1.71429 1H8.85714C9.2516 1 9.57143 1.31948 9.57143 1.71429V8.85714C9.57143 9.25195 9.2516 9.57143 8.85714 9.57143ZM2.42857 8.14286H8.14286V2.42857H2.42857V8.14286Z" />
                    <path
                        d="M3.94747 21C3.76472 21 3.58196 20.9302 3.44245 20.7907L1.20926 18.5579C0.930246 18.2789 0.930246 17.8269 1.20926 17.5478C1.48828 17.2688 1.94029 17.2688 2.21931 17.5478L3.94747 19.2757L8.35212 14.8707C8.63114 14.5917 9.08315 14.5917 9.36216 14.8707C9.64118 15.1497 9.64118 15.6017 9.36216 15.8807L4.4525 20.7907C4.31299 20.9302 4.13023 21 3.94747 21Z" />
                    <path
                        d="M20.2859 6H13.143C12.7485 6 12.4287 5.68052 12.4287 5.28571C12.4287 4.8909 12.7485 4.57143 13.143 4.57143H20.2859C20.6803 4.57143 21.0001 4.8909 21.0001 5.28571C21.0001 5.68052 20.6803 6 20.2859 6Z" />
                    <path
                        d="M20.2859 17.4286H13.143C12.7485 17.4286 12.4287 17.1091 12.4287 16.7143C12.4287 16.3195 12.7485 16 13.143 16H20.2859C20.6803 16 21.0001 16.3195 21.0001 16.7143C21.0001 17.1091 20.6803 17.4286 20.2859 17.4286Z" />
                </svg>

                <span class="nav-link dd">To Do List</span>
            </li>
        </a>
        
        <a class="n-link" href="{{ route('gettimecard') }}">
            <li class="nav-item  dd1 {{ request()->is('user/gettimecard*') || request()->is('user/getdetailtimecard*') || request()->is('user/sinlgetimecarddetails*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_2614_5265)">
                        <path
                            d="M9.9502 0.699997C8.12072 0.699997 6.33233 1.2425 4.81117 2.2589C3.29002 3.27531 2.10442 4.71996 1.40431 6.41017C0.704203 8.10039 0.521023 9.96026 0.877936 11.7546C1.23485 13.5489 2.11583 15.1971 3.40946 16.4907C4.7031 17.7844 6.35129 18.6653 8.14561 19.0223C9.93994 19.3792 11.7998 19.196 13.49 18.4959C15.1802 17.7958 16.6249 16.6102 17.6413 15.089C18.6577 13.5679 19.2002 11.7795 19.2002 9.95C19.1975 7.49756 18.2221 5.14632 16.488 3.41219C14.7539 1.67805 12.4026 0.702649 9.9502 0.699997ZM9.9502 17.6583C8.42563 17.6583 6.93531 17.2062 5.66768 16.3592C4.40005 15.5122 3.41205 14.3084 2.82863 12.8998C2.2452 11.4913 2.09255 9.94144 2.38998 8.44617C2.68741 6.9509 3.42155 5.57741 4.49958 4.49938C5.57761 3.42135 6.95111 2.6872 8.44638 2.38978C9.94165 2.09235 11.4915 2.245 12.9 2.82842C14.3086 3.41185 15.5124 4.39985 16.3594 5.66748C17.2064 6.9351 17.6585 8.42543 17.6585 9.95C17.6563 11.9937 16.8434 13.953 15.3983 15.3981C13.9532 16.8432 11.9939 17.6561 9.9502 17.6583Z" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M4.53339 1.84317C6.13677 0.771824 8.02183 0.199997 9.9502 0.199997C12.5351 0.202793 15.0138 1.23086 16.8416 3.05863C18.6693 4.88641 19.6974 7.3646 19.7002 9.94945C19.7002 11.8778 19.1284 13.7634 18.057 15.3668C16.9857 16.9702 15.4629 18.2199 13.6814 18.9578C11.8998 19.6958 9.93938 19.8889 8.04807 19.5126C6.15675 19.1364 4.41947 18.2078 3.05591 16.8443C1.69235 15.4807 0.763749 13.7434 0.387543 11.8521C0.0113373 9.96081 0.20442 8.00041 0.942374 6.21883C1.68033 4.43725 2.93001 2.91451 4.53339 1.84317ZM9.94993 1.2C8.21944 1.20005 6.52782 1.71322 5.08896 2.67464C3.65003 3.6361 2.52852 5.00266 1.86625 6.60152C1.20399 8.20037 1.03071 9.9597 1.36833 11.657C1.70595 13.3544 2.53931 14.9135 3.76301 16.1372C4.98672 17.3609 6.54582 18.1942 8.24316 18.5319C9.94049 18.8695 11.6998 18.6962 13.2987 18.0339C14.8975 17.3717 16.2641 16.2502 17.2256 14.8112C18.1869 13.3725 18.7001 11.6809 18.7002 9.95054C18.6977 7.63052 17.775 5.40624 16.1345 3.76574C14.494 2.1253 12.2699 1.20258 9.94993 1.2ZM12.7087 3.29036C11.3916 2.74478 9.9422 2.60203 8.54392 2.88017C7.14564 3.1583 5.86124 3.84483 4.85314 4.85293C3.84503 5.86104 3.15851 7.14544 2.88037 8.54372C2.60224 9.942 2.74499 11.3914 3.29057 12.7085C3.83615 14.0257 4.76006 15.1514 5.94546 15.9435C7.13087 16.7356 8.52452 17.1583 9.9502 17.1583C11.8613 17.1561 13.6934 16.3959 15.0448 15.0446C16.3963 13.6931 17.1564 11.8607 17.1585 9.94945C17.1584 8.52396 16.7357 7.13051 15.9437 5.94526C15.1516 4.75986 14.0259 3.83595 12.7087 3.29036ZM8.34883 1.89938C9.94109 1.58266 11.5915 1.74522 13.0914 2.36649C14.5913 2.98775 15.8732 4.03984 16.7752 5.38969C17.6771 6.73954 18.1585 8.32654 18.1585 9.95C18.1561 12.1261 17.2906 14.2129 15.7519 15.7517C14.2131 17.2904 12.1269 18.1559 9.95075 18.1583C8.32729 18.1583 6.73975 17.6769 5.38989 16.775C4.04004 15.873 2.98796 14.5911 2.36669 13.0912C1.74542 11.5913 1.58287 9.94089 1.89959 8.34863C2.21631 6.75637 2.99807 5.29378 4.14603 4.14583C5.29399 2.99787 6.75657 2.2161 8.34883 1.89938Z" />
                        <path
                            d="M10.3033 10.2917V6.65164C10.3033 6.47881 10.2346 6.31307 10.1124 6.19086C9.99021 6.06865 9.82446 6 9.65164 6C9.47881 6 9.31307 6.06865 9.19086 6.19086C9.06865 6.31307 9 6.47881 9 6.65164V10.5615C9.00004 10.7343 9.06872 10.9 9.19093 11.0222L11.1458 12.9771C11.2687 13.0958 11.4333 13.1615 11.6042 13.16C11.7751 13.1585 11.9385 13.09 12.0593 12.9691C12.1801 12.8483 12.2487 12.6849 12.2502 12.514C12.2516 12.3432 12.186 12.1786 12.0673 12.0557L10.3033 10.2917Z" />
                    </g>
                    <defs>
                        <clipPath id="clip0_2614_5265">
                            <rect width="20" height="20" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
                <span class="nav-link dd">Time Cards</span>
            </li>
        </a>
        <a class="n-link" href="{{ route('changeorders') }}">
            <li class="nav-item  dd1 {{ request()->is('user/changeorders*') || request()->is('user/changeorders*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.22222 19.8889C4.44952 19.8889 5.44444 18.894 5.44444 17.6667C5.44444 16.4394 4.44952 15.4444 3.22222 15.4444C1.99492 15.4444 1 16.4394 1 17.6667C1 18.894 1.99492 19.8889 3.22222 19.8889Z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M18.7779 6.55556C20.0052 6.55556 21.0001 5.56064 21.0001 4.33334C21.0001 3.10604 20.0052 2.11111 18.7779 2.11111C17.5506 2.11111 16.5557 3.10604 16.5557 4.33334C16.5557 5.56064 17.5506 6.55556 18.7779 6.55556Z"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M18.7776 6.55556V12.1111C18.7776 13.5845 18.1922 14.9976 17.1504 16.0395C16.1085 17.0814 14.6954 17.6667 13.222 17.6667H9.88867M9.88867 17.6667L13.222 14.3333M9.88867 17.6667L13.222 21"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M3.22266 15.4444V9.88889C3.22266 8.41546 3.80797 7.00239 4.84984 5.96052C5.89171 4.91865 7.30479 4.33333 8.77821 4.33333H12.1115M12.1115 4.33333L8.77821 1M12.1115 4.33333L8.77821 7.66667"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="nav-link dd">Change Order</span>
            </li>
        </a>
        <a class="n-link" href="{{ route('GetAllMyContact') }}">
            <li class="nav-item  dd1 {{ request()->is('user/mycontacts*') || request()->is('user/mycontacts*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0 0H15.2429C16.2924 0 17.1429 0.898 17.1429 1.99V18.01C17.1429 19.109 16.2924 20 15.2429 20H0V0ZM3.80952 2H1.90476V18H3.80952V2ZM5.71429 18H15.2381V2H5.71429V18ZM7.61905 14C7.61905 13.2044 7.92007 12.4413 8.45588 11.8787C8.9917 11.3161 9.71843 11 10.4762 11C11.234 11 11.9607 11.3161 12.4965 11.8787C13.0323 12.4413 13.3333 13.2044 13.3333 14H7.61905ZM10.4762 10C9.97102 10 9.48653 9.78929 9.12932 9.41421C8.77211 9.03914 8.57143 8.53043 8.57143 8C8.57143 7.46957 8.77211 6.96086 9.12932 6.58579C9.48653 6.21071 9.97102 6 10.4762 6C10.9814 6 11.4658 6.21071 11.8231 6.58579C12.1803 6.96086 12.381 7.46957 12.381 8C12.381 8.53043 12.1803 9.03914 11.8231 9.41421C11.4658 9.78929 10.9814 10 10.4762 10ZM18.0952 4H20V8H18.0952V4ZM18.0952 10H20V14H18.0952V10Z" />
                </svg>
                <span class="nav-link">My Contacts</span>
            </li>
        </a>
        <a class="n-link" href="{{ route('getEvents') }}">
            <li class="nav-item  dd1 {{ request()->is('user/getevents*') || request()->is('user/getevents*') ? 'activenavitem' : '' }}">
                <svg width="15" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16 3.15789V1.05263C16 0.773456 15.8946 0.505715 15.7071 0.308309C15.5196 0.110902 15.2652 0 15 0C14.7348 0 14.4804 0.110902 14.2929 0.308309C14.1054 0.505715 14 0.773456 14 1.05263V3.15789H6V1.05263C6 0.773456 5.89464 0.505715 5.70711 0.308309C5.51957 0.110902 5.26522 0 5 0C4.73478 0 4.48043 0.110902 4.29289 0.308309C4.10536 0.505715 4 0.773456 4 1.05263V3.15789H0V20H20V3.15789H16ZM18 17.8947H2V5.26316H18V17.8947ZM10 10.5263C10.2967 10.5263 10.5867 10.4337 10.8334 10.2602C11.08 10.0867 11.2723 9.84012 11.3858 9.55161C11.4994 9.26309 11.5291 8.94562 11.4712 8.63933C11.4133 8.33305 11.2704 8.0517 11.0607 7.83088C10.8509 7.61006 10.5836 7.45968 10.2926 7.39876C10.0017 7.33784 9.70006 7.3691 9.42597 7.48861C9.15189 7.60812 8.91762 7.8105 8.7528 8.07015C8.58797 8.32981 8.5 8.63508 8.5 8.94737C8.5 9.36613 8.65804 9.76774 8.93934 10.0639C9.22064 10.36 9.60218 10.5263 10 10.5263ZM13 12.8526C12.1145 12.2213 11.0695 11.8839 10 11.8839C8.93049 11.8839 7.88554 12.2213 7 12.8526V14.7368H13V12.8526Z" />
                </svg>

                <span class="nav-link dd">My Appointments</span>
            </li>
        </a>
        </ul>
        <ul class="nav custom-fontsize2 flex-column">
            <a class="n-link" href="#">
            <li class="nav-item dd1">
                <svg width="15" height="13" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.6 12.9L12.2 10.1C12.5677 10.1 12.9318 10.0276 13.2715 9.88686C13.6112 9.74615 13.9199 9.5399 14.1799 9.2799C14.4399 9.0199 14.6461 8.71123 14.7869 8.37151C14.9276 8.0318 15 7.6677 15 7.3V3.8C15 3.4323 14.9276 3.0682 14.7869 2.72849C14.6461 2.38878 14.4399 2.08011 14.1799 1.8201C13.9199 1.5601 13.6112 1.35385 13.2715 1.21314C12.9318 1.07242 12.5677 1 12.2 1H3.8C3.05739 1 2.3452 1.295 1.8201 1.8201C1.295 2.3452 1 3.05739 1 3.8V7.3C1 8.04261 1.295 8.7548 1.8201 9.2799C2.3452 9.805 3.05739 10.1 3.8 10.1H8L13.6 12.9Z"
                        stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M4.65744 6.24995C5.13082 6.24995 5.51458 5.93654 5.51458 5.54994C5.51458 5.16335 5.13082 4.84995 4.65744 4.84995C4.18405 4.84995 3.80029 5.16335 3.80029 5.54994C3.80029 5.93654 4.18405 6.24995 4.65744 6.24995Z"
                        />
                    <path
                        d="M8.15744 6.24995C8.63082 6.24995 9.01458 5.93654 9.01458 5.54994C9.01458 5.16335 8.63082 4.84995 8.15744 4.84995C7.68405 4.84995 7.30029 5.16335 7.30029 5.54994C7.30029 5.93654 7.68405 6.24995 8.15744 6.24995Z"
                         />
                    <path
                        d="M11.6574 6.24995C12.1308 6.24995 12.5146 5.93654 12.5146 5.54994C12.5146 5.16335 12.1308 4.84995 11.6574 4.84995C11.184 4.84995 10.8003 5.16335 10.8003 5.54994C10.8003 5.93654 11.184 6.24995 11.6574 6.24995Z"
                         />
                </svg>

                <span class="nav-link">Support</span>
            </li>
            </a>
            <a class="n-link" href="#">
            <li class="nav-item dd1">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.00029 5.60001C6.58029 5.60001 6.30029 5.88001 6.30029 6.30001V9.80001C6.30029 10.22 6.58029 10.5 7.00029 10.5C7.42029 10.5 7.70029 10.22 7.70029 9.80001V6.30001C7.70029 5.88001 7.42029 5.60001 7.00029 5.60001Z" fill="#495057"/>
                    <path d="M7.00029 4.9C7.38689 4.9 7.70029 4.5866 7.70029 4.2C7.70029 3.8134 7.38689 3.5 7.00029 3.5C6.61369 3.5 6.30029 3.8134 6.30029 4.2C6.30029 4.5866 6.61369 4.9 7.00029 4.9Z" fill="#495057"/>
                    <path d="M7 0C3.15 0 0 3.15 0 7C0 10.85 3.15 14 7 14C10.85 14 14 10.85 14 7C14 3.15 10.85 0 7 0ZM7 12.6C3.92 12.6 1.4 10.08 1.4 7C1.4 3.92 3.92 1.4 7 1.4C10.08 1.4 12.6 3.92 12.6 7C12.6 10.08 10.08 12.6 7 12.6Z" fill="#495057"/>
                    </svg>
                    
                <a class="nav-link">Privacy Policy</a>
            </li>
            </a>
        </ul>
        <div>
           <a href="{{ route('logoutfrontend') }}"> <button class="btn  w-100 custom-color">Logout</button></a>
        </div>
    </div>
	
 

    <!-- Content -->
    <div id="content">
	
	  <nav class="navbar navbar-expand-lg navbar-light bg-light stickey-nav">
            <div class="header">
                <button id="sidebarToggle" class="btn">
                    ☰
                </button>
                <div class="user-info">
                    <a href="{{ route('UpdateProfile') }}">
					 <?php if($user->profile_pic){?>
						<img src="{{ asset('') }}{{ $user->profile_pic }}" alt="{{ $user->name }}" class="rounded-circle" width="50" height="50">
                     <?php }else{?>
						<img src="{{ asset('assets') }}/images/UserImage.png" alt="{{ $user->name }}" class="rounded-circle" width="50" height="50">
					 <?php } ?>
                    </a>
                    <a href="{{ route('UpdateProfile') }}">
					<div class="user-details">
                        <span class="username"><?php echo $user['name']; ?></span>
                        <span class="Designation">@if(@$contacts->type_name) {{ @$contacts->type_name }} @endif</span>
                    </div>
                    </a>
                </div>
               <div class="header-right d-flex align-items-center">
                    <div class=" mx-2">
                        <button class="btn dropdown-toggle contact-Available" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $user['credit_contact']; ?> Contacts Available
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"><?php echo $user['credit_contact']; ?> Contacts Available</a>
							<a class="dropdown-item" href="#" class="buycontct_crdt" data-toggle="modal"
                            data-target="#Buycredit">Buy Contacts</a>
                            <!-- Add more items here as needed -->
                        </div>
                    </div>
                    <div class="clock mx-2 d-flex align-items-center {{ request()->is('user/getclockin*') || request()->is('user/getclockin*') || request()->is('user/gettsheetdetails*') ? 'activeclock' : '' }}">
                       <a href="{{ route('getclockin') }}"> <i class="far fa-clock"></i></a>
                    </div>

                    <div class="bell mx-2 d-flex align-items-center">
                        <!-- Notification Dropdown -->
                        @php
                        use App\Models\Notification;

                            $notificationscount = Notification::where('client_id', $user->id)->where('status', 0)->count();
                            
                            $notifications = Notification::where('client_id', $user->id)
                                ->orderBy('status', 'asc') // Order by status (unread first)
                                ->orderBy('created_at', 'desc') // Then order by created_at (newest first)
                                ->paginate(5);

                        @endphp
                            <div class="dropdown notific">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('assets') }}/images/bell_animated3.gif" width="30">
                                    <span class="badge bg-danger">{{ $notificationscount }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li>
                                        <h6 class="dropdown-header">Notifications</h6>
                                    </li>
                                    @foreach(@$notifications as  $notification)
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="changeStatus({{ $notification->id }}, this)">
                                                <!-- Notification Status Icon (Unread: Bell icon, Read: Check icon) -->
                                                @if($notification->status == 0)
                                                    <i class="bi bi-bell-fill text-warning" data-status="0"></i> <!-- Unread Icon -->
                                                @else
                                                    <i class="bi bi-check-circle-fill text-success" data-status="1"></i> <!-- Read Icon -->
                                                @endif
                                                
                                                <strong>{{ strlen($notification->title) > 40 ? substr($notification->title, 0, 40) . '...' : $notification->title }}</strong><br />
                                                {{ strlen($notification->body) > 100 ? substr($notification->body, 0, 100) . '...' : $notification->body }}

                                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                            </a>
                                        </li>
                                        <div class="dropdown-divider"></div>
                                    @endforeach 
                                    <li>
                                        <a class="dropdown-item text-center" href="{{ route('WebNotification') }}">
                                            View All Notifications
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Include jQuery if not already included -->



  
                    </div>
                </div>
            </div>
        </nav>



        <div class="modal fade" id="Buycredit">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title title-model" id="modalLabel1">Buy Contact
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <h5 class="title-model text-center">
                            You have "<?php if($user['credit_contact']) echo $user['credit_contact']; else echo '0'; ?>" Contact Credits left.</h5>
                        <p class="client-label text-center">Add Contacts</p>
                        <form action="{{ route('Creditcontact') }}" method="post">
                             @csrf 
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="counter-container">
                                        <div class="counter-wrapper">
                                            <div class="counter-btn" id="decrement">-</div>
                                            <div class="spacer"></div>
                                            <div class="counter-btn" id="increment">+</div>
                                        </div>
                                        <div class="counter-display" id="contactCount">2
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center mt-2">
                                   <div id="monthly_chrg">$4 / MONTHLY</div>
                                   <input type="hidden" name="price" value="4" id="plan_prc">
                                   <input type="hidden" name="contact_credit" value="2" id="plan_credit">

                                    <p class="m-0 client-label "><span class="status-unsigned m-0">Note *</span> You’re charged for contact
                                        set up today.</p>
                                    <p class="client-label  m-0"> Monthly charge will begin
                                        once contact accepts
                                        invitation.</p>

                                </div>
                                <div class="col-md-12">
                                    <button type="submit"
                                        class="btn btn-primary text-center add-new-job-btn w-100 my-3">
                                        Buy Contacts Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@yield('content')
		
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/bootstrap-datetimepicker.min.js?v=1.2.1') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets/fullcalendar.js?v=1.2.1') }}"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.js"></script>
    <script src="{{ asset('assets/signature_pad.umd.min.js?v=1.2.1') }}"></script>
    {{-- <script src="{{ asset('assets/app.js?v=1.2.1') }}"></script>  --}}
    
   
   

   

    
	<!-- Bootstrap JS and Popper.js -->
    <!-- Custom JS -->
    <script src="{{ asset('assets/myscript.js?v=1.2.1') }}"></script>

    <!-- Custom JS -->
    
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
        
    </script>
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const closeButton = document.querySelector('.close-btn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        closeButton.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        });
    });
</script>
<script src="{{ asset('assets/intl-tel-input-master/build/js/intlTelInputWithUtils.js?v=1.2.1') }}"></script>

  
  
<script>
    // Simple increment/decrement logic for contacts
    let contactCount = 2;

    document.getElementById('increment').addEventListener('click', function () {
        contactCount++;
        document.getElementById('contactCount').textContent = contactCount;
        
        var totalprc = contactCount*2;
        $('#monthly_chrg').html('<span>$' + totalprc + ' / MONTHLY</span>');
        $('#plan_prc').val(totalprc);
        $('#plan_credit').val(contactCount);

    });

    document.getElementById('decrement').addEventListener('click', function () {
        if (contactCount > 1) {
            contactCount--;
            document.getElementById('contactCount').textContent = contactCount;
            var totalprc = contactCount*2;
            $('#monthly_chrg').html('<span>$' + totalprc + ' / MONTHLY</span>');
            $('#plan_prc').val(totalprc);
            $('#plan_credit').val(contactCount);
        }
    });
</script>

<script>
function changeStatus(notificationId, element) {
    var icon = $(element).find("i");
    var currentStatus = icon.data('status');  // Get current status

    // Toggle status
    var newStatus = currentStatus === 0 ? 1 : 0;

    // Send AJAX request to update the notification status
    $.ajax({
        url: '{{ route('WebNotificationstatus') }}', // Your route to update status
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}", // CSRF token for security
            id: notificationId,
            status: newStatus
        },
        success: function(response) {
            // Check if the status was updated successfully
            if (response.success) {
                // Update the icon based on new status
                if (newStatus === 0) {
                    icon.removeClass('text-success bi-check-circle-fill')
                        .addClass('text-warning bi-bell-fill');
                } else {
                    icon.removeClass('text-warning bi-bell-fill')
                        .addClass('text-success bi-check-circle-fill');
                }
                icon.data('status', newStatus);  // Update the status data attribute
            } else {
                alert('Failed to update notification status.');
            }
        },
        error: function() {
            alert('An error occurred while updating the status.');
        }
    });
}

function closeModal() {
    // Find all open modals and hide them
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });
}
</script>

@yield('script')
</body>

</html>