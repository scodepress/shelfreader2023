<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use Illuminate\Support\Facades\DB;

class LibraryLccService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		return DB::table('master_shelf_lcc as m')
			->join('alerts as a','a.library_id','=','m.library_id')
			->join('corrections as c','c.library_id','=','m.library_id')
			->select('m.barcode')
			->where('m.library_id',$libraryId)
			->groupBy('m.barcode')
			->count();
	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}


}
