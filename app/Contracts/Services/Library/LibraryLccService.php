<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use Illuminate\Support\Facades\DB;

class LibraryLccService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		$masterShelfCount = DB::table('masterShelfCount')
			->where('libraryId',$libraryId)
			->select('barcode')
			->count();
		$alertsCount = DB::table('alerts')
			->where('libraryId',$libraryId)
			->select('barcode')
			->count();

		return $masterShelfCount + $alertsCount;

	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}


}
