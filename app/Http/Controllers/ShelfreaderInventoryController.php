<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\BookShelfTrait;
use App\Traits\InstitutionTrait;
use App\Models\ApiService;
use App\Models\InstitutionApiService;
use App\Models\InstitutionSortScheme;
use App\Models\Institution;
use App\Models\LocalInventory;
use App\Models\LocalInventoryOut;
use App\Models\Move;
use App\Models\Status;
use App\Models\Shelf;
use App\Models\SortKey;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ShelfreaderInventoryController extends Controller
{
	use BookShelfTrait;
	use InstitutionTrait;
	use UserTrait;
	/**
	 * @param Institution $institution
	 */

	public function show(Institution $institution)
	{	
		// If there are books in the inventory demo, run the inventory truncate method
		$user_id = Auth::user()->id; 
		$inventoryCount = LocalInventoryOut::where('user_id', $user_id)->count();
		if($inventoryCount === 0 && Shelf::where('user_id',$user_id)->count()>0) {

			$this->clearShelf($user_id);
		}

		$institution = $institution->where('id',Auth::user()->institution_id)->get();
		$user = User::where('id',Auth::user()->id)->get();
		$hasLoadedService = $this->hasLoadedService();

		$loaded_service = InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->get();

		$sort_schemes = InstitutionSortScheme::getSortSchemes(Auth::user()->institution_id);

		$current_api_service_id = InstitutionApiService::where('institution_id',Auth::user()
			->institution_id)
			->where('user_id',Auth::user()->id)
			->where('loaded',1)
			->get();

		$shelf = Shelf::where('user_id', Auth::user()->id)->orderBy('shelf_position')->get();

		$api_services = ApiService::institutionServices(Auth::user()->institution_id);

		$shelf_count = count($shelf);


		$status = Status::where('user_id',Auth::user()->id)->pluck('status');

		if($status->first()) {$status = $status[0]; } else {$status = 'None';}	
		$offshelf = Shelf::offShelf(Auth::user()->id,$shelf_count);

		$inventory = LocalInventory::getUnscannedItems();

		if( $shelf_count <= 18 ) { $offset = 0; }
		else
		{ $offset = ($shelf_count*59)-(18*59); } // Minus the number of books that fit on the shelf

		$count_corrections = $this->countCorrections(Auth::user()->id);
		$shelf_errors = $this->countShelfErrors(Auth::user()->id);

		if($shelf_errors > 0) {

			$lis = $this->lis(Auth::user()->id);

			// returns the destination position of the currently moving book (the green line)
			$mpos = $this->mpos(Auth::user()->id);

			// Returns shelf position of the currently moving book (the blue title)
			$currentMover = $this->moverPosition(Auth::user()->id);

			$movers = $this->currentMovers(Auth::user()->id);

		} else {

			$movers = 0;
			$lis = 0;
			$mpos = 0;
			$currentMover = 0;

		}

		$onshelf = 0;


		$corrections = Move::where('user_id',Auth::user()->id)->count();

		return Inertia::render('ShelfreaderInventory/Index', ['shelf' => $shelf,'offshelf' => $offshelf,'offset' => $offset, 'lis' => $lis,
			'api_services' => $api_services,
			'movers' => $movers, 'count_corrections' => $count_corrections, 'mpos' => $mpos, 'mover' => $currentMover,
			'corrections' => $corrections,
			'shelf_errors' => $shelf_errors, 'shelf_count' => $shelf_count, 'loaded_service' => $loaded_service, 'status'=>$status,
			'inventory'=>$inventory]);

	}

	public function checkBook($barcode,$user_id,$service)
	{

		$service = $this->currentService($user_id);

		$isOnShelf = Move::where('user_id',$user_id)->where('barcode',$barcode)->count();


		if($isOnShelf === 0) { 


			return $this->addBook($user_id,$barcode); 

		} else {

			$nextMoverBarcode = $this->nextMoverBarcode($user_id);

			if($nextMoverBarcode->first() && $nextMoverBarcode[0]->barcode === $barcode)
			{
				$title = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('title')[0];
				$callnumber = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('callnumber')[0];

				return Redirect::route('correction',['user_id'=>$user_id,'barcode'=>$barcode]);

			} else {

				return "Rescanned wrong book";
			}
		}

		//Error

	}
	public function addBook($user_id,$barcode)
	{

		$parameters = $this->itemParameters($barcode,$user_id);

		//Parameters returned from API call
		$callnumber = $parameters['call_number'];

		$title = $parameters['title'];


		// Create a key for the new book in the sort_keys table

		$key_save = $this->storeKey($callnumber,$barcode,$user_id);


		$this->placeBookOnShelf($user_id,$barcode,$callnumber,$title);
		$cposition = SortKey::correctShelfPosition($barcode,$user_id);
		Shelf::resetCpositions($barcode,$cposition,$user_id);

		if($this->countShelfErrors($user_id)>1) {

			$this->fillSubsequence($user_id);
			$this->fillMoves($user_id);
		}



		return Redirect::route('shelfreader-inventory');



	}

	public function truncate(Request $request)
	{
		$user_id = $request->user()->id;
		$this->clearShelf($user_id);

		return Redirect::route('shelf');
	}


}
