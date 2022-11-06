<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Api\ApiServicesInterface;
use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Contracts\Services\MasterShelf\MasterShelfInterface;
use Illuminate\Http\Request;
use App\Traits\BookShelfTrait;
use App\Traits\InstitutionTrait;
use App\Models\ApiService;
use App\Models\FirstScan;
use App\Models\Grinnell;
use App\Models\InstitutionApiService;
use App\Models\Institution;
use App\Models\LocalInventoryOut;
use App\Models\MasterShelf;
use App\Models\Move;
use App\Models\Status;
use App\Models\Shelf;
use App\Models\ShelfCandidate;
use App\Models\SkidmoreCloud;
use App\Models\SortKey;
use App\Models\SortScheme;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use MasterShelfService;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class ShelvesController extends Controller
{
	use BookShelfTrait;
	use InstitutionTrait;
	use UserTrait;

public function __construct(MasterShelfInterface $msi)
	{
		$this->msi = $msi;
	}
	public function show()
	{

		// If there are books in the inventory demo, run the inventory truncate method
		$user_id = Auth::user()->id;
		$institution_id = Auth::user()->institution_id;
		 
		$sortSchemeId = InstitutionApiService::getLoadedSortSchemeId($user_id);
		$apiServiceId = InstitutionApiService::getLoadedApiServiceId($user_id);
		$unloadedService = InstitutionApiService::where('user_id',$user_id)
			->where('loaded',0)->get();

		$sortSchemeName = InstitutionApiService::where('user_id',$user_id)->where('loaded',1)->pluck('sort_scheme_name')[0];


		$institution = Institution::where('id',Auth::user()->institution_id)->get();

		$skidmoreCloud =0;
		$user = User::where('id',Auth::user()->id)->get();
		$hasLoadedService = $this->hasLoadedService();

		$sort_schemes = SortScheme::get();


		$shelf = Shelf::where('user_id', Auth::user()->id)->orderBy('shelf_position')->get();

		$api_services = ApiService::institutionServices(Auth::user()->institution_id);
		$libraryApiServices = InstitutionApiService::getApiServices($institution_id,$user_id);

		$shelf_count = count($shelf);

		if($shelf_count>0)
		{
			$initialLocationName = Shelf::where('user_id',$user_id)
				->orderBy('scan_order')
				->pluck('effective_location_name')[0];

		} else {

			$initialLocationName = '';
		}

		$locationStatus = Shelf::where('user_id',$user_id)->orderByDesc('id')->get();

		if($locationStatus->first()) {
			$status = $locationStatus[0]->status;
		       	$statusAlert = $locationStatus[0]->effective_location_id;
		} else {

			$status = 'None';
			$statusAlert = '';
		}

		$offshelf = 0;

		if( $shelf_count <= 18 ) { $offset = 0; }
		else
		{ $offset = ($shelf_count*59)-(18*59); } // Minus the number of books that fit on the shelf

		$count_corrections = $this->countCorrections(Auth::user()->id);
		$shelf_errors = $this->countShelfErrors(Auth::user()->id);

		if($shelf_errors > 0 && $shelf_count > 1) {

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


		$countOfSortSchemes = DB::table('institution_api_services')
			->select('sort_scheme_id')
			->where('user_id',$user_id)
			->distinct('sort_scheme_id')
			->count();

		$corrections = Move::where('user_id',Auth::user()->id)->count();


		return Inertia::render('Shelf/ShelfDisplay', [

			'shelf' => $shelf,
			'offshelf' => $offshelf,
			'offset' => $offset,
			'lis' => $lis,
			'api_services' => $api_services,
			'movers' => $movers,
			'count_corrections' => $count_corrections,
			'mpos' => $mpos,
			'mover' => $currentMover,
			'corrections' => $corrections,
			'sortSchemeId' => $sortSchemeId,
			'apiServiceId' => $apiServiceId,
			'sortSchemeName' => $sortSchemeName,
			'shelf_errors' => $shelf_errors,
			'shelf_count' => $shelf_count,
			'loaded_service' => $apiServiceId,
			'status'=>$status,
			'sort_schemes' => $sort_schemes,
			'libraryApiServices' => $libraryApiServices,
			'statusAlert' => $statusAlert,
			'unloadedService' => $unloadedService,
			'countOfSortSchemes' => $countOfSortSchemes,
		]);


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

		return Redirect::route('shelf')->with(['message','You re-scanned the wrong item.']);	
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
	// Access the SortKeyInterface here*/

	$key_save = $this->storeKey($callnumber,$barcode,$user_id);


	$this->placeBookOnShelf($user_id,$barcode,$callnumber,$title);
	$cposition = SortKey::correctShelfPosition($barcode,$user_id);
	Shelf::resetCpositions($barcode,$cposition,$user_id);

	if($this->countShelfErrors($user_id)>1) {

	$this->fillSubsequence($user_id);
	$this->fillMoves($user_id);
	}



	return Redirect::route('shelf');



	}

	public function truncate(Request $request)
	{
		$user_id = $request->user()->id;

		$libraryId = Auth::user()->library_id;

		if(Shelf::where('user_id',$user_id)->first())
		{
			$this->msi->insertNewItems($user_id,$libraryId);
		}
		
		$this->clearShelf($user_id);

		if($request->sortSchemeId === 2)
		{

			return Redirect::route('maps');

		} else {

			return Redirect::route('shelf');
		}
	}


	public function delete(Request $request){


		Shelf::where('barcode',$request->barcode)
			->where('user_id',$request->user()->id)
			->delete();

		SortKey::where('barcode',$request->barcode)
			->where('user_id',$request->user()->id)
			->delete();

		return Redirect::route('shelf');

	}


}
