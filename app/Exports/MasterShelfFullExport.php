<?php

namespace App\Exports;

use Auth;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class MasterShelfFullExport implements FromCollection
{
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public $sortSchemeId;

	public function __construct($sortSchemeId)
	{
		$this->sortSchemeId = $sortSchemeId;
	}

	public function collection()
	{
		if($this->sortSchemeId == 1)
		{
			return DB::table('master_shelf_lcc')
				->select('barcode','title','call_number','date')
				->where('user_id',Auth::user()->id)
				->groupBy('barcode','title','call_number','date')
				->get();
		} else {
			
			return DB::table('master_shelf_maps')
				->select('barcode','title','call_number','date')
				->where('user_id',Auth::user()->id)
				->groupBy('barcode','title','call_number','date')
				->get();


		}
	}
}
