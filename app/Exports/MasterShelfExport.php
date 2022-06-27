<?php

namespace App\Exports;

use App\Models\MasterShelf;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class MasterShelfExport implements FromCollection
{
	public $beginningDate;
	public $endingDate;

	public function __construct($beginningDate,$endingDate)
	{
		$this->beginningDate = $beginningDate;
		$this->endingDate = $endingDate;
	}

	public function collection()
	{
		return MasterShelf::
			select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('date','>=',$this->beginningDate)
			->where('date','<=',$this->endingDate)
			->orderBy('id')
			->get()
			->unique('barcode','title');
	}
}
