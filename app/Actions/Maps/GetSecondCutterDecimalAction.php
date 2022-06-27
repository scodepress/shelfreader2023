<?php
namespace App\Actions\Maps;


class GetSecondCutterDecimalAction
{
	public function __construct()
	{
		

	}

	public static function execute($tmask,$depthOfSecondCutterDecimal) {
		$secondCutterDecimal = false;
		foreach ($tmask as $key=>$t) {
			if(ctype_digit($t)) {
				$secondCutterDecimal .= $t;
			} else {
				return $secondCutterDecimal;
			}
			
		}
		return $secondCutterDecimal;
		
	}
}
