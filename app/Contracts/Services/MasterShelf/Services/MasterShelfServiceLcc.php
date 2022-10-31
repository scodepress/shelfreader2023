<?php

namespace App\Contracts\Services\MasterShelf\Services;

use App\Contracts\Services\MasterShelf\MasterShelfInterface;
use App\Models\MasterShelfResult;
use App\Models\SearchParameter;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MasterShelfServiceLcc implements MasterShelfInterface {

	public function insertNewItems($userId,$libraryId) 
	{
		$shelf = $this->getNewItemsWithKeys($userId,$libraryId);

		foreach($shelf as $s)
		{
			$items[] = [

				'user_id' => $userId,
				'library_id' => $libraryId,
				'barcode' => $s->barcode,
				'title' => $s->title,
				'call_number' => $s->callno,
				'date' => date('Y-m-d'),
				'prefix' => $s->prefix,
				'tp1' => $s->tp1,
				'tp2' => $s->tp2,
				'pre_date' => $s->pre_date,
				'pvn' => $s->pvn,
				'pvl' => $s->pvl,
				'cutter' => $s->cutter,
				'pcd' => $s->pcd,
				'cutter_date' => $s->cutter_date,
				'inline_cutter' => $s->inline_cutter,
				'inline_cutter_decimal' => $s->inline_cutter_decimal,
				'cutter_date2' => $s->cutter_date2,
				'cutter2' => $s->cutter2,
				'pcd2' => $s->pcd2,
				'part1' => $s->part1,

			];

		}
		DB::table('master_shelf_lcc')->insert($items);
	}

	public function getNewItemsWithKeys($userId,$libraryId)
	{
		return DB::table('shelves as s')
			->join('sort_keys as k','s.barcode','=','k.barcode')
			->select('s.barcode','s.title','k.callno','prefix','tp1','tp2','pre_date','pvn','pvl','cutter','pcd','cutter_date',
				'inline_cutter','inline_cutter_decimal','cutter_date2','cutter2','pcd2','part1')
				->where('s.user_id', $userId)
				->get();
	}


	public function getSortedItemsFromMasterShelf($userId)
	{

		return DB::table('master_shelf_lcc')
			->select('barcode','title','call_number','date')
			->where('user_id',$userId)
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy('created_at')
			->get()->unique('barcode');
	}

	public function getSortedItemsByCallNumber($userId,$beginningCallNumber,$endingCallNumber)
	{

		return DB::table('master_shelf_lcc')
			->select('barcode','title','call_number','date')
			->where('user_id',$userId)
			->where('callnumber','>=',$beginningCallNumber)
			->where('callnumber','<=',$endingCallNumber)
			->groupBy('barcode')
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy('created_at')
			->get()->unique('barcode');
	}

	public function getSortedItemsByDateRange($userId,$beginningDate,$endingDate)
	{

		return DB::table('master_shelf_lcc')
			->select('barcode','title','call_number','date')
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy('created_at')
			->where('user_id',$userId)
			->where('date','>=',$beginningDate)
			->where('date','<=',$endingDate)
			->get()->unique('barcode');
	}

	public function getSortedItemsByCallNumberAndDateRange($userId,$beginningCallNumber,$endingCallNumber,$beginningDate,$endingDate)
	{
		return DB::table('master_shelf_lcc')
			->select('barcode','title','call_number','date')
			->where('user_id',$userId)
			->where('call_number','>=',$beginningCallNumber)
			->where('call_number','<=',$endingCallNumber)
			->where('date','>=',$beginningDate)
			->where('date','<=',$endingDate)
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy('created_at')
			->get()->unique('barcode');
	}

	public function getRangeByItemIds($libraryId,$beginningItemId,$endingItemId)
	{
		return DB::table('master_shelf_results')
			->select('barcode','title','call_number','date')
			->where('library_id',$libraryId)
			->where('id','>=',$beginningItemId)
			->where('id','<=',$endingItemId)
			->get();
	}

	public function getIdOfFirstCallNumber($libraryId,$beginningCallNumber)
	{
		return MasterShelfResult::where('call_number', $beginningCallNumber)
			->where('library_id',$libraryId)
			->pluck('id')[0];
	}

	public function getIdOfLastCallNumber($libraryId,$endingCallNumber)
	{
		return MasterShelfResult::where('call_number',$endingCallNumber)
			->where('library_id',$libraryId)
			->pluck('id')[0];
	}

	public function insertSearchResultsForDisplay($userId,$libraryId)
	{
		$sp = SearchParameter::where('user_id',$userId)->get();

		if(!$sp->first()) {

			// Insert all results

			$shelf = $this->getSortedItemsFromMasterShelf($userId);
			if($shelf->first()) {
			$items = null;
			foreach($shelf as $s)
			{
				$items[] = [
					'user_id' => $userId,
					'library_id' => $libraryId,
					'barcode' => $s->barcode,
					'title' => $s->title,
					'call_number' => $s->call_number,
					'date' => $s->date,
				];

			}

			MasterShelfResult::where('user_id',$userId)->delete();
			DB::table('master_shelf_results')->insert($items);

			return;
			} else {
				$sortSchemeId = Auth::user()->scheme_id;
				Redirect::route('master.shelf',['sortSchemeId' =>$sortSchemeId])
					->with('message','There are no records in Inventory.');
			}
		}

		if($sp->first()) {
			if($sp[0]->beginningDate && !$sp[0]->beginningCallNumber) 		{

				// Insert all results in a given date range

				$shelf = $this->getSortedItemsByDateRange($userId,$sp[0]->beginningDate,$sp[0]->endingDate);

				$items = null;
				foreach($shelf as $s)
				{
					$items[] = [
						'user_id' => $userId,
						'library_id' => $libraryId,
						'barcode' => $s->barcode,
						'title' => $s->title,
						'call_number' => $s->call_number,
						'date' => $s->date,
					];

				}

				MasterShelfResult::where('user_id',$userId)->delete();
				DB::table('master_shelf_results')->insert($items);
				return;
			}

			if(!$sp[0]->beginningDate && $sp[0]->beginningCallNumber) 		
			{

				// Insert all results in the given call number range
				$shelf = $this->getSortedItemsFromMasterShelf($userId);

				$items = null;
				foreach($shelf as $s)
				{
					$items[] = [
						'user_id' => $userId,
						'library_id' => $libraryId,
						'barcode' => $s->barcode,
						'title' => $s->title,
						'call_number' => $s->call_number,
						'date' => $s->date,
					];

				}

				MasterShelfResult::where('user_id',$userId)->delete();
				DB::table('master_shelf_results')->insert($items);

				// find id of call numbers in results table
				$bcall = $this->getIdOfFirstCallNumber($libraryId,$sp[0]->beginningCallNumber);
				$ecall = $this->getIdOfLastCallNumber($libraryId,$sp[0]->endingCallNumber);

				$newShelf = $this->getRangeByItemIds($libraryId,$bcall,$ecall);
				MasterShelfResult::where('user_id',$userId)->delete();

				$items = null;
				foreach($newShelf as $s)
				{
					$items[] = [
						'user_id' => $userId,
						'library_id' => $libraryId,
						'barcode' => $s->barcode,
						'title' => $s->title,
						'call_number' => $s->call_number,
						'date' => $s->date,
					];

				}

				DB::table('master_shelf_results')->insert($items);

				return;
			}

			if($sp[0]->beginningDate && $sp[0]->beginningCallNumber) 		{

				// Insert all results in date and call number range

				$shelf = $this->getSortedItemsByCallNumberAndDateRange(
					$userId,
					$sp[0]->beginningCallNumber,
					$sp[0]->endingCallNumber,
					$sp[0]->beginningDate,
					$sp[0]->endingDate);

				$items = null;
				foreach($shelf as $s)
				{
					$items[] = [
						'user_id' => $userId,
						'library_id' => $libraryId,
						'barcode' => $s->barcode,
						'title' => $s->title,
						'call_number' => $s->call_number,
						'date' => $s->date,
					];

				}

				MasterShelfResult::where('user_id',$userId)->delete();

				DB::table('master_shelf_results')->insert($items);
				return;
			}
		}
	}

	public function getDefaultBeginningCallNumber($libraryId)
	{

		return DB::table('master_shelf_lcc')
			->select('call_number')
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy("pcd")
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy("pcd2")
			->orderBy("part1")
			->orderBy('created_at')
			->where('library_id',$libraryId)
			->pluck('call_number')[0];
	}

	public function getDefaultEndingCallNumber($libraryId)
	{
		return DB::table('master_shelf_lcc')
			->select('call_number')
			->orderByDesc('prefix')
			->orderByDesc('tp1')
			->orderByDesc('tp2')
			->orderByDesc('pre_date')
			->orderByDesc('pvn')
			->orderByDesc('pvl')
			->orderByDesc('cutter')
			->orderByDesc("pcd")
			->orderByDesc('cutter_date')
			->orderByDesc('inline_cutter')
			->orderByDesc('inline_cutter_decimal')
			->orderByDesc('cutter_date2')
			->orderByDesc('cutter2')
			->orderByDesc("pcd2")
			->orderByDesc("part1")
			->orderByDesc('created_at')
			->where('library_id',$libraryId)
			->pluck('call_number')[0];
	}

	function getDefaultBeginningDate($libraryId)
	{
		return DB::table('master_shelf_lcc')
			->select('date')
			->where('library_id',$libraryId)
			->orderBy('date')
			->first()
			->date;

	}

	function getDefaultEndingDate($libraryId)
	{
		return DB::table('master_shelf_lcc')
			->select('date')
			->where('library_id',$libraryId)
			->orderByDesc('date')
			->first()
			->date;

	}

	public function getAllDates($libraryId)
	{

		return DB::table('master_shelf_lcc')
			->where('library_id',$libraryId)
			->groupBy('date')
			->orderByDesc('date')
			->pluck('date');	
	}

	public function getCallNumberFromBarcode($barcode)
	{
		return DB::table('master_shelf_lcc')
			->select('call_number')
			->where('barcode',$barcode)
			->first()->call_number;
	}
}
