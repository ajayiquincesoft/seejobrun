<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zoha\Metable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
		'devicetype',
		'timezone',
		'stripe_customer_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function stage()
    {
        return $this->hasMany(Stage::class,'user_id','id');
    }

    public function media()
    {
        return $this->hasMany(Media::class,'user_id','id');
    }

    public function contact()
    {
        return $this->hasMany(Contact::class,'user_id','id');
    }

    public function contactshared()
    {
        return $this->hasMany(Contactshared::class,'user_id','id');
    }

    public function job()
    {
        return $this->hasMany(Job::class,'user_id','id');
    }
    public function lead()
    {
        return $this->hasMany(Lead::class,'user_id','id');
    }
    public function todosection()
    {
        return $this->hasMany(Todosection::class,'user_id','id');
    }
    public function general_todo_section()
    {
        return $this->hasMany(General_todo_section::class,'user_id','id');
    }
    public function jobstage()
    {
        return $this->hasMany(Jobstage::class,'user_id','id');
    }

    public function jobmedia()
    {
        return $this->hasMany(Jobmedia::class,'user_id','id');
    }

    public function jobcontact()
    {
        return $this->hasMany(Jobcontacts::class,'user_id','id');
    }

    public function jobinspection()
    {
        return $this->hasMany(Jobinspection::class,'user_id','id');
    }

    public function stagetemplate()
    {
        return $this->hasMany(Stagetemplate::class,'user_id','id');
    }

    public function selectedPlan()
    { 
        return $this->hasMany(SelectedPlan::class,'user_id','id');
    }

    public function payment()
    { 
        return $this->hasMany(Payment::class,'user_id','id');
    }

    public function punchlist()
    { 
        return $this->hasMany(Punchlist::class,'user_id','id');
    }

    public function punchlistimg()
    { 
        return $this->hasMany(Punchlistimg::class,'user_id','id');
    }  
	
	//task assignment
	public function taskassignment()
    { 
        return $this->hasMany(Taskassignment::class,'user_id','id');
    }

    public function taskassignmentimages()
    { 
        return $this->hasMany(Taskassignmentimages::class,'user_id','id');
    }
	

    public function changeorder()
    {
        return $this->hasMany(changeorder::class,'user_id','id');
    }

    public function item()
    {
        return $this->hasMany(Item::class,'user_id','id');
    }

    public function clocktime()
    {
        return $this->hasMany(Clocktime::class,'user_id','id');
    }

    public function contactuserid()
    {
        return $this->hasMany(Contact::class,'contact_user_id','id');
    }
	 public function notifications(){
        return $this->hasMany(Notification::class,'user_id','id');
    }
	 public function fcmToken(){
        return $this->hasOne(FcmToken::class,'user_id','id');
	}
	
	
}
