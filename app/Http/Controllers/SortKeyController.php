<?php

namespace App\Http\Controllers;

use App\Contracts\Services\SortKey\SortKeysInterface;

class SortKeyController extends Controller
{

	public function __construct()
	{

	}

	public function index($callNumber,$barcode)
	{
		return $this->sortKeysInterface->index($callNumber,$barcode);

	}


}
