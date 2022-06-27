<?php
namespace App\Actions\Maps;


class GetClassAction
{

	public static function execute($smask) {
		$mapClass = '';
		foreach ($smask as $key=>$cs) {
			if($key === 0) { continue; }
			if (is_numeric($cs)) {

				$mapClass .= $cs;

			} else {

				return $mapClass;


			}

		}

		return $mapClass;
	}
}
