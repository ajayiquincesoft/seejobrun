<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SelectedPlan;
use App\Models\Payment;
use Session;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //dd('yes');
        //
       // $data = User::orderBy('id','DESC')->where('user_type','!=',1)->paginate(10);
	   $search = $request->get('search');
		$query = User::orderBy('id', 'DESC')->where('user_type', '!=', 1);

		if ($search) {
			$query->where('name', 'like', '%' . $search . '%');
		}

		$data = $query->paginate(10);
        return view('admin/user/index',['data'=>$data]);
    }

    public function signOut() 
    {
      Session::flush();
      Auth::logout();

      return Redirect('login');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $slectedPlans = SelectedPlan::with(['plan'])->where('user_id', '=',$id)->first();

        $userpayment = Payment::where('user_id','=',$id)->get();

        //dd($userpayment);
        return view('admin/user/show',['data'=>$slectedPlans,'payment_history'=>$userpayment]);
    }

    public function edit($id)
    {
        $data = User::find($id);

        $data->getMetas();

        //dd($data);
    
        return view('admin/user/edit',['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $type=$request->user_type;
        //
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'user_status'=>'required',
        ]);

        try
        {
            DB::beginTransaction();

            $data = User::findOrFail($id);
            $data->name = $request->name;
            $data->email = $request->email;
            $data->status = $request->user_status;
            
            if (!Hash::check($request->get('password'), $data->password) && $request->get('password') !='') 
            {
                $data->password = Hash::make($request->get('password'));     
            }

            $data->save();

            $user_save = $data->updateMeta([
                'Address' => $request->Address,
                'Mobile_no' => $request->Mobile_no,
                'State' => $request->State,
                'Country' => $request->Country,
            ]);

            DB::commit();
            return redirect()->route('user.index')->withSuccess('User updated successfully');
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return back()->withErrors($e->getMessage())->withInput($request->all());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->withSuccess('User deleted successfully');
    }

    public function profile(Request $request)
    {
        $input = $request->all();
        if($input)
        {
            $validatedData = $request->validate([
                'name'  =>'required',
                'email'  =>'required|email'
            ]);
            try
            {
                $user = User::findOrFail(Auth::User()->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->save();

                return back()->with('success','Profile updated successfully');
            }
            catch (\Exception $e)
            {
                return back()->withErrors($e->getMessage());
            }
        }

        return view('admin/profile');
    }

    public function updatePassword(Request $request)
    {
        $input = $request->all();
        if($input) 
        {
            $validatedData = $request->validate([
                'currentPassword' => 'required',
                'newPassword' => 'required|min:8',
                'confirmPassword' => 'required|same:newPassword',
            ]);
            try 
            {
                $user = User::find(Auth::User()->id);

                if (Hash::check($request->get('currentPassword'), $user->password)) 
                {
                    $user->password = Hash::make($request->get('newPassword'));
                    $user->save();

                    return back()->with('success', 'Admin Password change successfully');
                }
                return back()->withErrors('Old password not match')->withInput($request->all());
            } 
            catch (Exception $e) 
            {
                return back()->withErrors($e->getMessage())->withInput($request->all());
            }
        }

        return view('admin/change-password');
    }
}
