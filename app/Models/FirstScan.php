<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirstScan extends Model
{
    use HasFactory;

    public function scopeUser($query)
    {
	    return $query->where('user_id',Auth::id());
    }
}
