<?php

namespace App\Models;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterShelf extends Model
{
	use HasFactory;


	public function scopeUser($query){
		return $query->where('user_id',Auth::id()); 
	}



}
