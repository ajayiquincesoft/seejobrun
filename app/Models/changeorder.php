<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class changeorder extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id','id');
    }

    public function item()
    {
        return $this->hasMany(Item::class,'order_id','id');
    }

    public function getClientIdAttribute($value)
    {
        return unserialize($value);
    }
}
