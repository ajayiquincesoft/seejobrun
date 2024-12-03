<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('useraccount-delete', [App\Http\Controllers\PrivacypolicyController::class, 'useraccountdelete'])->name('account_delete');
Route::post('useraccount-delete', [App\Http\Controllers\PrivacypolicyController::class, 'useraccountMessage'])->name('accountdelete');
Route::get('privacy-policy', [App\Http\Controllers\PrivacypolicyController::class, 'privacypolicy'])->name('privacypolicy');
Route::get('add-contact-subs', [App\Http\Controllers\PrivacypolicyController::class, 'addcontactsubs'])->name('addcontactsubs');
Route::post('add-contact-subscription', [App\Http\Controllers\PrivacypolicyController::class, 'addcontactsubscription'])->name('addcontactsubscription');
Route::get('accept-contact-invitation', [App\Http\Controllers\ApiController::class, 'AcceptContactInvitations']);
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => true, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::get('/verify-email/{id}', [App\Http\Controllers\ApiController::class, 'verifyEmail']);

Route::get('forget-password', [App\Http\Controllers\ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');

Route::post('forget-password', [App\Http\Controllers\ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 

Route::get('reset-password/{token}', [App\Http\Controllers\ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [App\Http\Controllers\ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::namespace('Admin')->prefix('Admin')->middleware(['auth','admin'])->group(function () 
{
	Route::resource('user', 'UserController');

    Route::post('logout', [App\Http\Controllers\Admin\UserController::class, 'signOut'])->name('logout');

    Route::match(['get', 'put'], 'profile', 'UserController@profile')->name('admin.profile');
    Route::match(['get', 'put'], 'change-password', 'UserController@updatePassword')->name('admin.password');

    Route::resource('contact', 'ContactController');

    /*Route::get('job', [App\Http\Controllers\Admin\JobController::class,'index'])->name('search');*/

    Route::resource('job', 'JobController');

    Route::resource('template', 'TemplateController');
});


// Web routes
Route::get('/user-login', 'User\UserController@getUserLogin')->name('user-login');
Route::post('/user-login', 'User\UserController@postUserLogin')->name('userlogin');
Route::get('/userlogout', 'User\UserController@logout')->name('logoutfrontend');
Route::get('/register', 'User\UserController@register')->name('register');
Route::post('/register', 'User\UserController@Postregister')->name('postregistration');
Route::get('/registerotpauths', 'User\UserController@otpauthregister')->name('otpAuthregister');
Route::post('/verifyregisterotp', 'User\UserController@verifyRegisterOtp')->name('Verifyregisterotp');

Route::namespace('User')->prefix('user')->middleware(['auth', 'user'])->group(function () 
{
     Route::get('/dashboard', 'UserController@dashboard')->name('user.dashboard');
	 Route::get('/jobs', 'UserController@jobs')->name('user.jobs');
	 Route::post('/addjob', 'UserController@addjob')->name('user.addjob');
     Route::post('/deletejob', 'UserController@deleteJob')->name('deletejob');

	 Route::get('/getsinglecontact', 'UserController@GetSingleContact')->name('getsinglecontact'); 
	 Route::post('/add_contacts', 'UserController@addContact')->name('addContact');
	 //Route::post('/add_contacts', 'UserController@addContact')->name('addContact');
	 Route::get('/job/{id}', 'UserController@getJob')->name('getJob');
     Route::post('/update_job_general', 'UserController@updateJobGeneral')->name('updateJobGeneral');
     // job tasks
     Route::post('/add_taskassignment', 'UserController@AddTaskAssignment')->name('addTaskAssingment');
     Route::post('/approve_singletask', 'UserController@approvedSingleTask')->name('aprovesingletask');
     Route::post('/delete_taskassignment', 'UserController@delete_taskassignment')->name('deletesingletask');
     Route::post('/delete_taskassignment_attachement', 'UserController@delete_taskassignmentattachement')->name('deletetaskassignmentattachement');
     Route::post('/update_single_taskassignment', 'UserController@updateSingletaskAssignment')->name('updateSingleTaskAssignment');
     Route::post('/get_tasks_and_punchlist_by_date', 'UserController@GetTasksAndPunchlistBydate')->name('gettasksandpunchlistbydate');
    
     //Final PunchList 
     Route::post('/add_fpunchlist', 'UserController@AdddfPunchlist')->name('addfinalpunchlist');
     Route::post('/update_fpunchlist', 'UserController@updatefPunchlist')->name('updatefinalpunchlist');
     Route::post('/update_single_punchlist', 'UserController@updateSinglePunchlist')->name('updatesinglefinalpunchlist');
     Route::post('/delete_single_punchlist_attachment', 'UserController@deleteSinglePunchlistAttachment')->name('deletesinglepunchlistAttachment');
     Route::post('/delete_single_punchlist', 'UserController@deleteSinglePunchlist')->name('deletesinglepunchlist');
     Route::post('/update_allpunchlists', 'UserController@updateAllPunchlist')->name('updateallPunchlist');

     // Job stages
     Route::post('/add_jobstage', 'UserController@AddJobStage')->name('addJobStage');
     Route::post('/get_jobstage_by_id', 'UserController@GetJobStageByid')->name('getjobstageByid');
     Route::post('/update_stage', 'UserController@updateStage')->name('updateStage');
     Route::post('/stageorder', 'UserController@stageorder')->name('stageorder');
     Route::post('/deletestage', 'UserController@deleteStage')->name('deleteStage');

     //Job Documents
     Route::post('/addjobdocument', 'UserController@addJobDocument')->name('addjobdocument');
     Route::post('/deletejobattachment', 'UserController@deleteJobAttachment')->name('deletejobattachment');

    // Job Pictures
    Route::post('/addjobpictures', 'UserController@addJobPicture')->name('addjobpicture');
    Route::post('/deletejobpicture', 'UserController@deleteJobpicture')->name('deletejobpictures');

    //Job contact
    Route::post('/updatecontactsharedpermission', 'UserController@UpdateContactsharedPermission')->name('updatecontactsharedpermission');
    Route::post('/addjobcontactsbyjobid', 'UserController@addJobContactsbyJobId')->name('addjobcontactsbyjobid');
    Route::post('/deletejobcontact', 'UserController@deleteJobContact')->name('deletejobcontact');
    
    
    // My daily Tasks
    Route::get('/mydailytask', 'UserController@myDailyTasks')->name('MyDailyTasks');
    Route::post('/gettaskbydate', 'UserController@gettaskbydate')->name('gettaskBydate');
    Route::post('/show_and_hide_task', 'UserController@showAndHideTask')->name('ShowAndHideTask');
    Route::get('/hiddentasks', 'UserController@HiddenTasks')->name('HiddenTasks');


    // My Contact
    Route::get('/mycontacts', 'UserController@GetAllMyContact')->name('GetAllMyContact');
    Route::post('/mycontacts', 'UserController@GetAllMyContact')->name('GetAllMyContactfilter');
    Route::post('/deletecontact', 'UserController@DeleteContact')->name('DeleteContact');
    Route::post('/updatecontact', 'UserController@updateContact')->name('UpdateContact');
    Route::post('/archiveorunarchivecontact', 'UserController@ArchiveOrUnArchiveContact')->name('ArchOrUnArchContact');
    Route::post('/creditcontact', 'UserController@CreditContact')->name('Creditcontact');
    Route::get('/buycreditcontact', 'UserController@Buycreditcontact')->name('BuyCreditcontact');
    Route::post('/buycreditcontacts', 'UserController@Buycreditcontacts')->name('BuyCreditcontacts');

    // Change Order 
    Route::get('/changeorders', 'UserController@changeOrders')->name('changeorders');
    Route::get('/getchangeorderlistbyjobid', 'UserController@getchangeOrderListByJobid')->name('getChangeOrderListByJobid');
    Route::post('/singlechangeorderdetails', 'UserController@singleChangeOrderdetails')->name('SingleChangeOrderdetails');
    Route::post('/updatechangeorder', 'UserController@UpdateChangeOrder')->name('UpdatechangeOrder');
    Route::post('/addchangeorder', 'UserController@addChangeOrder')->name('AddchangeOrder');

    // Appointment OR Event
    Route::post('/addevent', 'UserController@AddEvent')->name('addevent');
    Route::get('/getevents', 'UserController@GetEvents')->name('getEvents');
    Route::post('/get_events_by_date', 'UserController@GetEventsBydate')->name('geteventsbydate');
    Route::post('/edit_event', 'UserController@editevent')->name('EditEvent');
    Route::post('/deleteevent', 'UserController@deleteEvent')->name('DeleteEvent');

    //General TODO section LIST

    Route::get('/gettodolist', 'UserController@GetTodoList')->name('getTodolist');
    Route::post('/addgeneraltodosection', 'UserController@AddGenTodoSection')->name('AddGenTodoSection');
    Route::post('/updategeneraltodosection', 'UserController@UpdateToDoGeneralSection')->name('UpdateTodogeneralsection');
    Route::post('/deletegeneraltodosection', 'UserController@DeleteToDoGeneralSection')->name('DeleteTodogeneralsection');


    //General Todo tasks
    Route::post('/addgentodotask', 'UserController@Addgentodotask')->name('AddGenToDotask');
    Route::post('/getgentodosectionwithtask', 'UserController@Getgentodosectionwithtask')->name('getgeneraltodosectask');
    Route::post('/updategeneraltodotaskstatus', 'UserController@updategentodoTaskStatus')->name('updategenetodotaskstatus');
    Route::post('/deletegeneraltodotask', 'UserController@deletegentodoTask')->name('deletegenetodotask');
    Route::post('/updategeneraltodotask', 'UserController@updategentodoTask')->name('updategenetodotask');

    //TimeCard 
     Route::get('/gettimecard', 'UserController@GetTimeCard')->name('gettimecard');
     Route::get('/getdetailtimecard/{id}', 'UserController@getemployeetimecard')->name('getEmployeeTimeCard');
     Route::post('/gettimecards', 'UserController@GetTimeCardsInfo')->name('getTimeCardsInfo');
     Route::get('/sinlgetimecarddetails', 'UserController@Singletimecarddetails')->name('singletimecarddetails');
     Route::post('/editclockinclockout', 'UserController@EditClockinClockout')->name('editClockinClockouts');

     // Clock In module

     Route::get('/getclockin', 'UserController@Getclockin')->name('getclockin');
     //Route::post('/getclockstatus', 'UserController@GetTimesheetClockinClockout')->name('getTimeSheetClockInClockOut');
     Route::post('/getallclocks', 'UserController@GetAllClocks')->name('getAllClocks');
     Route::get('/gettsheetdetails', 'UserController@gettimesheetdetails')->name('GettimeSheetDetails');
     Route::post('/addclockin', 'UserController@addClockin')->name('Addclockins');
     Route::post('/updateclockout', 'UserController@updateClockout')->name('UpdateClockout');

    // User Profile Edit 
    Route::get('/updateprofile', 'UserController@Updateprofile')->name('UpdateProfile');
    Route::post('/postupdateprofile', 'UserController@PostUpdateprofile')->name('PostUpdateProfile');
    Route::get('/privacypolicy', 'UserController@PrivacyPolicy')->name('PrivacyPolicy');

    // Leads Routes

    Route::get('/leads', 'UserController@leads')->name('leads');
    Route::post('/addlead', 'UserController@addLead')->name('AddLead');
    Route::get('/leaddetails/{id}', 'UserController@leadDetails')->name('LeadDetails');
    Route::post('/updatelead', 'UserController@updateLead')->name('UpdateLead');
    Route::post('/convertleadtojob', 'UserController@convertleadtojob')->name('ConvertLeadToJob');
    Route::post('/deletelead', 'UserController@deleteLead')->name('DeleteLead');

    //Lead TO DO SECTION
    Route::post('/addtodosection', 'UserController@AddTodosection')->name('AddToDoSection');
    Route::post('/updatetodosection', 'UserController@updateTodosection')->name('UpdateToDoSection');
    Route::post('/deletetodosection', 'UserController@deleteTodosection')->name('DeleteToDoSection');

    //Lead To Do Section Task

    Route::post('/addtodosectiontask', 'UserController@AddtodoSectiontask')->name('AddToDoSectionTask');
    Route::post('/updatetodosectiontask', 'UserController@updatetodoSectiontask')->name('updateToDoSectionTask');  
    Route::post('/updatetodotaskstatus', 'UserController@updatetodoTaskStatus')->name('updatetodotaskstatus');  
    Route::post('/deletetodotask', 'UserController@deletetodoTask')->name('deletetodotask'); 
    
    Route::post('/webfcmtoken', 'UserController@webFcmToken')->name('WebFcmToken'); 

    // Notifications
    Route::get('/webnotification', 'UserController@webNotification')->name('WebNotification');
    Route::post('/webnotificationstatus', 'UserController@webNotificationstsus')->name('WebNotificationstatus');

    Route::get('/plans', 'UserController@plans')->name('Plans');

    Route::post('/purchaseplan', 'UserController@purchasePlan')->name('PurchasePlan');
    Route::post('/cancelstripeplan', 'UserController@Cancelstripeplan')->name('CancelStripePlan');

    // Other user routes

});
