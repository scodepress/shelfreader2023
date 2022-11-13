<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use App\Models\User;
use App\Models\UsageCount;
use Illuminate\Support\Facades\DB;

class LibraryMapsService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		$masterShelfMapsCount = DB::table('master_shelf_maps')
			->where('library_id',$libraryId)
			->select('barcode')
			->count();
		$alertsCount = DB::table('alerts')
			->where('library_id',$libraryId)
			->select('barcode')
			->count();

		return $masterShelfMapsCount + $alertsCount;
	}

	public function getTotalScanCountByUser($libraryId)
	{ 
		$libraryWorkers = User::where('library_id',$libraryId)->get();

		foreach($libraryWorkers as $key=>$lw)
		{
			$masterShelfMapsCount = DB::table('master_shelf_maps')
				->where('user_id',$lw->id)
				->select('barcode')
				->count();
			$alertsCount = DB::table('alerts')
				->where('user_id',$lw->id)
				->where('sort_scheme_id',2)
				->select('barcode')
				->count();
			$masterShelfLccCount = DB::table('master_shelf_lcc')
				->where('user_id',$lw->id)
				->select('barcode')
				->count();

			$userTotals = $masterShelfMapsCount + $alertsCount + $masterShelfLccCount;

			$userCorrectionCount = $this->getUserCorrectionCount($lw->id);
			$lineNumber = $key+1;
			$userCount[] = "$lineNumber. $lw->name scanned $userTotals items and made $userCorrectionCount corrections.";
		}

		return $userCount;

	}

	public function totalNumberOfUsers($libraryId){

		User::where('library_id',$libraryId)->count();

	}

	public function getUserCorrectionCount($userId)
	{
		return Correction::where('user_id',$userId)
			->where('sort_scheme_id',2)
			->select('barcode')->count();
	}

	public function getOldUsageCountByLibrary($libraryId)
	{
		$ucount = UsageCount::where('library_id',$libraryId)->get();

		if($ucount->first())
		{
			return UsageCount::where('library_id',$libraryId)->pluck('count')[0];
		} else {

			return 0;
		}

	}

	public function getOldUsageCountByUser($userId)
	{
		$ucount = UsageCount::where('user_id',$userId)->get();

		if($ucount->first())
		{
			return UsageCount::where('user_id',$userId)->pluck('count')[0];
		} else {

			return 0;
		}

	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)
			->where('sort_scheme_id',2)
			->count();
	}


}
