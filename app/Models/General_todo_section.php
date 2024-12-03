<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class General_todo_section extends Model
{
    use HasFactory;
    public function general_todo_task()
    {
        return $this->hasMany(General_todo_task::class,'todosec_id','id');
    }
}
