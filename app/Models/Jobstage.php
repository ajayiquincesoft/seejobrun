<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobstage extends Model
{
    use HasFactory;

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class,'stage_id','id');
    }

    public function stagetemplate()
    {
        return $this->hasMany(Stagetemplate::class,'template_id','id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class,'media_id','id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class,'contact_id','id');
    }
}
