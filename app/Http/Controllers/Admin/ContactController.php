<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $data = Contact::orderBy('id','DESC')->where('status','=',1)->paginate(10);
        return view('admin/contact/index',['data'=>$data]);
    }

    public function show($id)
    {
        $data = Contact::find($id);

        return view('admin/contact/show',['data'=>$data]);
    }
}
