<?php

namespace App\Exports;

use App\Models\MasterShelf;
use Auth;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use MasterShelfService;

class MasterShelfExportByCallNumber implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
     */
	public $beginningCallNumber;
	public $endingCallNumber;
	public $beginningDate;
	public $endingDate;

	public function __construct($beginningDate,$endingDate,$beginningCallNumber,$endingCallNumber)
	{
		$this->beginningCallNumber = $beginningCallNumber;
		$this->endingCallNumber = $endingCallNumber;
		$this->beginningDate = $beginningDate;
		$this->endingDate = $endingDate;
	}

    public function collection()
    {
	    if($this->beginningCallNumber && $this->beginningDate === 'null')
	    {
		$beginningId = MasterShelf::where('callno',$this->beginningCallNumber)
			->pluck('id')[0];
		$endingId = MasterShelf::where('callno',$this->endingCallNumber)
			->pluck('id')[0];

		return MasterShelf::
			select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('id','>=',$beginningId)
			->where('id','<=',$endingId)
			->orderBy('id')
			->get()
			->unique('barcode');

	    } elseif($this->beginningDate && !$this->beginningCallNumber)
	    {
		     
		return MasterShelf::
			select('barcode','title','callno','effective_location_name','date')
			->where('user_id',Auth::id())
			->where('date','>=',$this->beginningDate)
			->where('date','<=',$this->endingDate)
			->orderBy('id')
			->get()
			->unique('barcode','title');

	    } elseif($this->beginningDate && $this->beginningCallNumber)
	    { 
		$beginningEffectiveShelvingOrder = MasterShelf::where('callno',$this->beginningCallNumber)
			->pluck('id')[0];
		$endingEffectiveShelvingOrder = MasterShelf::where('callno',$this->endingCallNumber)
			->pluck('id')[0];

		$masterShelf = DB::table('master_shelves')
			->select('barcode','title','callno','date')
			->where('user_id',Auth::id())
			->where('id','>=',$beginningEffectiveShelvingOrder)
			->where('id','<=',$endingEffectiveShelvingOrder)
			->where('date','>=', $this->beginningDate)
			->where('date','<=', $this->endingDate)
			->orderBy('id')
			->get()
			->unique('barcode');

	    }

	    return $masterShelf;
    }
}
