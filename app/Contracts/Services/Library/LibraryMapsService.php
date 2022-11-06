<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
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
		$masterShelfLccCount = DB::table('master_shelf_lcc')
			->where('library_id',$libraryId)
			->select('barcode')
			->count();

		return $masterShelfMapsCount + $alertsCount + $masterShelfLccCount;
	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}


}
