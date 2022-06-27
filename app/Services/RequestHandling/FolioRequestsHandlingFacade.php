<?php

namespace App\Services\RequestHandling;

use Illuminate\Support\Facades\Facade;

class FolioRequestsHandlingFacade extends Facade {

	public static function getFacadeAccessor()
	{
		return 'requestshandling';

	}
} 
