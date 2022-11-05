<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use Illuminate\Support\Facades\DB;

class LibraryMapsService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		return DB::table('corrections as c')
			->join('alerts as a','a.library_id','=','c.library_id')
			->join('master_shelf_maps as m','a.library_id','=','m.library_id')
			->where('a.library_id',$libraryId)
			->select('a.barcode')
			->groupBy('a.barcode')
			->count();
	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}


}
