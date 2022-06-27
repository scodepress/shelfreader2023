<?php

namespace App\Http\Controllers;

use App\Models\MasterShelf;
use App\Models\FirstScan;
use Auth;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MasterShelfService;
use App\Exports\MasterShelfExport;
use App\Exports\MasterShelfExportByCallNumber;
use App\Models\OnlineInventoryItem;
use App\Models\SearchParameter;
use App\Models\Shelf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;
use Redirect;
use OnlineInventoryService;

class MasterShelfController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
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

	public function export($beginningDate,$endingDate,$dateFileFormat) 
	{
		if($dateFileFormat === 'csv')
		{
			return Excel::download(new MasterShelfExport($beginningDate,$endingDate), 'inventory.csv');
		} else {

			return Excel::download(new MasterShelfExport($beginningDate,$endingDate), 'inventory.xlsx');
		}

	}

	public function exportByCallNumber($beginningDate,$endingDate,$beginningCallNumber,$endingCallNumber,$callNumberFileFormat) 
	{
		if($callNumberFileFormat === 'csv')
		{
			return Excel::download(new MasterShelfExportByCallNumber($beginningDate,$endingDate,$beginningCallNumber,
				$endingCallNumber), 'inventory.csv');
		} else {

			return Excel::download(new MasterShelfExportByCallNumber($beginningDate,$endingDate,$beginningCallNumber,
				$endingCallNumber), 'inventory.xlsx');
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
	public function show()
	{
		$user_id = Auth::user()->id;

		$param = SearchParameter::where('user_id',$user_id)->get();

		
		$allDates = MasterShelf::where('user_id',$user_id)
			->groupBy('date')
			->orderByDesc('date')
			->pluck('date');

		if(!$param->first())
		{
			$masterShelf = MasterShelfService::orderedMasterShelf();

			$beginningDate = null;
			$endingDate = null;
			$beginningCallNumber = null;
			$endingCallNumber = null;
		}

		elseif($param[0]->beginningDate && $param[0]->beginningCallNumber)
		{
			$beginningDate = $param[0]->beginningDate;
			$endingDate = $param[0]->endingDate;
			$beginningCallNumber = $param[0]->beginningCallNumber;
			$endingCallNumber = $param[0]->endingCallNumber;
			$masterShelf = MasterShelfService::orderedMasterShelfByCallNumberAndDate($beginningCallNumber,$endingCallNumber,
				$beginningDate,$endingDate);

			return Inertia::render('MasterShelf/Index', [

			'masterShelf' => $masterShelf,	    
			'beginningDate' => $beginningDate,	    
			'endingDate' => $endingDate,	    
			'beginningCallNumber' => $beginningCallNumber,	    
			'endingCallNumber' => $endingCallNumber,	    
			'allDates' => $allDates,
		]);

		} elseif($param[0]->beginningCallNumber && !$param[0]->beginningDate)

		{
			$beginningDate = null;
			$endingDate = null;
			$beginningCallNumber = $param[0]->beginningCallNumber;
			$endingCallNumber = $param[0]->endingCallNumber;
			$showAlerts = SearchParameter::where('user_id',$user_id)->pluck('alerts')[0];

			if($showAlerts === 0)
			{
				$masterShelf = MasterShelfService::orderedMasterShelfByCallNumber($beginningCallNumber,$endingCallNumber);
			}
			else 
			{	
				$leftMostNormalizedCallNumber = MasterShelf::where('user_id',Auth::id())
				->where('call_number',$beginningCallNumber)								     
				->pluck('effective_shelving_order')[0];

				$rightMostNormalizedCallNumber = MasterShelf::where('user_id',Auth::id())
				->where('call_number',$endingCallNumber)
				->pluck('effective_shelving_order')[0];

				OnlineInventoryService::createResponseArray($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,$effectiveLocationId);

				$masterShelf = OnlineInventoryItem::where('user_id',$user_id)->get();

			}
		} elseif($param[0]->beginningDate && !$param[0]->beginningCallNumber) 
		{
			$beginningDate = $param[0]->beginningDate;
			$endingDate = $param[0]->endingDate;
			$beginningCallNumber = null;
			$endingCallNumber = null;
			$showAlerts = SearchParameter::where('user_id',$user_id)->pluck('alerts')[0];

			if($showAlerts == 0)
			{
				$masterShelf = MasterShelfService::orderedMasterShelfByDate($beginningDate,$endingDate);
			}
			else 
			{	
				// Get ESI of first and last call numbers for missing item list creation
				
				$masterShelfList = MasterShelfService::orderedMasterShelfByDate($beginningDate,$endingDate);
				$num = count($masterShelfList);
				$scannedBarcodes = MasterShelf::where('user_id',$user_id)
					->where('date','>=',$beginningDate)
					->where('date','<=',$endingDate)
					->pluck('barcode');

				$leftMostNormalizedCallNumber = $masterShelfList->where('position',1)[0]['effective_shelving_order']; 
				$effectiveLocationId = $masterShelfList->where('position',1)[0]['effective_location_id']; 
				$rightMostNormalizedCallNumber = $masterShelfList
					->where('position',$num)[$num-1]['effective_shelving_order'];

				
				OnlineInventoryService::createResponseArray($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,
					$effectiveLocationId);
				
				$masterShelfUnprocessed = OnlineInventoryItem::where('user_id',$user_id)
					->orderBy('effective_shelving_order')
					->get();
				$masterShelf = MasterShelfService::mixedInventoryReport($masterShelfUnprocessed,$scannedBarcodes);

			}
		}
		
		if(!isset($masterShelf))
		{
			$masterShelf = MasterShelfService::orderedMasterShelf();

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
		]);
	}


	public function searchInventory(Request $request){

		$beginningDate = $request->beginningDate;
		$endingDate = $request->endingDate;

		$user_id = Auth::user()->id;
		if($request->showAlerts === true)
		{
			$showAlerts = 1;
		} else {

			$showAlerts = 0;
		}


		SearchParameter::where('user_id',$user_id)->delete();

		$sparam = new SearchParameter;
		$sparam->user_id = $user_id;
		$sparam->beginningDate = $beginningDate;
		$sparam->endingDate = $endingDate;
		$sparam->beginningCallNumber = $request->beginningCallNumber;
		$sparam->endingCallNumber = $request->endingCallNumber;
		$sparam->alerts = $showAlerts;
		$sparam->save();

		return Redirect::route('master.shelf');
	}

	public function searchCallNumbers(Request $request)
	{
		$user_id = Auth::user()->id;

		SearchParameter::where('user_id',$user_id)->delete();

		$sparam = new SearchParameter;
		$sparam->user_id = $user_id;
				$sparam->save();

		return Redirect::route('master.shelf');

	}

	public function clearSearch()
	{
		$user_id = Auth::user()->id;
		SearchParameter::where('user_id',$user_id)->delete();

		return Redirect::route('master.shelf');
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
