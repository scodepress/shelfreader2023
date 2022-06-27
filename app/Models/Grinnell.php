<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grinnell extends Model
{
	use HasFactory;

	public static function getGrinnellItems()
	{
		return DB::table('grinnell_items')
			->select('barcode','call_number','title')
			->orderBy('call_number')
			->get();
	}
}
