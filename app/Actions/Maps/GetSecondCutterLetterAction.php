<?php
namespace App\Actions\Maps;


class GetSecondCutterLetterAction
{
	public function __construct()
	{
		

	}

	public static function execute($tmask,$depthOfSecondCutter) {
		$cutterLetter = false;
		foreach ($tmask as $key=>$t) {
			if(ctype_alpha($t)) {
				$cutterLetter .= $t;
			} else {
				return $cutterLetter;
			}
			
		}
		return $cutterLetter;
		
	}
}
