<?php
namespace App\Actions\Maps;

class GetClassDecimalAction
{
	public function __construct()
	{


	}

	public static function execute($tmask,$astring) {
		if($tmask[0] != '.') { return false; }
		$classDecimal = null;
		foreach ($tmask as $key=>$s) {
			if($s === '.') { continue; }
			if (is_numeric($s)) {
				$classDecimal .= $s;
			} else {

				return $classDecimal;
			}


		}

		return $classDecimal;
	}

}
