<?php

namespace App\Services\RequestHandling;

use App\Contracts\Services\Api\ApiServicesInterface;
use App\Models\Shelf;
use Illuminate\Http\Request;
use App\Exceptions\Handler;
use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Models\FirstScan;
use App\Models\ShelfCandidate;
use App\Models\Status;
use App\Models\UnprocessedCallNumber;
use Auth;
use App\Traits\BookShelfTrait;
use Redirect;

class FolioRequests
{
	use BookShelfTrait;

	public function getFirstScan()
	{
		return $this->firstScan->where('user_id',Auth::id())->get();
	}

	public function apiResponse($apiServicesInterface,$barcode) 
	{
		return $apiServicesInterface->itemParameters($barcode);
	}

	public function saveFirstScan($callNumber,$barcode,$title,$effectiveLocationName,$effectiveLocationId)
	{

		$fs = new FirstScan;
		$fs->user_id = Auth::id();
		$fs->callnumber = $callNumber;
		$fs->barcode = $barcode;
		$fs->title = $title;
		$fs->effective_location_name = $effectiveLocationName;
		$fs->effective_location_id = $effectiveLocationId;
		$fs->save();

	}

	public function isCorrectLocation($barcode)
	{
		$candidateLocation = ShelfCandidate::user()->where('barcode',$barcode)->pluck('effective_location_id')[0];

		$firstScanLocation = FirstScan::user()->pluck('effective_location_id')[0];

		if($firstScanLocation === $candidateLocation)
		{
			return true;
		} else {

			return false;
		}
	}

	public function saveShelfCandidate($callNumber,$barcode,$title,$status,$effectiveShelvingOrder,$effectiveLocationId,$effectiveLocationName)
	{

		$shel = new ShelfCandidate;
		$shel->user_id = Auth::id();
		$shel->callnumber = $callNumber;
		$shel->barcode = $barcode;
		$shel->title = $title;
		$shel->status = $status;
		$shel->effective_shelving_order = $effectiveShelvingOrder;
		$shel->effective_location_id = $effectiveLocationId;
		$shel->effective_location_name = $effectiveLocationName;
		$shel->save();

	}

	public function saveCandidateToShelf($cposition,$callNumber,$barcode,$title,$status,$effectiveShelvingOrder,$effectiveLocationId,$effectiveLocationName)
	{

		$initial_shelf_position = Shelf::where('user_id',Auth::id())->count()+1;

		$bk = new Shelf;

		$bk->scan_order = $initial_shelf_position;
		$bk->user_id = Auth::id();
		$bk->callnumber = $callNumber;
		$bk->barcode = $barcode;
		$bk->title = $title;
		$bk->shelf_position = $initial_shelf_position;
		$bk->correct_position = $cposition;
		$bk->status = $status;
		$bk->effective_shelving_order = $effectiveShelvingOrder;
		$bk->effective_location_id = $effectiveLocationId;
		$bk->effective_location_name = $effectiveLocationName;
		$bk->save();

		Shelf::resetCpositions($barcode,$cposition,Auth::id());
	}

	public function bookNotOnShelf($cposition,$sortSchemeId,$user_id,$callNumber,$barcode,$title,$status,
		$effectiveLocationName,$effectiveLocationId,$effectiveShelvingOrder){

		try {

			if($sortSchemeId === 1)
			{
				// LCC sorting
				$this->placeBookOnShelf($user_id,$barcode,$callNumber,$title,$status,$effectiveShelvingOrder,
					$effectiveLocationId,$effectiveLocationName,$cposition);

			}  elseif($sortSchemeId === 2) {
				// Maps

				$this->placeBookOnShelf($user_id,$barcode,$callNumber,$title,$status,
					$effectiveShelvingOrder,$effectiveLocationId,$effectiveLocationName,$cposition);

			}



		} catch (\Illuminate\Database\QueryException $ex) {

			$upc = new UnprocessedCallNumber;
			$upc->user_id = $user_id;
			$upc->barcode = $barcode;
			$upc->call_number = $callNumber;
			$upc->title = $title;
			$upc->save();

			Status::where('user_id',$user_id)->delete();

			if(!$barcode) { $barcode = 'Empty'; }
			$stat = new Status;
			$stat->user_id = $user_id;
			$stat->barcode = $barcode;
			$stat->status = "Call Number '$callNumber' could not be processed";
			$stat->save();

			return false;
		}


	}

	public function checkBook($barcode,$user_id)
	{

		return Shelf::where('user_id',$user_id)->where('barcode',$barcode)->count();

	}

	public function bookJustShelved($user_id,$sortSchemeId)
	{

		if($this->countShelfErrors($user_id)>0) {

			$this->fillSubsequence($user_id);
			$this->fillMoves($user_id);
		}

		if($sortSchemeId === 2)

		{

			return Redirect::route('maps');
		} else {

			return Redirect::route('shelf');
		}
	}

}

