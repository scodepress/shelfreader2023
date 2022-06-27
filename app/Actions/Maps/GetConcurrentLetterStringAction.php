<?php
namespace App\Actions\Maps;


class GetConcurrentLetterStringAction
{
	public function __construct()
	{
		

	}

	public static function execute($smask,$depth) {
		$cutterLetter = false;
		$tmask = array_slice($smask,$depth);
		foreach ($tmask as $key=>$t) {
			if($key === 0 AND $t === '.') { continue; }
			if(ctype_alpha($t)) {
				$cutterLetter .= $t;
			} else {
				return $cutterLetter;
			}
			
		}
		return $cutterLetter;
		
	}
}
