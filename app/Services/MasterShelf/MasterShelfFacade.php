<?php

namespace App\Services\MasterShelf;

use Illuminate\Support\Facades\Facade;

class MasterShelfFacade extends Facade {

	public static function getFacadeAccessor()
	{
		return 'mastershelf';

	}
}
