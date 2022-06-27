<?php

namespace App\Services\Api\Alma;

use App\Contracts\Services\Api\ApiServicesInterface;
use Illuminate\Support\Facades\Http;

class AlmaApiService implements ApiServicesInterface
{

	public function index($barcode)
	{

		$response = Http::withHeaders([

			"Content-Type" => "application/json",
			"Accept" => "application/json",
		])
			
		->get("https://api-na.hosted.exlibrisgroup.com/almaws/v1/items?item_barcode=$barcode&apikey=l8xx829eab89208a4bfd995656c6cbe4c08c");
		$response = $response->getBody()->getContents();

		$response = collect(json_decode($response, true));

		 return $response;

	}

	public function itemParameters($barcode)
	{
		$response = $this->index($barcode);
		//dd($response);
		$title = $response['bib_data']['title'];
		$call_number = $response['holding_data']['call_number'];
		$status = $response['item_data']['base_status']['desc'];
		$location_value = $response['item_data']['location']['value'];
		$location_desc = $response['item_data']['location']['desc'];

		return ['callNumber'=>$call_number, 'title'=>$title, 'status' =>$status,'effectiveShelvingOrder'=>0, 
			'effectiveLocationId'=>$location_value,'effectiveLocationName'=>$location_desc];
	}

}
