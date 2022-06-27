<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Shelf;

class MapKey extends Model
{
    use HasFactory;

    
    public static function correctShelfPosition($barcode,$user_id)
	{
		if(Shelf::where('user_id',Auth::user()->id)->count() < 2) { return 1;}
		
		$cpositions = self::correctPositions($user_id);

		foreach($cpositions as $key=>$c) 
		{
			if($c->barcode == $barcode) {
				return $key+1;
			}
		}
	}



	public static function correctPositions($user_id)
	{ 

		return DB::table('map_keys')
			->select('barcode')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('first_dotted_cutter_letter')
			->orderBy('first_dotted_cutter_number')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy('first_undotted_cutter_number')
			->orderByRaw('publication_date')
			->orderBy('second_dotted_cutter_letter')
			->orderByRaw('second_dotted_cutter_decimal')
			->orderBy('scale')
			->orderBy('cutter_letter3')
			->orderBy('cutter_number3')
			->orderBy('year_of_reproduction')
			->orderBy('third_dotted_cutter_letter')
			->orderBy('third_dotted_cutter_number')
			->orderBy('created_at')
			->where('user_id',$user_id)
			->get();
	} 
}

