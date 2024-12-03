<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punchlistimg extends Model
{
    use HasFactory;

    public function punchlist()
    {
        return $this->belongsTo(Punchlist::class,'punch_id');
    }
}
