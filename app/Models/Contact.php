<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $appends = ['type_name'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function job()
    {
        return $this->hasOne(Job::class,'client_id','id');
    }

    public function jobcontact()
    {
        return $this->hasMany(Jobcontacts::class,'contact_id','id');
    }

    public function jobinspection()
    {
        return $this->hasMany(Jobinspection::class,'contact_id','id');
    }

    public function contactshared()
    {
        return $this->hasOne(Contactshared::class,'contact_id','id')->withDefault(function () {
            return new contactshared();
        });
    }
	 public function contactshared1()
    {
		return $this->hasMany(Contactshared::class,'contact_id');
    }
    public function getTypeNameAttribute()
    {
        $value = contact::where('id','=',$this->id)->first()->type;

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
	 public function addedByUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the contact user
    public function contactUser()
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }
}
