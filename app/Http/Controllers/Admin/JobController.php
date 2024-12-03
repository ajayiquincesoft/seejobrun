<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\User;
use App\Models\Contact;
use App\Models\Media;
use App\Models\Stage;
use App\Models\Jobcontacts;
use App\Models\Jobmedia;
use App\Models\Jobstage;

class JobController extends Controller
{
    public function index(Request $request)
    {
        //$data = Job::with(['user'])->orderBy('id','DESC')->where('status','=',1)->paginate(10);

        $search = $request->search_name;
    $jobtype = $request->search_jobtype;
    $select_user = $request->search_user;

    $query = Job::query();

    if (!empty($search)) {
        $query = $query->where('name', 'LIKE', '%' . $search . '%')
                       ->orWhere('mobile', '=', $search);
    }

    if (!empty($jobtype)) {
        $query = $query->where('job_type', '=', $jobtype);
    }

    if (!empty($select_user)) {
        $query = $query->where('user_id', '=', $select_user);
    }

    // Set the number of items per page
    $perPage = 10;

    // Fetch paginated results
    $data = $query->with('user')->orderBy('id', 'DESC')->paginate($perPage);

    $user = User::where('user_type', '!=', 1)->get();

		return view('admin/job/index', ['data' => $data, 'user' => $user]);
        
    }

    public function show($id)
    {
        $jobdata = Job::with(['user', 'jobstage','jobmedia','jobcontact','jobstage.stage','jobmedia.media','jobcontact.contact','contact','punchlist','punchlist.punchlistimg','punchlist.punchcontact'])->find($id);

        //dd($jobdata);

        return view('admin/job/show',['data'=>$jobdata]);
    }
}
