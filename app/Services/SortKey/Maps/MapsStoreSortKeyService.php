<?php

namespace App\Services\SortKey\Maps;

use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Facades\Services\MapsCallnumberService;
use App\Models\MapKey;
use App\Models\Shelf;
use App\Models\SortKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MapsStoreSortKeyService implements SortKeysInterface  {


	public function index($callNumber,$barcode) {

		$keys = new MapsCallnumberService();
		$mapKeys = $keys->getCallNumberKey($callNumber);
		$this->storeSortKey($mapKeys,$callNumber,$barcode);

		$user_id = Auth::user()->id;
		return $this->correctShelfPosition($barcode,$user_id);

	}

	public function storeSortKey($keys,$callNumber,$barcode)
	{
		$skey = new MapKey;
		$skey->user_id = Auth::user()->id;
		$skey->barcode = $barcode;
		$skey->callnumber = $callNumber;
		$skey->class_letter = $keys['classLetter'] ;
		$skey->class_number = $keys['classNumber'] ;
		$skey->class_decimal = $keys['classDecimalNumber'] ;
		$skey->topline_dotted_cutter_letter = $keys['firstDottedCutterLetter'] ;
		$skey->topline_dotted_cutter_decimal = $keys['firstDottedCutterDecimal'] ;
		$skey->neighborhood_cutter = $keys['neighborhoodCutter'] ;
		$skey->first_undotted_cutter_letter = $keys['firstUndottedCutterLetter'] ;
		$skey->first_undotted_cutter_decimal = $keys['firstUndottedCutterDecimal'] ;
		$skey->scale_name = $keys['scaleName'];
		$skey->scale_number = $keys['scaleNumber'];
		$skey->publication_date = $keys['dateOfUse'] ;
		$skey->nextline_dotted_cutter_letter = $keys['secondDottedCutterLetter'];
		$skey->nextline_dotted_cutter_decimal = $keys['secondDottedCutterDecimal'];
		$skey->specification = $keys['specification'];
		$skey->year_of_reproduction = $keys['dateOfReproduction'];
		$skey->save();


	}

	public function correctShelfPosition($barcode,$user_id)
	{
		if(MapKey::where('user_id',Auth::user()->id)->count() < 2) { return 1;}
		
		$cpositions = $this->correctPositions($user_id);

		foreach($cpositions as $key=>$c) 
		{
			if($c->barcode == $barcode) {
				return $key+1;
			}
		}
	}



	public function correctPositions($user_id)
	{ 

		return DB::table('map_keys')
			->select('barcode')
			->where('user_id',$user_id)
			->orderByDesc('class_number')
			->orderByDesc('class_decimal')
			->orderByDesc('topline_dotted_cutter_letter')
			->orderByDesc('topline_dotted_cutter_decimal')
			->orderByDesc('neighborhood_cutter')
			->orderByDesc('first_undotted_cutter_letter')
			->orderByDesc('first_undotted_cutter_decimal')
			->orderByDesc('scale_name')
			->orderByDesc('scale_number')
			->orderBy('publication_date')
			->orderByDesc('nextline_dotted_cutter_letter')
			->orderByDesc('nextline_dotted_cutter_decimal')
			->orderByDesc('specification')
			->orderBy('year_of_reproduction')
			->orderByDesc('created_at')
			->get();
	} 
}
