<?php

namespace App\Services\SortKey\Dewey;

use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Models\DeweySortKey;
use App\Models\MissouriDeweySortKey;
use App\Traits\MissouriDeweyTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeweyStoreSortKeyService implements SortKeysInterface  {

	use MissouriDeweyTrait;


	public function index($callNumber,$barcode) 
	{
		$keys = null;
		$user_id = Auth::user()->id;

		$this->storeSortKey($keys,$callNumber,$barcode);

		return $this->correctShelfPosition($barcode,$user_id);
	}

	public function storeSortKey($keys,$callNumber,$barcode)
	{
		$this->storeSortKeyString($callNumber,$barcode);
	}

	public function correctShelfPosition($barcode,$user_id)
	{
		if(MissouriDeweySortKey::where('user_id',$user_id)->count() < 2) {
			return 1;
		}

		$cpositions = $this->getSortedItems($user_id);

		foreach($cpositions as $key=>$c) 
		{
			if($c->barcode == $barcode) {
				return $key+1;
			}
		}
	}

	public function getSortedItems($user_id)
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
