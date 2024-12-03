<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [ApiController::class, 'register']);
Route::post('register_new', [ApiController::class, 'registerNew']);
Route::post('registerotpauth', [ApiController::class, 'RegisterOtpAuth']);
Route::post('login', [ApiController::class, 'login']);
Route::post('forgot-password', [ApiController::class, 'forgotPassword']);
Route::post('verify-code', [ApiController::class, 'verify']);
Route::post('get_plan',[ApiController::class, 'getPlan']);


Route::group(['middleware' => 'jwt.verify'], function () 
{
   // Route::group(['middleware' => 'user.plan'], function () 
   // {
        Route::post('add_stage', [ApiController::class, 'addStage']);
        Route::post('update_stage', [ApiController::class, 'updateStage']);
        Route::post('delete_stage', [ApiController::class, 'deleteStage']);
        Route::post('get_stage', [ApiController::class, 'getStage']);

        Route::post('add_media', [ApiController::class, 'addMedia']);
        Route::post('get_media', [ApiController::class, 'getMedia']); 
		Route::post('get_media_by_job_id', [ApiController::class, 'getMediaByJobId']);
        Route::post('delete_media', [ApiController::class, 'deleteMedia']);

        Route::post('add_contacts', [ApiController::class, 'addContact']);
		Route::post('add_contacts_new', [ApiController::class, 'addContactNew']);
        Route::post('get_allcontacts', [ApiController::class, 'getallContact']);
		Route::post('get_alltypecontacts', [ApiController::class, 'getAllTypeContact']);
        Route::post('get_contacts', [ApiController::class, 'getContact']);
		Route::post('delete_contact', [ApiController::class, 'deleteContact']);
		Route::post('update_contact_profile', [ApiController::class, 'UpdateContactProfile']);
		Route::post('update_contactshared_permission', [ApiController::class, 'UpdateContactSharedPermission']);

        
		Route::post('add_halfjob', [ApiController::class, 'addHalfJob']);
        Route::post('update_gateno', [ApiController::class, 'update_gateno']);
        Route::post('get_job', [ApiController::class, 'getJob']);
		Route::post('add_jobstage', [ApiController::class, 'addJobStage']);
		Route::post('add_jobstage_by_jobid', [ApiController::class, 'addJobStageByJobId']);
		Route::post('add_jobdocument_by_jobid', [ApiController::class, 'addJobDocumentByJobId']);
		Route::post('add_jobpicture_by_jobid', [ApiController::class, 'addJobPictureByJobId']);
		Route::post('add_jobcontacts_by_jobid', [ApiController::class, 'addJobContactsByJobId']);
		Route::post('add_jobinpection_by_jobid', [ApiController::class, 'addJobInpectionByJobId']);
		
		Route::post('delete_jobstage', [ApiController::class, 'deleteJobStage']);
		Route::post('add_jobattachment', [ApiController::class, 'addJobAttachment']);
		Route::post('delete_jobattachment', [ApiController::class, 'deleteJobAttachment']);
		Route::post('delete_job', [ApiController::class, 'deleteJob']);
		Route::post('archive_job', [ApiController::class, 'archive_job']); 
		Route::post('get_archive_job', [ApiController::class, 'getArchiveJob']); 
		
    //});

	//testing API

	Route::post('testdashboard', [ApiController::class, 'testdashboard']);
	Route::post('testget_job', [ApiController::class, 'testgetJob']);
	Route::post('testupdate_gateno', [ApiController::class, 'testupdate_gateno']);

	// Leads module API

	Route::post('add_lead', [ApiController::class, 'AddLead']);
	Route::post('get_leads', [ApiController::class, 'getLeads']);
	Route::post('update_lead', [ApiController::class, 'updateLead']);
	Route::post('delete_lead', [ApiController::class, 'deleteLead']);
	Route::post('get_lead_by_id', [ApiController::class, 'getleadbyId']);
	Route::post('convert_lead_to_job', [ApiController::class, 'convertLeadtoJob']);

	// End Leads module API

	// Start TODO TASK Module

	Route::post('add_todosection', [ApiController::class, 'Addtodosection']);
	Route::post('get_todosection', [ApiController::class, 'Gettodosection']);
	Route::post('update_todosection', [ApiController::class, 'updatetodosection']);
	Route::post('delete_todosection', [ApiController::class, 'deletetodosection']);

	//to do section task API
	Route::post('add_todosection_task', [ApiController::class, 'AddtodoSectionTask']);
	Route::post('delete_todosection_task', [ApiController::class, 'deletetodoSectionTask']);
	Route::post('update_todosection_task', [ApiController::class, 'updatetodoSectionTask']);
	Route::post('get_todosection_with_tasks', [ApiController::class, 'GettodoSectionWithTask']);
	Route::post('update_todosection_task_status', [ApiController::class, 'update_todosection_task_status']);


	// End TODO TASK Module
	
	//Start Job TODoSection Module

	Route::post('add_todosection_job', [ApiController::class, 'AddtodosectionJob']);
	Route::post('get_todosection_job', [ApiController::class, 'GettodosectionJob']);
	// update_todosection,deletetodosection will work existing API
	

	//End TODO Section Module

	Route::post('select_plan',[ApiController::class, 'selectPlan']);
	Route::post('select_plan_test',[ApiController::class, 'selectPlanTest']);
    Route::post('logout', [ApiController::class, 'logout']);
    Route::post('showuser', [ApiController::class, 'showUser']);
    //Route::post('get_user', [ApiController::class, 'get_user']);

    Route::post('pay', [ApiController::class, 'makePayment']);
    Route::post('ios-pay', [ApiController::class, 'makePaymentForIos']);
	Route::post('android-pay', [ApiController::class, 'makePaymentForAndroid']);
	Route::post('ios-subscription-restore', [ApiController::class, 'IosSubscriptionRestore']);
	//Route::post('unsubscribe_subscription', [ApiController::class, 'unsubscribeSubscription']);
	
    Route::post('user_meta_save', [ApiController::class, 'user_meta_save']);

    Route::post('add_template', [ApiController::class, 'addTemplate']);
    Route::post('get_template', [ApiController::class, 'getTemplate']);
    Route::post('delete_template', [ApiController::class, 'deleteTemplate']);
	
	//task assignment
	
	Route::post('add_taskassignment', [ApiController::class, 'addtaskassignment']);
	Route::post('approve_singletask', [ApiController::class, 'updateTaskAssignment']);
	Route::post('get_taskassignment', [ApiController::class, 'getTaskAssignment']);
	Route::post('update_single_taskassignment', [ApiController::class, 'updateSingletaskAssignment']);
	Route::post('delete_taskassignment', [ApiController::class, 'deleteTaskassignment']);
	Route::post('delete_taskassignment_attachement', [ApiController::class, 'deleteTaskassignmentAttachment']);
	Route::post('get_taskassignment_attachment_by_id', [ApiController::class, 'getTaskassignmentAttachmentById']);
	Route::post('add_taskassignment_attachment', [ApiController::class, 'addtaskassignmentAttachment']);
	Route::post('getcontactsharedbyjob', [ApiController::class, 'getContactSharedByJobId']);
	Route::post('getgeneralshared', [ApiController::class, 'getGeneralShared']);
	Route::post('addcontactshared', [ApiController::class, 'AddContactShared']);
	
	//punchlist
    Route::post('add_punchlist', [ApiController::class, 'addPunchlist']);
    Route::post('update_allpunchlist', [ApiController::class, 'updateAllPunchlist']);
    Route::post('update_punchlist', [ApiController::class, 'updatePunchlist']);
    Route::post('get_approvepunchlist', [ApiController::class, 'getapprovePunchlist']);
    Route::post('get_punchlist', [ApiController::class, 'getPunchlist']);
    Route::post('delete_punchlist', [ApiController::class, 'deletePunchlist']);
	Route::post('delete_punchlist_attachement', [ApiController::class, 'deletePunchlistAttachment']);
	Route::post('get_punchlist_attachment_by_id', [ApiController::class, 'getPunchlistAttachmentById']);
	Route::post('add_punchlist_attachment', [ApiController::class, 'addPunchlistAttachment']);

    Route::post('dashboard', [ApiController::class, 'dashboard']);
    Route::post('change_order', [ApiController::class, 'changeorder']);
    Route::post('get_changeorder', [ApiController::class, 'getChangeorder']);
    Route::post('update_changeorder', [ApiController::class, 'updateChangeorder']);
    Route::post('get_user', [ApiController::class, 'getUser']);
    Route::post('update_clocktime', [ApiController::class, 'updateClocktime']);
	Route::post('edit_clockinclockout', [ApiController::class, 'edit_ClockinClockout']);
    Route::post('user_timesheet', [ApiController::class, 'getusertimesheet']);
    Route::post('get_employee', [ApiController::class, 'getEmployee']);
    Route::post('timecard_details', [ApiController::class, 'timecard_details']);
    Route::post('get_employeejob', [ApiController::class, 'get_employeejob']);
    Route::post('getjobtimecard', [ApiController::class, 'getjobtimecard']);
    Route::post('getclockdetails', [ApiController::class, 'clockdetails']);
	Route::post('update_profile', [ApiController::class, 'updateProfile']);
	Route::post('jobcalender', [ApiController::class, 'jobcalender']);
	Route::post('jobcalender_new', [ApiController::class, 'jobcalendernew']);
	Route::post('addevent', [ApiController::class, 'addEvent']);
	Route::post('eventedit', [ApiController::class, 'EditEvent']);
	Route::post('getevents', [ApiController::class, 'getEvents']);
	Route::post('delete_event', [ApiController::class, 'DeleteEvent']);
	Route::post('getevent_notification', [ApiController::class, 'GetEventNotification']);
	Route::post('get_timecard', [ApiController::class, 'getTimeCard']);
	Route::post('get_timecard_details', [ApiController::class, 'getTimecardDetails']);
	Route::post('get_clockstatus', [ApiController::class, 'getTimesheetClockinClockout']);
	Route::post('add_clockin', [ApiController::class, 'addTimesheetClockin']);
	Route::post('update_clockout', [ApiController::class, 'updateTimesheetClockout']);
	Route::post('get_allclocks', [ApiController::class, 'get_allClocks']);
	Route::post('get_tsheet_details', [ApiController::class, 'getTsheetDetails']);
	Route::post('update_single_jobnotepad', [ApiController::class, 'updateSingleJobNotePad']);
	Route::post('change_orderlist', [ApiController::class, 'changeorderList']);
	Route::post('add_singlecontact', [ApiController::class, 'addSingleContact']);
	Route::post('delete_singlecontact', [ApiController::class, 'deleteSingleContact']);
	Route::post('update_singlecontact', [ApiController::class, 'updateSingleContact']);
	Route::post('changeorder_details', [ApiController::class, 'changeorderDetails']);
	Route::post('addnewchangeorder', [ApiController::class, 'addNewChangeOrder']);
	Route::post('save-fcm-token', [ApiController::class, 'savefcmToken']);
	Route::post('save-notifications', [ApiController::class, 'saveNotifications']);
	Route::post('read-notifications', [ApiController::class, 'readNotificationStatus']);
	Route::get('get-unread-notifications', [ApiController::class, 'getUnreadNotifications']);
	
	Route::get('get-all-notifications', [ApiController::class, 'getAllNotifications']);
	Route::post('get-all-jobsforclock', [ApiController::class, 'getAllJobsForClock']);
	Route::post('check-job-permission', [ApiController::class, 'checkJobpermission']);

    Route::post('blank-template', [ApiController::class, 'blanktemplate']);
    Route::post('stage-template', [ApiController::class, 'stagetemplate']);
    Route::post('delete_temp_stage', [ApiController::class, 'deletestagetemplate']);
    Route::post('add_job2', [ApiController::class, 'addJob2']);
    Route::post('template_add_job', [ApiController::class, 'templateaddjob']); 
    Route::post('stage_order', [ApiController::class, 'stageorder']); 
	
	//******************** upgrade API ******************************
	
	Route::post('archiveorunarchivecontacts', [ApiController::class, 'archive_Or_unarcive_contacts']);
	Route::post('allmydailytask', [ApiController::class, 'all_my_daily_task']);
	Route::post('allmydailytask_new', [ApiController::class, 'all_my_daily_task_new']); 
	Route::post('buycontactcredits', [ApiController::class, 'Addbuycontactcredits']); 
	Route::post('show_and_hide_task', [ApiController::class, 'ShowAndHidetask']);
	Route::post('pending-contact', [ApiController::class, 'PendingContact']); 
	Route::post('accept-contact-invitation-new', [ApiController::class, 'AcceptContactInvitationsnew']); 


	// Notification testing

	Route::post('notification_test', [ApiController::class, 'Notificationtest']); 

	// General TO DO sections
	
	Route::post('add_generaltodosection', [ApiController::class, 'Addgeneraltodosection']);
	Route::post('get_generaltodosection', [ApiController::class, 'Getgeneraltodosection']);
	Route::post('update_generaltodosection', [ApiController::class, 'updategeneraltodosection']);
	Route::post('delete_generaltodosection', [ApiController::class, 'deletegeneraltodosection']);

	// General to do section tasks
	Route::post('add_general_todo_task', [ApiController::class, 'AddGeneraltodoTask']);
	Route::post('delete_general_todo_task', [ApiController::class, 'deletegeneraltodoTask']);
	Route::post('update_generaltodo_task', [ApiController::class, 'updategeneraltodoTask']);
	Route::post('get_generaltodosections_with_tasks', [ApiController::class, 'GetgeneraltodoWithTask']);
	Route::post('update_generaltodo_task_status', [ApiController::class, 'update_generaltodo_task_status']);
	
	
	
	
// Route::post('update_todosection_task_status', [ApiController::class, 'update_todosection_task_status']);



});


