<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taskassignmentimages extends Model
{
    use HasFactory;
	
	 public function taskassignment()
    {
        return $this->belongsTo(Taskassignment::class,'taskassignment_id');
    }
}
