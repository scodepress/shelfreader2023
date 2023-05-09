<?php

namespace App\Exports;

use App\Models\MasterShelfResult;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class MasterShelfExport implements FromCollection
{

	public function collection()
	{
		return MasterShelfResult::
			select('barcode','title','call_number','date')
			->where('user_id', Auth::user()->id)
			->groupBy('barcode','title','call_number','date','id')
			->orderBy('id')
			->get();
	}
}
