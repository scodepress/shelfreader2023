<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Api\ApiServicesInterface;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
	private $apiServicesInterface;

	public function __construct(ApiServicesInterface $apiServicesInterface)
	{
		$this->apiServicesInterface =  $apiServicesInterface;	
	}

	public function index($barcode)
	{
		return $barcode;
	}
}
