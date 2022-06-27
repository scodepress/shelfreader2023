<?php
namespace App\Actions\Maps;


class GetConcurrentNumberStringAction

{
	public function __construct()
	{


	}

	public static function execute($smask,$depth) {
		$firstCutterDecimal = false;
		$tmask = array_slice($smask,$depth);

		foreach ($tmask as $key=>$s) {
			if(is_numeric($s)) {
				$firstCutterDecimal .= $s;
			}  else {

				return $firstCutterDecimal;
			}

		}
		return $firstCutterDecimal;
	}
}
