<?php
namespace App\Actions\Maps;

use App\Actions\Maps\GetClassAction;
use App\Actions\Maps\MakeAmaskAction;

class ProcessCallNumberAction
{
	
	##############################
	# Max Call number: G3804 .N4 :2W7 S73  2002 .K8 1999 .B6 
	#                                      s24

	public $amask;
	public $aclass;

	public function __construct(MakeAmaskAction $mask,GetClassAction $class)
	{
		$this->amask = $mask;
		$this->aclass = $class;	

	}

	public function execute($callNumberString) {

		
		$callNumberArray = str_split($callNumberString);
		$smask = str_split($callNumberString);




			$arr = $this->amask->execute($callNumberArray);

		return $this->aclass->execute($callNumberArray);

		

	}
		
}
