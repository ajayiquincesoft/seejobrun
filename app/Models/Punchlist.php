<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punchlist extends Model
{
    use HasFactory;

    protected $appends = ['contact_name'];

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

    public function punchlistimg()
    {
    	return $this->hasMany(Punchlistimg::class,'punch_id','id');
    }

    public function punchcontact()
    {
        return $this->belongsTo(Contact::class,'contact_id','id');
    }

    public function getContactNameAttribute()
    {
        return Contact::where('id','=',$this->contact_id)->first()->name;
    }
}
