<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobcontacts extends Model
{
    use HasFactory;

    protected $appends = ['job_name'];

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class,'contact_id');
    }

    public function getJobNameAttribute()
    {
        return Job::where('id','=',$this->job_id)->first()->name;

    }
	public function contactshared()
	{
		return $this->belongsTo(Contactshared::class);
	}
	 public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
