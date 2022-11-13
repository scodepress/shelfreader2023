<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use App\Models\User;
use App\Models\UsageCount;
use Illuminate\Support\Facades\DB;

class LibraryLccService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		$alertsCount = DB::table('alerts')
			->where('library_id',$libraryId)
			->select('barcode')
			->count();
		$masterShelfLccCount = DB::table('master_shelf_lcc')
			->where('library_id',$libraryId)
			->select('barcode')
			->count();

		$oldUsageCountByLibrary = $this->getOldUsageCountByLibrary($libraryId);

		return $alertsCount + $masterShelfLccCount + $oldUsageCountByLibrary;
	}

	public function getTotalScanCountByUser($libraryId)
	{
		$libraryWorkers = User::where('library_id',$libraryId)->get();

		foreach($libraryWorkers as $key=>$lw)
		{
			$alertsCount = DB::table('alerts')
				->where('user_id',$lw->id)
				->select('barcode')
				->count();
			$masterShelfLccCount = DB::table('master_shelf_lcc')
				->where('user_id',$lw->id)
				->select('barcode')
				->count();

			$oldUsageCount = $this->getOldUsageCountByUser($lw->id);

			$userTotals = $alertsCount + $masterShelfLccCount + $oldUsageCount;

			$userCorrectionCount = $this->getUserCorrectionCount($lw->id);
			$lineNumber = $key+1;
			$userCount[] = "$lineNumber. $lw->name scanned $userTotals items and made $userCorrectionCount corrections.";
		}

		return $userCount;

	}

	public function totalNumberOfUsers($libraryId){

		User::where('library_id',$libraryId)->count();

	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}

	public function getUserCorrectionCount($userId)
	{
		return Correction::where('user_id',$userId)->select('barcode')->count();
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
}
