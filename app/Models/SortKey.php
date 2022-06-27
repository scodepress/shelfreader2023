<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class SortKey extends Model
{
	use HasFactory;


	public static function index($callNumber,$barcode) {

		$pre_keys = Callnumber::makeKey($callNumber);

		$keys = explode("*", $pre_keys);

		self::storeSortKeyByu($keys,$callNumber,$barcode);

		$user_id = Auth::user()->id;

		return self::correctShelfPositionByu($barcode,$user_id);
	}

	public static function storeSortKeyByu($keys,$callNumber,$barcode)
	{

		$prefix = trim($keys[0]);
		$tp1 = trim($keys[1]);
		$tp2 = trim($keys[2]);
		$pre_date = trim($keys[3]);
		$pvn = trim($keys[4]);
		$pvl = trim($keys[5]);
		$cutter = trim($keys[6]);
		$pcd = trim($keys[7]);
		$cutter_date = trim($keys[8]);
		$inline_cutter = trim($keys[9]);
		$inline_cutter_decimal = trim($keys[10]);
		$cutter_date2 = trim($keys[11]);
		$cutter2 = trim($keys[12]);
		$pcd2 = trim($keys[13]);
		$part1 = trim($keys[14]);
		$part2 = 0;
		$part3 = 0;
		$part4 = 0;
		$part5 = 0;
		$part6 = 0;
		$part7 = 0;


		// Insert this into sortkeys table

		$sort = new SortKey;

		$sort->user_id = Auth::id(); 
		$sort->barcode = $barcode;
		$sort->callno = $callNumber;
		$sort->prefix = $prefix;
		$sort->tp1 = $tp1;
		$sort->tp2 = ".$tp2";
		$sort->pre_date = $pre_date;
		$sort->pvn = $pvn;
		$sort->pvl = $pvl;
		$sort->cutter = $cutter;
		$sort->pcd = ".$pcd";
		$cutter_date = $cutter_date;
		$sort->inline_cutter = $inline_cutter;
		$sort->inline_cutter_decimal = ".$inline_cutter_decimal";   
		$sort->cutter_date2 = $cutter_date2;   
		$sort->cutter2 = $cutter2;
		$sort->pcd2 = ".$pcd2";
		$sort->part1 = $part1;
		$sort->part2 = $part2;
		$sort->part3 = $part3;
		$sort->part4 = $part4;
		$sort->part5 = $part5;
		$sort->part6 = $part6;
		$sort->part7 = $part7;

		$sort->save();

	}


	public static function correctShelfPositionByu($barcode,$user_id)
	{
		$shelfCount = Shelf::where('user_id',$user_id)->count();
		if($shelfCount === 0)
		{
			return 1;
		} else {

			$cpositions = self::correctPositionsByu($user_id);

			foreach($cpositions as $key=>$c) 
			{
				if($c->barcode == $barcode) {
					return $key+1;
				}
			}
		}
	}



	public static function correctPositionsByu($user_id)
	{
		return DB::table('sort_keys')
			->select('barcode')
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy("part2")
			->orderBy("part3")
			->orderBy("part4")
			->orderBy("part5")
			->orderBy("part6")
			->orderBy("part7")
			->orderBy('created_at')
			->where('user_id',$user_id)
			->get();
	} 
}
