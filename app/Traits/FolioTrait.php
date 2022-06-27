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
use App\Models\Callnumber;
use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait FolioTrait {

	public function parseFolioResponse($folioResponse) {
		
		$folioItemAttributes = [];

		$folioItem = [
			
			'callNumber' => $folioResponse['items'][0]['callNumber'],
			'title' => $folioResponse['items'][0]['title'],
			'status' => $folioResponse['items'][0]['status']['name'],
			'effectiveShelvingOrder' => $folioResponse['items'][0]['effectiveShelvingOrder'],
			'effectiveLocation' => $folioResponse['items'][0]['effectiveLocation']['id'],
			
		];
			return $folioItem;

	}

	public function checkBook($barcode,$user_id)
	{

		return Shelf::where('user_id',$user_id)->where('barcode',$barcode)->count();

	}
							
	public function countUniqueLocationIds()
	{
		return Shelf::where('user_id',Auth::user()->id)->groupBy('effective_location_id')->count();
		
	}
	 
	public function addFolioBook($user_id,$barcode,$callNumber,$title){
		 
	
	}
}
