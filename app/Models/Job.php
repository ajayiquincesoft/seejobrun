<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function jobstage()
    {
        return $this->hasMany(Jobstage::class,'job_id','id')->orderby('stage_order','asc');
    }

    public function jobmedia()
    {
        return $this->hasMany(Jobmedia::class,'job_id','id');
    }

    public function jobcontact()
    {
        return $this->hasMany(Jobcontacts::class,'job_id','id');
    }

    public function jobinspection()
    {
        return $this->hasMany(Jobinspection::class,'job_id','id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class,'client_id','id');
    }

    public function punchlist()
    { 
        return $this->hasMany(Punchlist::class,'job_id','id');
    } 
	
	public function taskassignment()
    { 
        return $this->hasMany(Taskassignment::class,'job_id','id');
    }

    public function changeorder()
    { 
        return $this->hasMany(changeorder::class,'job_id','id');
    }
}
