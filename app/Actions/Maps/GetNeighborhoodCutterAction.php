<?php
namespace App\Actions\Maps;


class GetNeighborhoodCutterAction
{
	public function __construct()
	{


	}


	public static function execute($tmask, $depth) {
		if($depth != false) {
			$neighborhoodCutter = false;
			foreach ($tmask as $key=>$s) {

				if ($key >= 0  AND $key <= 2) {
					$neighborhoodCutter .= $s;
				} elseif($key > 2) {

					return $neighborhoodCutter;
				}


			}

			return $neighborhoodCutter;

		} else {

		}


	}
}
