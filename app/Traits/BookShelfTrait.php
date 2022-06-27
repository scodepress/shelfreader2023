<?php

namespace App\Traits;

use App\Models\Shelf;
use App\Models\SortKey;
use App\Models\DeweySortKey;
use App\Models\Lis;
use App\Models\Move;
use App\Models\Subsequence;
use App\Models\Alma;
use App\Models\Sirsi;
use App\Models\Status;
use App\Models\Callnumber;
use App\Models\FirstScan;
use App\Models\InstitutionApiService;
use App\Models\LocalInventoryOut;
use App\Models\MapKey;
use App\Models\MissouriDeweySortKey;
use App\Models\OnlineInventoryItem;
use App\Models\Setting;
use App\Models\ShelfCandidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait BookShelfTrait {

	public function placeBookOnShelf($user_id,$barcode,$callnumber,$title,$status,$effectiveShelvingOrder,
		$effective_location_id,$effectiveLocationName,$correct_position) {


		$initial_shelf_position = Shelf::where('user_id', $user_id)->count()+1;

		$bk = new Shelf;

		$bk->scan_order = $initial_shelf_position;
		$bk->user_id = $user_id;
		$bk->callnumber = $callnumber;
		$bk->barcode = $barcode;
		$bk->title = $title;
		$bk->shelf_position = $initial_shelf_position;
		$bk->correct_position = $correct_position;
		$bk->status = $status;
		$bk->effective_shelving_order = $effectiveShelvingOrder;
		$bk->effective_location_id = $effective_location_id;
		$bk->effective_location_name = $effectiveLocationName;
		$bk->save();
	}

	public function checkNewBook($barcode,$user_id)
	{

		return Shelf::where('user_id',$user_id)->where('barcode',$barcode)->count();

	}


	public function storeKey($callnumber,$barcode)
	{
		$pre_sort_key = Callnumber::makeKey($callnumber);

		$sort_key = explode("*", $pre_sort_key);



		$prefix = trim($sort_key[0]);
		$tp1 = trim($sort_key[1]);
		$tp2 = trim($sort_key[2]);
		$pre_date = trim($sort_key[3]);
		$pvn = trim($sort_key[4]);
		$pvl = trim($sort_key[5]);
		$cutter = trim($sort_key[6]);
		$pcd = trim($sort_key[7]);
		$cutter_date = trim($sort_key[8]);
		$inline_cutter = trim($sort_key[9]);
		$inline_cutter_decimal = trim($sort_key[10]);
		$cutter_date2 = trim($sort_key[11]);
		$cutter2 = trim($sort_key[12]);
		$pcd2 = trim($sort_key[13]);
		$part1 = trim($sort_key[14]);
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
		$sort->callno = $callnumber;
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
		$sort->cutter_date2;   
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

	public function storeDeweyKey($callnumber,$barcode,$user_id)
	{

		$key_string = DeweySortKey::createKey($callnumber);

		$parts = explode('*', $key_string);

		$category = $parts[0];
		$decimel = $parts[1];
		$letters = $parts[2];

		$key = new DeweySortKey;

		$key->user_id = $user_id;
		$key->barcode = $barcode;
		$key->class = $category;
		$key->subdivision = $decimel;
		$key->letters = $letters;

		$key->save();
	}

	public function currentService($user_id)
	{
		return InstitutionApiService::where('user_id', $user_id)->pluck('api_service_id')[0];

	}

	public function almaResponse($barcode,$user_id)
	{
		$service = $this->currentService($user_id);

		if($service === 1 ) {

			return Alma::almaResponse($barcode);

		} else {

			return Sirsi::sirsiResponse($barcode); 
		}

	}

	public function itemParameters($barcode,$user_id)
	{
		$service = $this->currentService($user_id);

		if($service === 1 ) {

			return Alma::almaParameters($barcode);

		} else {

			return Sirsi::sirsiParameters($barcode); 
		}


	}

	public function cpositionArray($user_id)
	{
		return Shelf::cpositionByPosition($user_id);
	}

	public function movers($user_id)
	{
		$cposition_array = $this->cpositionArray($user_id);
		$lis = $this->lis($user_id);
		$m = array_diff($cposition_array,$lis);
		sort($m);
		return $m;
	}

	public function lis($user_id)
	{
		$cpos_array = $this->cpositionArray($user_id);
		//dd($cpos_array);
		$n = count($cpos_array);

		return Lis::LongestIncreasingSubsequence($cpos_array, $n);
	}

	public function mpos($user_id)

	{  
	/*
	|--------------------------------------------------------------------------
	| Move position method
	|--------------------------------------------------------------------------
	|
	| This method finds the destination position of the currently moving item
	|
	 */

		// Set of correct positions ordered by position
		$gcp = $this->cpositionArray($user_id); 

		// all cpositions in subsequences table ordered by position
		$plis = $this->lis($user_id);
		//dd($plis);

		// Gets cposition of next book to be moved
		$move = $this->moverCposition($user_id);

		//dd($move);

		$mgc = max($gcp);
		//dd($mgc);
		$mnc = min($gcp);
		//dd($mnc);
		$mpos = null;

		foreach($plis as $key=>$l)

		{
			// cposition of moving book is less than the current element in lis 
			if($move < $l)

			{

				//Get current position of book with cposition of $l
				$cup = \DB::table('shelves')
					->where('correct_position', $l)
					->where('user_id',$user_id)
					->pluck('shelf_position')[0];

				//dd($cup);

				$mp = \DB::table('shelves')
					->where('correct_position', $move)
					->where('user_id',$user_id)
					->pluck('shelf_position')[0];

				if($mp > $cup) { return $cup; } //book is moving from right of $l to position of $l (left move)

				else // book is moving from left of $l to left adjacent of $l (right move)

				{ 
					$mpos = $cup-1; 
					if($mpos<$mnc) { return $move; } 
					else 
					{ return $mpos; }  }

			}

		}



		if($mpos == null)  // Cposition of book is greater than greatest lis element (all right moves)

		{
			foreach($plis as $key=>$l)

			{
				if($move>$l)

				{   

					$cup = \DB::table('shelves')
						->where('correct_position', $l)
						->where('user_id',$user_id)
						->pluck('shelf_position')[0];

					$mpos = $cup+($mgc-$cup); 
				}
			}

			return $mpos;

		}

	}

	public function moverCposition($user_id)
	{
		// Get correct position of next book to be moved
		return \DB::table('moves')
			->select('correct_position')
			->where('user_id', $user_id)
			->orderBy('correct_position')
			->first()->correct_position;
	}

	public function nextMoverItem($user_id)
	{
		// Get correct position of next book to be moved
		return \DB::table('moves as m')
			->join('shelves as s','s.barcode','=','m.barcode')
			->select('m.barcode','s.title')
			->where('m.user_id', $user_id)
			->orderBy('m.correct_position')
			->limit(1)
			->get();
	}

	public function nextMoverBarcode($user_id)
	{
		// Get correct position of next book to be moved
		return \DB::table('moves')
			->select('barcode')
			->where('user_id', $user_id)
			->orderBy('correct_position')
			->get();
	}

	public function moverPosition($user_id)
	{
		// Get shelf position of current book to be moved
		return \DB::table('moves')
			->select('shelf_position')
			->where('user_id', $user_id)
			->orderBy('correct_position')
			->first()->shelf_position;
	}

	public function book_right($shelf_position,$mpos,$dbar,$user_id)
	{
	    /*
	    |--------------------------------------------------------------------------
	| Adjusts shelf_positions when a correction is made in the shelf (book moving right)
	|--------------------------------------------------------------------------
	|
	| Fires when barcode is re-scanned
	|
	     */

		//dd($shelf_position);

		// Increment positions of books on shelf when book is moving right
		\DB::table('shelves')
			->where('shelf_position', '>', $shelf_position)
			->where('user_id', $user_id)
			->where('shelf_position', '<=', $mpos)
			->decrement('shelf_position', 1);

		// Change the position of the moved book to it's new position
		\DB::table('shelves')
			->where('user_id', $user_id)
			->where('barcode', $dbar)
			->update(['shelf_position' => $mpos]);

		$query = \DB::statement("Update moves m 
			inner join shelves s 
			on s.barcode = m.barcode 
			set m.shelf_position = s.shelf_position 
			where s.user_id = $user_id"); 

		return $query;

	}

	public function book_left($shelf_position,$mpos,$dbar,$user_id)
	{
	/*
	|--------------------------------------------------------------------------
	| Adjusts shelf_positions when a correction is made in the shelf (book moving left)
	|--------------------------------------------------------------------------
	|
	| Fires when barcode is re-scanned
	|
	 */


		// Increment positions of books on shelf when book is moving left
		Shelf::where('shelf_position', '>=', $mpos)->where('user_id', \Auth::id())
					     ->where('shelf_position', '<', $shelf_position)->increment('shelf_position',1);

		// Change the position of the moved book to it's new position
		Shelf::where('user_id', \Auth::id())
			->where('barcode', $dbar)
			->update(['shelf_position' => $mpos]);

		$query = \DB::statement("Update moves m 
			inner join shelves s 
			on s.barcode = m.barcode 
			set m.shelf_position = s.shelf_position 
			where s.user_id = $user_id");

		return $query; 

	}

	public function fillMoves($user_id)
	{
		// This happens any time a new book is added and there are errors in the shelf
		Move::where('user_id', $user_id)->delete();

		$movers = $this->movers($user_id);

		$binfo = DB::table('shelves')
			->select('user_id','barcode','shelf_position','correct_position')
			->whereIn('correct_position', $movers)
			->where('user_id', Auth::id())
			->get();

		foreach($binfo as $key=>$b) {
			$book_info[] = ['user_id' => $b->user_id, 'barcode' => $b->barcode, 'shelf_position' => $b->shelf_position,
				'correct_position' => $b->correct_position];
		}

		DB::table('moves')->insert($book_info);

	}

	public function currentMovers($user_id)
	{
		return \DB::table('moves as m')
			->join('shelves as s','m.barcode','=','s.barcode')
			->select('m.barcode','m.shelf_position','m.correct_position','s.title','s.callnumber')
			->where('m.user_id', $user_id)
			->orderBy('m.correct_position')
			->get();
	}

	public function fillSubsequence($user_id)
	{
		Subsequence::where('user_id', $user_id)->delete();
		$lis = $this->lis($user_id);

		$binfo = \DB::table('shelves')
			->select('user_id','barcode','shelf_position','correct_position')
			->whereIn('correct_position', $lis)
			->where('user_id', Auth::id())
			->get();

		foreach($binfo as $key=>$b) {
			$book_info[] = ['user_id' => $b->user_id, 'barcode' => $b->barcode, 'shelf_position' => $b->shelf_position,
				'correct_position' => $b->correct_position];
		}

		\DB::table('subsequences')->insert($book_info);
	}

	public static function countShelfErrors($user_id)

	{

		return DB::table('shelves as s1')
			->join('shelves as s2', 's2.barcode', '=', 's1.barcode')
			->selectRaw("s2.id")
			->whereRaw("s2.shelf_position != s2.correct_position")
			->where('s2.user_id', '=', $user_id)
			->where('s1.user_id', '=', $user_id)
			->where('s1.user_id',$user_id)
			->where('s2.user_id',$user_id)
			->count();
	}

	public function countCorrections($user_id)
	{
		return \DB::table('moves')
			->where('user_id', $user_id)
			->count();
	} 

	public function onShelf($barcode,$user_id)
	{
		return \DB::table('shelves')
			->where('barcode', $barcode)
			->where('user_id', $user_id)
			->count();
	}

	public function clearShelf($user_id)
	{
		Shelf::where('user_id',$user_id)->delete();
		SortKey::where('user_id',$user_id)->delete();
		Subsequence::where('user_id', $user_id)->delete();
		Move::where('user_id',$user_id)->delete();
		Status::where('user_id',$user_id)->delete();
		LocalInventoryOut::where('user_id',$user_id)->delete();
		OnlineInventoryItem::where('user_id',$user_id)->delete();
	}


}
