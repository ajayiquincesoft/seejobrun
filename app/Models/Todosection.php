<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todosection extends Model
{
    use HasFactory;
    public function todosectiontask()
    {
        return $this->hasMany(Todosectiontask::class,'todosec_id','id');
    }
}
