<?php

namespace App\Contracts\Services\Maps;


interface MapsKeyInterface
{
	public function index($callnumber,$barcode);
}
