<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    public function index()
    {
        $data = Template::orderBy('id','DESC')->paginate(10);
        return view('admin/template/index',['data'=>$data]);
    }

    public function create()
    {
        $emailType=array('1'=>'Contact Success','2'=>'Email Verify','3'=>'Confirmation Code','4'=>'Forget Password',
		'5'=>'Add Job Email','6'=>'Job injured','7'=>'Appointment Notification','8'=>'Add Contact to Job',
		'9'=>'Add Contact Invitation','10'=>'Add Contact Invitation for not registered user',
	'11'=>'Signup Email Verify','12'=>'Add Contact Invitation for registered user new version','13'=>'Add Contact Invitation for not registered user new version');
        return view('admin/template/create',['type'=>$emailType]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'content' => 'required',            
            'status'=>'required'
        ]);

        $data = Template::make();
        $data->type = $request->type;           
        $data->content = $request->content;            
        $data->status = $request->status;           
        $data->save();
       
        return redirect()->route('template.index')->withSuccess('Template created successfully');
    }
    
    public function edit($id)
    {
        $data = Template::find($id);
        $emailType=array('1'=>'Contact Success','2'=>'Email Verify','3'=>'Confirmation Code','4'=>'Forget Password','5'=>'Add Job Email','6'=>'Job injured','7'=>'Appointment Notification','8'=>'Add Contact to Job','9'=>'Add Contact Invitation','10'=>'Add Contact Invitation for not registered user','11'=>'Signup Email Verify','12'=>'Add Contact Invitation for registered user new version','13'=>'Add Contact Invitation for not registered user new version');
        return view('admin/template/edit',['data'=>$data,'type'=>$emailType]);
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type' => 'required',
            'content' => 'required',            
            'status'=>'required'
        ]);

        $data = Template::findOrFail($id);
        $data->type = $request->type;        
        $data->content = $request->content;        
        $data->status = $request->status;
        $data->update();

        return redirect()->route('template.index')->withSuccess('Template updated successfully');
    }
    
    public function destroy($id)
    {
        $data = Template::findOrFail($id);
        $data->delete();

        return back()->withSuccess('Page deleted successfully');
    }
}
