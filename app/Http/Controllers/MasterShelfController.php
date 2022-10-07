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
use App\Models\MasterShelfResult;
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
	public function show($sortSchemeId)
	{
		$user_id = Auth::user()->id;
		$libraryId = User::where('id',$user_id)->pluck('library_id')[0];
		$param = SearchParameter::where('user_id',$user_id)->get();
		$allDates = $this->msi->getAllDates($libraryId);


		if($param->first()) {
			$this->msi->insertSearchResultsForDisplay($user_id,$libraryId);
			$masterShelf = MasterShelfResult::where('user_id',$user_id)->where('library_id',$libraryId)->paginate(20);
			$beginningDate = $param[0]->beginningDate;
			$endingDate = $param[0]->endingDate;
			$beginningCallNumber = $param[0]->beginningCallNumber;
			$endingCallNumber = $param[0]->endingCallNumber;
		} else {
			$r=$this->msi->insertSearchResultsForDisplay($user_id,$libraryId);

			$masterShelf = MasterShelfResult::where('user_id',$user_id)->paginate(20);
			$beginningDate = null;
			$endingDate = null;
			$beginningCallNumber = null;
			$endingCallNumber = null;
		}

		
		return Inertia::render('MasterShelf/Index', [

			'masterShelf' => $masterShelf,	    
			'beginningDate' => $beginningDate,	    
			'endingDate' => $endingDate,	    
			'beginningCallNumber' => $beginningCallNumber,	    
			'endingCallNumber' => $endingCallNumber,	    
			'allDates' => $allDates,
			'sortSchemeId' => $sortSchemeId,
		]);
	}


	public function searchInventory(Request $request){

		$beginningDate = $request->beginningDate;
		$endingDate = $request->endingDate;
		$sortSchemeId = $request->sortSchemeId;
		$user_id = Auth::user()->id;
		$libraryId = User::where('id',$user_id)->pluck('library_id')[0];

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
		$sparam->beginningCallNumber = $request->beginningCallNumber;
		$sparam->endingCallNumber = $request->endingCallNumber;
		$sparam->alerts = $showAlerts;
		$sparam->save();

		MasterShelfResult::where('user_id',$user_id)->delete();

		$books = $this->msi->insertSearchResultsForDisplay($user_id, $libraryId);

		return Redirect::route('master.shelf',['sortSchemeId' => $sortSchemeId]);
	}

	public function searchCallNumbers(Request $request)
	{
		$user_id = Auth::user()->id;
		$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];

		SearchParameter::where('user_id',$user_id)->delete();

		$sparam = new SearchParameter;
		$sparam->user_id = $user_id;
		$sparam->save();

		return Redirect::route('master.shelf',['sortSchemeId' => 1]);

	}

	public function clearSearch()
	{
		$user_id = Auth::user()->id;
		$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];

		SearchParameter::where('user_id',$user_id)->delete();

		return Redirect::route('master.shelf',['sortSchemeId' => $sortSchemeId]);
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
