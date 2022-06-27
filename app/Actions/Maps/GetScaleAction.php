<?php
namespace App\Actions\Maps;


class GetScaleAction
{
	public function __construct()
	{
		

	}

	
	public static function execute($tmask) {
		$scale = false;
		foreach ($tmask as $key=>$t) {
			if($key === 0 AND $t === 's') {
				continue;
			}
			if(ctype_digit($t)) {
				$scale .= $t;
			} else {
				return $scale;
			}
			
		}
		return $scale;
		
	}
}
