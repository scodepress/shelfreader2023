<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Api\ApiServicesInterface;
use App\Models\FolioRequest;
use App\Models\Shelf;
use App\Models\Status;
use App\Models\SortKey;
use App\Models\Unfoundling;
use App\Models\InstitutionApiService;
use App\Models\UnprocessedCallNumber;
use App\Models\ClickedBarcode;
use App\Traits\BookShelfTrait;
use Illuminate\Http\Request;
use App\Traits\FolioTrait;
use App\Traits\SkidmoreTrait;
use App\Exceptions\Handler;
use App\Models\Institution;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Facades\Services\MapsCallnumberService;
use App\Models\FirstScan;
use App\Models\MapKey;
use App\Models\MissouriDeweySortKey;
use App\Models\ShelfCandidate;
use App\Models\Sirsi;
use App\Services\Api\Sirsi\SirsiApiService;
use App\Services\RequestHandling\FolioRequests;
use MapsCallnumber;
use FolioService;

class FolioRequestController extends Controller
{
	use FolioTrait;
	use SkidmoreTrait;
	use BookShelfTrait;



	public function processFolio(Request $request) {

		$user_id = $request->user()->id;
		$apiServiceId = $request->service;
		$barcode = $request->barcode;
		$sortSchemeId = $request->sortSchemeId;
		$apiResponse = Sirsi::itemParameters($barcode);
		if(!$apiResponse)
		{
			return Redirect::route('shelf')->with(['message'=>"The server returned an empty response." ]);
		}
		$callNumber = $apiResponse['callNumber'];
		$title = $apiResponse['title'];
		$status = $apiResponse['status'];
		$effectiveShelvingOrder = $apiResponse['effectiveShelvingOrder'];
		$effectiveLocationId = $apiResponse['effectiveLocationId'];
		$effectiveLocationName = $apiResponse['effectiveLocationName'];
		$institution_id = Auth::user()->institution_id;

		$nextMoverBarcode = $this->nextMoverBarcode($user_id);

		//Checks

		if(FolioService::checkBook($barcode,$user_id) === 0) 
		{
			$cposition = SortKey::index($callNumber,$barcode);
			FolioService::bookNotOnShelf($cposition,$sortSchemeId,$user_id,$callNumber,$barcode,$title,$status,
				$effectiveLocationName,$effectiveLocationId,$effectiveShelvingOrder);

			FolioService::bookJustShelved($user_id,$sortSchemeId);

		} else {

			$nextMoverBarcode = $this->nextMoverBarcode($user_id);

			if($nextMoverBarcode->first() && $nextMoverBarcode[0]->barcode === $barcode)
			{

				return Redirect::route('correction',['user_id'=>$user_id,'barcode'=>$barcode]);

			} else {

				return "You Re-Scanned the Wrong Books";
			} 
		}


		return Redirect::route('shelf');
	} 
}

