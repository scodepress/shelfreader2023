<?php

namespace App\Http\Controllers;

use App\Contracts\Services\MasterShelf\MasterShelfInterface;
use App\Models\MasterShelf;
use App\Models\FirstScan;
use Auth;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MasterShelfService;
use App\Exports\MasterShelfExport;
use App\Exports\MasterShelfExportByCallNumber;
use App\Exports\MasterShelfFullExport;
use App\Http\Requests\InventorySearchParameterRequest;
use App\Models\MasterShelfResult;
use App\Models\InstitutionApiService;
use App\Models\OnlineInventoryItem;
use App\Models\SearchParameter;
use App\Models\Shelf;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;
use Redirect;
use OnlineInventoryService;

class MasterShelfController extends Controller
{
	public function __construct(MasterShelfInterface $msi)
	{
		$this->msi = $msi;
	}

	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	public function export($dateFileFormat) 
	{
		if($dateFileFormat === 'csv')
		{
			return Excel::download(new MasterShelfExport(), 'inventory.csv');
		} else {

			return Excel::download(new MasterShelfExport(), 'inventory.xlsx');
		}

	}

	public function exportMasterShelf($fileFormat,$sortSchemeId) 
	{
		if($fileFormat === 'csv')
		{
			return Excel::download(new MasterShelfFullExport($sortSchemeId), 'inventory.csv');
		} else {

			return Excel::download(new MasterShelfFullExport($sortSchemeId), 'inventory.xlsx');
		}

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($sortSchemeId,$clear)
	{
		$user_id = Auth::user()->id;

		$libraryId = User::where('id',$user_id)->pluck('library_id')[0];
		$param = SearchParameter::where('user_id',$user_id)->get();
		$allDates = $this->msi->getAllDates($libraryId);
		$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];
		$unloadedService = InstitutionApiService::where('user_id',$user_id)
			->where('loaded',0)->get();

		if($clear == 1)  {

			SearchParameter::where('user_id',$user_id)->delete();
		}

		if($param->first()) {
			$this->msi->insertSearchResultsForDisplay($user_id,$libraryId);
			$masterShelf = MasterShelfResult::where('user_id',$user_id)->where('library_id',$libraryId)->paginate(20);
			$beginningDate = $param[0]->beginningDate;
			$endingDate = $param[0]->endingDate;
			$beginningCallNumber = $param[0]->beginningCallNumber;
			$endingCallNumber = $param[0]->endingCallNumber;
		} else {
			$this->msi->insertSearchResultsForDisplay($user_id,$libraryId);

			$masterShelf = MasterShelfResult::where('user_id',$user_id)->paginate(20);
			$beginningDate = null;
			$endingDate = null;
			$beginningCallNumber = null;
			$endingCallNumber = null;
		}

		$countOfSortSchemes = DB::table('institution_api_services')
			->select('sort_scheme_id')
			->where('user_id',$user_id)
			->distinct('sort_scheme_id')
			->count();
		
		return Inertia::render('MasterShelf/Index', [

			'masterShelf' => $masterShelf,	    
			'beginningDate' => $beginningDate,	    
			'endingDate' => $endingDate,	    
			'beginningCallNumber' => $beginningCallNumber,	    
			'endingCallNumber' => $endingCallNumber,	    
			'allDates' => $allDates,
			'sortSchemeId' => $sortSchemeId,
			'countOfSortSchemes' => $countOfSortSchemes,
			'unloadedService' => $unloadedService,
		]);
	}


	public function searchInventory(InventorySearchParameterRequest $request){

		$beginningDate = $request->beginningDate;
		$endingDate = $request->endingDate;
		$sortSchemeId = $request->sortSchemeId;
		$user_id = Auth::user()->id;
		$libraryId = User::where('id',$user_id)->pluck('library_id')[0];
		$beginningBarcode = $request->beginningBarcode;
		$endingBarcode = $request->endingBarcode;

		if($beginningBarcode) {
			$beginningCallNumber = $this->msi->getCallNumberFromBarcode($beginningBarcode);
			$endingCallNumber = $this->msi->getCallNumberFromBarcode($endingBarcode);
		} else {
			$beginningCallNumber = null;
			$endingCallNumber = null;
		}

		if($request->showAlerts === true)
		{
			$showAlerts = 1;

		} else {

			$showAlerts = 0;
		}


		SearchParameter::where('user_id',$user_id)->delete();

		$sparam = new SearchParameter;
		$sparam->user_id = $user_id;
		$sparam->sort_scheme_id = $request->sortSchemeId;
		$sparam->beginningDate = $beginningDate;
		$sparam->endingDate = $endingDate;
		$sparam->beginningCallNumber = $beginningCallNumber;
		$sparam->endingCallNumber = $endingCallNumber;
		$sparam->alerts = $showAlerts;
		$sparam->save();

		MasterShelfResult::where('user_id',$user_id)->delete();

		$books = $this->msi->insertSearchResultsForDisplay($user_id, $libraryId);

		return Redirect::route('master.shelf',['sortSchemeId' => $sortSchemeId, 'clear' => 0]);
	}

	public function searchCallNumbers(InventorySearchParameterRequest $inventorySearchParameterRequest)
	{
		$user_id = Auth::user()->id;
		$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];

		SearchParameter::where('user_id',$user_id)->delete();

		$sparam = new SearchParameter;
		$sparam->user_id = $user_id;
		$sparam->save();

		return Redirect::route('master.shelf',['sortSchemeId' => 1, 'clear' => 0]);

	}

	public function clearSearch()
	{
		$user_id = Auth::user()->id;
		$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];

		SearchParameter::where('user_id',$user_id)->delete();

		return Redirect::route('master.shelf',['sortSchemeId' => $sortSchemeId, 'clear' => 0]);
	}

	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
