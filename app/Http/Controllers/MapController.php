<?php

namespace App\Http\Controllers;

use App\Traits\BookShelfTrait;
use App\Traits\InstitutionTrait;
use App\Models\Alma;
use App\Models\ApiService;
use App\Models\DeweySortKey;
use App\Models\InstitutionApiService;
use App\Models\InstitutionSortScheme;
use App\Models\Institution;
use App\Models\Lis;
use App\Models\LocalInventoryOut;
use App\Models\Move;
use App\Models\Setting;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use App\Models\SortKey;
use App\Models\SortScheme;
use App\Models\Subsequence;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use MapsCallnumber;
use App\Facades\MapsCallnumberFacade;
use App\Models\Cloud;
use App\Models\Map;
use App\Models\Shelf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MapController extends Controller
{
	use BookShelfTrait;
	use InstitutionTrait;
	use UserTrait;

	public function show()
	{
		$drawer = Shelf::where('user_id',Auth::user()->id)->orderBy('shelf_position')->get();
		// If there are books in the inventory demo, run the inventory truncate method
		$user_id = Auth::user()->id;
		$institution_id = Auth::user()->institution_id;
		$institution = Institution::where('id',Auth::user()->institution_id)->get();
		$user = User::where('id',Auth::user()->id)->get();
		$hasLoadedService = $this->hasLoadedService();

		$apiServiceId = InstitutionApiService::getLoadedApiServiceId($user_id);
		$sortSchemeId = InstitutionApiService::getLoadedSortSchemeId($user_id);

		$loaded_service = InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->pluck('api_service_id')[0];
		$unloaded_service = InstitutionApiService::where('user_id',Auth::user()->id)
			->where('loaded',0)
			->get();

		$sortSchemeName = InstitutionApiService::where('user_id',$user_id)->where('loaded',1)->pluck('sort_scheme_name')[0];
		$sort_schemes = SortScheme::get();

		$current_api_service_id = InstitutionApiService::where('institution_id',Auth::user()->institution_id)->where('user_id',Auth::user			()->id)->where('loaded',1)->get();

		$shelf = Shelf::where('user_id', Auth::user()->id)->orderBy('shelf_position')->get();

		$api_services = ApiService::institutionServices(Auth::user()->institution_id);

		$shelf_count = count($shelf);

		$api_services = ApiService::institutionServices(Auth::user()->institution_id);
		$libraryApiServices = InstitutionApiService::getApiServices($institution_id,$user_id);

		$status = Status::where('user_id',Auth::user()->id)->pluck('status');

		if($status->first()) {$status = $status[0]; } else {$status = 'None';}

		$libraryApiServices = InstitutionApiService::getApiServices($institution_id,$user_id);

		$count_corrections = $this->countCorrections(Auth::user()->id);
		$shelf_errors = $this->countShelfErrors(Auth::user()->id);

		if($shelf_errors > 0 && $shelf_count > 1) {

			$lis = $this->lis(Auth::user()->id);

			// returns the destination position of the currently moving book (the green line)
			$mpos = $this->mpos(Auth::user()->id);

			// Returns shelf position of the currently moving book (the blue title)
			$currentMover = $this->moverPosition(Auth::user()->id);

			$movers = $this->currentMovers(Auth::user()->id);
			$nextMoverItem = $this->nextMoverItem($user_id);

		} else {

			$movers = 0;
			$lis = 0;
			$mpos = 0;
			$currentMover = 0;
			$nextMoverItem = 0;

		}

		$onshelf = 0;


		$corrections = Move::where('user_id',Auth::user()->id)->count();

		return Inertia::render('Maps/Index',[
			'drawer' => $drawer,
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
			'loadedService' => $loaded_service,
			'unloadedService' => $unloaded_service,
			'status'=>$status,
		       	'sortSchemes' => $sort_schemes,
		       	'sortSchemeId' => $sortSchemeId,
		       	'nextMoverItem' => $nextMoverItem,
		       	'libraryApiServices' => $libraryApiServices,
		]);

	}

}

