
<?php

namespace App\Contracts\Services\Library;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Correction;
use Illuminate\Support\Facades\DB;

class LibraryMapsService implements LibraryInterface {

	public function getTotalScanCount($libraryId)
	{
		return DB::table('corrections as c')
			->join('alerts as a','library_id','=',$libraryId)
			->join('master_shelf_maps as m','library_id','=',$libraryId)
			->select('barcode')
			->groupBy('barcode')
			->count();
	}


	public function getCorrectionCount($libraryId)
	{
		return Correction::where('library_id',$libraryId)->count();
	}


}
