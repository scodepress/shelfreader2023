<?php

namespace App\Services\MasterShelf;

use App\Models\MasterShelf;
use App\Models\Shelf;
use Auth;
use DB;

class MasterShelfService {


	public function masterShelfArray($masterShelf)
	{

			$e = 1;
			foreach($masterShelf as $key=>$m)
			{
				$masterShelf[$key] = [

					'position' => $e,
					'barcode' => $m->barcode,
					'title' => $m->title,
					'callno' => $m->callno,
					'date' => $m->date

				];

				$e++;
			}

			//$sortSchemeId = InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->pluck('sort_scheme_id')[0];
			return $masterShelf;
	}

	public function mixedInventoryReport($allItemsInRange,$scannedBarcodes)
	{
		$e = 1;
		$onShelf = null;

			foreach($allItemsInRange as $key=>$m)
			{
				if(in_array($m->barcode,$scannedBarcodes->toArray()))
				{
					$onShelf = 1;

				} else {

					$onShelf = 0;
				}

				$masterShelf[$key] = [

					'position' => $e,
					'barcode' => $m->barcode,
					'title' => $m->title,
					'callno' => $m->callno,
					'date' => $m->date,
					'onShelf' => $onShelf,

				];

				$e++;
			}

			return $masterShelf;
	}

	public function orderedMasterShelf()
	{
		$masterShelf = DB::table('master_shelves')

			->select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->orderBy('id')
			->get()
			->unique('barcode');

		if($masterShelf)
		{
			return $this->masterShelfArray($masterShelf);
		} else {

			return false;
		}
	}

	public function orderedMasterShelfByDate($beginningDate,$endingDate){
	
		$masterShelf = DB::table('master_shelves')

			->select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('date','>=',$beginningDate)
			->where('date','<=',$endingDate)
			->orderBy('id')
			->get()
			->unique('barcode');

		if($masterShelf)
		{
			return $this->masterShelfArray($masterShelf);
		} else {

			return false;
		}
	}

	public function orderedMasterShelfByCallNumber($beginningCallNumber,$endingCallNumber){
	
		$beginningId = MasterShelf::where('callno',$beginningCallNumber)
			->pluck('id')[0];
		$endingId = MasterShelf::where('callno',$endingCallNumber)
			->pluck('id')[0];
			
		$masterShelf = DB::table('master_shelves')

			->select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('id','>=',$beginningId)
			->where('id','<=',$endingId)
			->orderBy('id')
			->get()
			->unique('barcode');

		if($masterShelf)
		{
			return $this->masterShelfArray($masterShelf);

		} else {

			return false;
		}
	}

	public function orderedMasterShelfByCallNumberAndDate($beginningCallNumber,$endingCallNumber,$beginningDate,$endingDate){
		//dd($beginningCallNumber);
		$beginningId = MasterShelf::where('callno',$beginningCallNumber)
			->pluck('id')[0];
		$endingId = MasterShelf::where('callno',$endingCallNumber)
			->pluck('id')[0];
			
		$masterShelf = DB::table('master_shelves')

			->select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('id','>=',$beginningId)
			->where('id','<=',$endingId)
			->where('date','>=', $beginningDate)
			->where('date','<=', $endingDate)
			->orderBy('id')
			->get()
			->unique('barcode');

		if($masterShelf)
		{
			return $this->masterShelfArray($masterShelf);

		} else {

			return false;
		}
	}

	public function getShelfWithSortKeys($userId)
	{
		return DB::table('shelves as s')
			->join('sort_keys as k','s.barcode','=','k.barcode')
			->select('s.barcode','s.title','k.callno','prefix','tp1','tp2','pre_date','pvn','pvl','cutter','pcd','cutter_date','inline_cutter','inline_cutter_decimal',
			'cutter_date2','cutter2','pcd2','part1')
			->where('s.user_id', $userId)
			->get();

	}

	public function insertShelfContents($userId,$libraryId)
	{
		$shelf = $this->getShelfWithSortKeys($userId);

		foreach($shelf as $s)
		{
			$items[] = [

				'user_id' => $userId,
				'library_id' => $libraryId,
				'barcode' => $s->barcode,
				'title' => $s->title,
				'callno' => $s->callno,
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

		DB::table('master_shelves')->insert($items);

		$this->sortUsersMasterShelfByCallNumber($userId,$libraryId);
	}

	public function sortUsersMasterShelfByCallNumber($userId,$libraryId)
	{

		$sortedItems = DB::table('master_shelves')
			->select('user_id','library_id','barcode','title','callno','prefix','tp1','tp2','pre_date','pvn','pvl','cutter','pcd','cutter_date','inline_cutter',
			'inline_cutter_decimal','cutter_date2','cutter2','pcd2','part1')
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
			->get();

		MasterShelf::where('user_id',$userId)->delete();

		foreach($sortedItems as $s)
		{
			$items[] = [

				'user_id' => $userId,
				'library_id' => $libraryId,
				'barcode' => $s->barcode,
				'title' => $s->title,
				'callno' => $s->callno,
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

		DB::table('master_shelves')->insert($items);
	}
}
