<?php

namespace App\Models;

use App\Actions\Maps\MakeAmaskAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MissouriDeweySortKey extends Model
{
	use HasFactory;

	public static function correctShelfPosition($barcode,$user_id)
	{

		if(MissouriDeweySortKey::where('user_id',$user_id)->count() < 2) {
			return 1;
		}

		$cpositions = self::getSortedItems($user_id);

		foreach($cpositions as $key=>$c) 
		{
			if($c->barcode == $barcode) {
				return $key+1;
			}
		}
	}

	public static function getSortedItems($user_id)
	{  

		return DB::table('missouri_dewey_sort_keys')
			->select('barcode')
			->orderBy('prefix')
			->orderBy('author')
			->orderBy('cutter_number')
			->orderBy('decimal_number')
			->orderBy('title_letters')
			->orderBy('date')
			->orderBy('date2')
			->orderByRaw("volume_number1")
			->orderBy('volume_number2')
			->where('user_id',$user_id)
			->get();
	}

}
