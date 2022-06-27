<?php

namespace App\Actions\Maps;


class CountCuttersAction
{
	public function execute($callNumberString){

		return substr_count($callNumberString, '.');

	}
}
