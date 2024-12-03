<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobmedia extends Model
{
    use HasFactory;

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class,'media_id');
    }
}
