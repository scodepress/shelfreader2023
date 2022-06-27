<?php

namespace App\Http\Controllers;

use App\Models\InstitutionApiService;
use App\Traits\FolioTrait;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Contracts\Services\Api\ApiServicesInterface;

class RequestHubController extends Controller
{
	use FolioTrait;

	public $apiServicesInterface;

	public function __construct(ApiServicesInterface $apiServicesInterface)
	{
		$this->apiServicesInterface =  $apiServicesInterface;	
	}

	public function incomingRequest(Request $request)
	{
		$user_id = $request->user()->id;
		$service_id = $request->service;
		$barcode = $request->barcode; 

		$sortSchemeId = InstitutionApiService::where('user_id',$user_id)->where('loaded',1)->pluck('sort_scheme_id')[0];
		

		// Get Api response

		if($service_id == 'inventory')  {
		
			return Redirect::route('process-folio-inventory',['barcode'=>$barcode,'user_id'=>$user_id,'service'=>$service_id]);
		} 

		if($service_id === 1) {

			return Redirect::route('process-alma',['barcode'=>$barcode,'user_id'=>$user_id]);

		} elseif($service_id === 3) {
		
			$apiResponse = $this->apiServicesInterface->index($barcode);
 
			$callNumber = $this->parseFolioResponse($apiResponse)['callNumber'];
			$title = stripslashes($this->parseFolioResponse($apiResponse)['title']);
			$status = $this->parseFolioResponse($apiResponse)['status'];


		} else {
		
			return Redirect::route('check_book',['barcode'=>$barcode,'user_id'=>$user_id,'service'=>$service_id]);
		}

	}

}
