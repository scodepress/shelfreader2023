<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Api\ApiServicesInterface;
use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Models\Shelf;
use App\Models\ShelfCandidate;
use App\Models\Status;
use App\Services\RequestHandling\FolioRequests;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use App\Traits\BookShelfTrait;
use App\Traits\FolioTrait;

class ShelfCandidateController extends Controller
{
	use BookShelfTrait;
	use FolioTrait;

	public $sortKeysInterface;
	public $apiServicesInterface;

	public function __construct(SortKeysInterface $sortKeysInterface, ApiServicesInterface $apiServicesInterface)
	{
		$this->sortKeysInterface = $sortKeysInterface;
		$this->apiServicesInterface  = $apiServicesInterface;
	}

	public function index()
	{
		//
	}

	public function create()
	{
		//
	}

	public function saveCandidateToShelf(Request $request)
	{
		$candidate = ShelfCandidate::where('user_id',Auth::id())->where('barcode',$request->barcode)->get();
		$callNumber = $candidate[0]->callnumber;
		$title = $candidate[0]->title;
		$status = $candidate[0]->status;
		$effectiveShelvingOrder = $candidate[0]->effective_shelving_order;
		$effectiveLocationId = $candidate[0]->effective_location_id;
		$effectiveLocationName = $candidate[0]->effective_location_name;


		$cposition = $this->sortKeysInterface->index($callNumber,$request->barcode);
		$initial_shelf_position = Shelf::where('user_id',Auth::id())->count()+1;

		//Do testing here before putting in db

		$bk = new Shelf;

		$bk->scan_order = $initial_shelf_position;
		$bk->user_id = Auth::id();
		$bk->callnumber = $callNumber;
		$bk->barcode = $request->barcode;
		$bk->title = $title;
		$bk->shelf_position = $initial_shelf_position;
		$bk->correct_position = $cposition;
		$bk->status = $status;
		$bk->effective_shelving_order = $effectiveShelvingOrder;
		$bk->effective_location_id = $effectiveLocationId;
		$bk->effective_location_name = $effectiveLocationName;
		$bk->save();

		Shelf::resetCpositions($request->barcode,$cposition,Auth::id());

		$user_id = Auth::id();

		if($this->countShelfErrors($user_id)>0) {

			$this->fillSubsequence($user_id);
			$this->fillMoves($user_id);
		}

		return Redirect::route('shelf');


	}

	public function store($callNumber,$barcode,$title,$status,$effectiveShelvingOrder,$effectiveLocationId,$effectiveLocationName)
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



	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
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
	public function destroy($barcode)
	{
		ShelfCandidate::user()->where('barcode',$barcode)->delete() ;

		return Redirect::route('shelf');
	}

}
