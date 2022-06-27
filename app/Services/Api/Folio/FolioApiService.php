<?php

namespace App\Services\Api\Folio;

use App\Contracts\Services\Api\ApiServicesInterface;
use App\Models\FolioAuthenticationToken;
use App\Models\ApiUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class FolioApiService implements ApiServicesInterface
{

	public function index($barcode)
	{
		//Return the Api response to the controller
		$institution_id = Auth::user()->institution_id;
		
		$token = FolioAuthenticationToken::where('institution_id', $institution_id)->orderByDesc('created_at')->pluck('auth_key')[0];

		$tenant = FolioAuthenticationToken::where('institution_id', $institution_id)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];
		
		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token
			
		])->get("$apiUrl/inventory/items?query=barcode = ($barcode)");

		if($response->json('items'))
		{
			return $response->json();
		} else {

			return false;
		}

	}

	public function itemParameters($barcode) {
		
		$folioResponse = $this->index($barcode);

		if($folioResponse === false)
		{
			return false;
		}

		$folioItem = [];

		$folioItem = [
			
			'callNumber' => $folioResponse['items'][0]['callNumber'],
			'title' => $folioResponse['items'][0]['title'],
			'status' => $folioResponse['items'][0]['status']['name'],
			'effectiveShelvingOrder' => $folioResponse['items'][0]['effectiveShelvingOrder'],
			'effectiveLocationId' => $folioResponse['items'][0]['effectiveLocation']['id'],
			'effectiveLocationName' => $folioResponse['items'][0]['effectiveLocation']['name'],
			
		];

			return $folioItem;

	}
	
}
