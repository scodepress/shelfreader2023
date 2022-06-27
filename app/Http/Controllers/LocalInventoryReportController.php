<?php

namespace App\Http\Controllers;

use App\Facades\OnlineInventoryFacade;
use App\Models\LocalInventory;
use App\Models\LocalInventoryOut;
use App\Models\MissouriStateMasterKey;
use App\Models\Shelf;
use App\Services\Folio\OnlineInventoryRequest;
use App\Models\OnlineInventoryItem;
use App\Services\OnlineInventory\OnlineInventoryService as OnlineInventoryOnlineInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use OnlineInventory;
use OnlineInventoryService;

class LocalInventoryReportController extends Controller
{

	public function show() {

		$bookCount = Shelf::where('user_id',Auth::id())->count();

		if($bookCount>0) {
			
			$effectiveLocationId = Shelf::where('user_id',Auth::user()->id)->orderByDesc('id')->pluck('effective_location_id')[0];

			$firstBookBarcode = Shelf::where('user_id',Auth::id())->orderBy('correct_position')->pluck('barcode')[0];
			$lastBookBarcode = Shelf::where('user_id',Auth::id())->orderByDesc('correct_position')->pluck('barcode')[0];
			$leftMostNormalizedCallNumber = Shelf::where('user_id',Auth::id())->orderBy('correct_position')
								     ->pluck('effective_shelving_order')[0];
			$rightMostNormalizedCallNumber = Shelf::where('user_id',Auth::id())->orderByDesc('correct_position')
								      ->pluck('effective_shelving_order')[0];
			$onlineInventory = OnlineInventoryService::createResponseArray($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,
			$effectiveLocationId);
			$firstBookPosition = Shelf::where('barcode',$firstBookBarcode)->pluck('id')[0];
			$firstBookCallNumber = OnlineInventoryItem::where('effective_shelving_order','>',$leftMostNormalizedCallNumber)
				->select('call_number')
				->orderBy('effective_shelving_order')
				->get();
			if($firstBookCallNumber->first()) {
				$firstBookCallNumber = $firstBookCallNumber->first()->call_number;
			} else {
				$firstBookBarcode = 0;
			}
			$lastBookPosition = Shelf::where('barcode',$lastBookBarcode)->pluck('id')[0];
			$lastBookCallNumber = Shelf::where('barcode',$lastBookBarcode)->pluck('callnumber')[0];

			$itemsWithIncorrectStatus = Shelf::where('status','!=','Available')->where('user_id',Auth::id())->get();

			$missingItems = OnlineInventoryService::getMissingItems();

			$uniqueLocationIds = OnlineInventoryService::uniqueShelfLocationIds(Auth::id());
			$uniqueLocationNames = OnlineInventoryService::uniqueShelfLocationNames(Auth::id());
			$uniqueStatuses = OnlineInventoryService::uniqueStatuses(Auth::id());
			$initialLocationName = OnlineInventoryService::initialLocationName(Auth::id());
			$unexpectedItems = OnlineInventoryService::unexpectedItems(Auth::id(),$initialLocationName[0]
				->effective_location_name,$firstBookBarcode,$lastBookBarcode);
			
		} else {

			$missingItems = 0;
			$firstBookPosition = null;
			$lastBookPosition = 0;
			$itemsWithIncorrectStatus = 0;
			$firstBookCallNumber = 0;
			$lastBookCallNumber = 0;
			$uniqueLocationIds = 0;
			$uniqueLocationNames = 0;
			$uniqueStatuses = 0;
			$unexpectedItems = 0;
			$initialLocationName = 0;
		}

		return Inertia::render('LocalInventory/Index',[
			'missingItems' => $missingItems,
			'firstBookPosition' => $firstBookPosition,
			'lastBookPosition' => $lastBookPosition,
			'itemsWithIncorrectStatus' => $itemsWithIncorrectStatus,
			'firstBookCallNumber' => $firstBookCallNumber,
			'lastBookCallNumber' => $lastBookCallNumber,
			'uniqueLocationIds' => $uniqueLocationIds,
			'uniqueLocationNames' => $uniqueLocationNames,
			'uniqueStatuses' => $uniqueStatuses,
			'initialLocationName' => $initialLocationName,
			'unexpectedItems' => $unexpectedItems,

		]);


	}
}
