<?php

namespace App\Contracts\Services\MasterShelf\Services;

use App\Contracts\Services\MasterShelf\MasterShelfInterface;
use App\Models\MasterShelfResult;
use App\Models\SearchParameter;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MasterShelfMaps implements MasterShelfInterface {

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
				'call_number' => $s->callnumber,
				'date' => date('Y-m-d'),
				'class_letter' => $s->class_letter,
				'class_number' => $s->class_number,
				'class_decimal' => $s->class_decimal,
				'topline_dotted_cutter_letter' => $s->topline_dotted_cutter_letter,
				'topline_dotted_cutter_decimal' => $s->topline_dotted_cutter_decimal,
				'neighborhood_cutter' => $s->neighborhood_cutter,
				'first_undotted_cutter_letter' => $s->first_undotted_cutter_letter,
				'first_undotted_cutter_decimal' => $s->first_undotted_cutter_decimal,
				'scale_name' => $s->scale_name,
				'scale_number' => $s->scale_number,
				'publication_date' => $s->publication_date,
				'nextline_dotted_cutter_letter' => $s->nextline_dotted_cutter_letter,
				'nextline_dotted_cutter_decimal' => $s->nextline_dotted_cutter_decimal,
				'specification' => $s->specification,
				'year_of_reproduction' => $s->year_of_reproduction,			
			];

			DB::table('master_shelf_maps')->insert($items);
		}
	}

	public function getNewItemsWithKeys($userId,$libraryId)
	{
		return DB::table('shelves as s')
			->join('map_keys as k','s.barcode','=','k.barcode')
			->select('s.barcode','s.title','k.callnumber','class_letter','class_number','class_decimal',
				'topline_dotted_cutter_letter',
				'topline_dotted_cutter_decimal','neighborhood_cutter','first_undotted_cutter_letter','first_undotted_cutter_decimal',
				'scale_name','scale_number','publication_date','nextline_dotted_cutter_letter','nextline_dotted_cutter_decimal',
				'specification','year_of_reproduction')
				->where('s.user_id', $userId)
				->get();
	}


	public function getSortedItemsFromMasterShelf($userId)
	{
		return DB::table('master_shelf_maps')
			->select('title','call_number','date','barcode')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('topline_dotted_cutter_letter')
			->orderBy('topline_dotted_cutter_decimal')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy("first_undotted_cutter_decimal")
			->orderBy('scale_name')
			->orderBy('scale_number')
			->orderBy('publication_date')
			->orderBy('nextline_dotted_cutter_letter')
			->orderBy('nextline_dotted_cutter_decimal')
			->orderBy("specification")
			->orderBy("year_of_reproduction")
			->orderBy('created_at')
			->where('user_id',$userId)
			->get();
	}


	public function getSortedItemsByCallNumber($userId,$beginningCallNumber,$endingCallNumber)
	{

		return DB::table('master_shelf_maps')
			->select('barcode','title','call_number','date')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('topline_dotted_cutter_letter')
			->orderBy('topline_dotted_cutter_decimal')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy("first_undotted_cutter_decimal")
			->orderBy('scale_name')
			->orderBy('scale_number')
			->orderBy('publication_date')
			->orderBy('nextline_dotted_cutter_letter')
			->orderBy('nextline_dotted_cutter_decimal')
			->orderBy("specification")
			->orderBy("year_of_reproduction")
			->orderBy('created_at')
			->where('user_id',$userId)
			->where('call_number','>=',$beginningCallNumber)
			->where('call_number','<=',$endingCallNumber)
			->get();
	}

	public function getSortedItemsByDateRange($userId,$beginningDate,$endingDate)
	{

		return DB::table('master_shelf_maps')
			->select('barcode','title','call_number','date')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('topline_dotted_cutter_letter')
			->orderBy('topline_dotted_cutter_decimal')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy("first_undotted_cutter_decimal")
			->orderBy('scale_name')
			->orderBy('scale_number')
			->orderBy('publication_date')
			->orderBy('nextline_dotted_cutter_letter')
			->orderBy('nextline_dotted_cutter_decimal')
			->orderBy("specification")
			->orderBy("year_of_reproduction")
			->orderBy('created_at')
			->where('user_id',$userId)
			->where('date','>=',$beginningDate)
			->where('date','<=',$endingDate)
			->get();
	}

	public function getSortedItemsByCallNumberAndDateRange($libraryId,$beginningCallNumber,$endingCallNumber,$beginningDate,$endingDate)
	{
		return DB::table('master_shelf_maps')
			->select('barcode','title','call_number','date')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('topline_dotted_cutter_letter')
			->orderBy('topline_dotted_cutter_decimal')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy("first_undotted_cutter_decimal")
			->orderBy('scale_name')
			->orderBy('scale_number')
			->orderBy('publication_date')
			->orderBy('nextline_dotted_cutter_letter')
			->orderBy('nextline_dotted_cutter_decimal')
			->orderBy("specification")
			->orderBy("year_of_reproduction")
			->orderBy('created_at')
			->where('library_id',$libraryId)
			->where('call_number','>=',$beginningDate)
			->where('call_number','<=',$endingDate)
			->where('date','>=',$beginningDate)
			->where('date','<=',$endingDate)
			->get();
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

		} else {
				$sortSchemeId = Auth::user()->scheme_id;
				Redirect::route('master.shelf',['sortSchemeId' =>$sortSchemeId,'clear' => 1])
					->with('message','There are no records in Maps Inventory.');
			}

		}

		if($sp->first()) {
			if($sp[0]->beginningDate && !$sp[0]->beginningCallNumber) 		{

				// Insert all results in a given date range

				$shelf = $this->getSortedItemsByDateRange($userId,$sp[0]->beginningDate,$sp[0]->endingDate);

			if($shelf->first()) {
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

			} else {
				$sortSchemeId = Auth::user()->scheme_id;
				Redirect::route('master.shelf',['sortSchemeId' =>$sortSchemeId,'clear' => 0])
					->with('message','There are no records in the date range specified.');
			}
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

		return DB::table('master_shelf_maps')
			->select('call_number')
			->orderBy('class_letter')
			->orderBy('class_number')
			->orderBy('class_decimal')
			->orderBy('topline_dotted_cutter_letter')
			->orderBy('topline_dotted_cutter_decimal')
			->orderBy('neighborhood_cutter')
			->orderBy('first_undotted_cutter_letter')
			->orderBy("first_undotted_cutter_decimal")
			->orderBy('scale_name')
			->orderBy('scale_number')
			->orderBy('publication_date')
			->orderBy('nextline_dotted_cutter_letter')
			->orderBy('nextline_dotted_cutter_decimal')
			->orderBy("specification")
			->orderBy("year_of_reproduction")
			->orderBy('created_at')
			->where('library_id',$libraryId)
			->pluck('call_number')[0];
	}

	public function getDefaultEndingCallNumber($libraryId)
	{

		return DB::table('master_shelf_maps')
			->select('call_number')
			->orderByDesc('class_letter')
			->orderByDesc('class_number')
			->orderByDesc('class_decimal')
			->orderByDesc('topline_dotted_cutter_letter')
			->orderByDesc('topline_dotted_cutter_decimal')
			->orderByDesc('neighborhood_cutter')
			->orderByDesc('first_undotted_cutter_letter')
			->orderByDesc("first_undotted_cutter_decimal")
			->orderByDesc('scale_name')
			->orderByDesc('scale_number')
			->orderByDesc('publication_date')
			->orderByDesc('nextline_dotted_cutter_letter')
			->orderByDesc('nextline_dotted_cutter_decimal')
			->orderByDesc("specification")
			->orderByDesc("year_of_reproduction")
			->orderByDesc('created_at')
			->where('library_id',$libraryId)
			->pluck('call_number')[0];
	}

	function getDefaultBeginningDate($libraryId)
	{
		return DB::table('master_shelf_maps')
			->select('date')
			->where('library_id',$libraryId)
			->orderBy('date')
			->first()
			->date;

	}

	function getDefaultEndingDate($libraryId)
	{
		return DB::table('master_shelf_maps')
			->select('date')
			->where('library_id',$libraryId)
			->orderByDesc('date')
			->first()
			->date;

	}

	public function getAllDates($libraryId)
	{

		return DB::table('master_shelf_maps')
			->select('date')
			->where('library_id',$libraryId)
			->groupBy('date')
			->orderByDesc('date')
			->pluck('date');	
	}

	public function getCallNumberFromBarcode($barcode)
	{
		return DB::table('master_shelf_maps')

			->select('call_number')
			->where('barcode',$barcode)
			->first()->call_number;
	}
}

