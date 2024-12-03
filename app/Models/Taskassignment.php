<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taskassignment extends Model
{
    use HasFactory;
	 protected $appends = ['contact_name','type_name'];
	
	 public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class,'client_id','id');
    }
	 public function taskassignmentimages()
    {
    	return $this->hasMany(Taskassignmentimages::class,'taskassignment_id','id');
    }
	 public function getContactNameAttribute()
	{
		return Contact::where('id','=',$this->contact_id)->first()->name;
	}
    public function getTypeNameAttribute()
    {
        $value = contact::where('id','=',$this->contact_id)->first()->type;

        if($value == "1")
        {
        	return "Client";
        }
        else if($value == "2")
        {
        	return "Sub-Contractor";
        }
        else if($value == "3")
        {
        	return "Employee";
        }
        else if($value == "4")
        {
        	return "General-Contractor";
        }
        else if($value == "5")
        {
        	return "Architect/Engineer";
        }
        else if($value == "6")
        {
        	return "Interior-Designer";
        }
        else if($value == "7")
        {
        	return "Inspector";
        }
        else if($value == "8")
        {
        	return "Bookkeeper";
        }
    }
}
