<?php

namespace App\Services\Folio;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\FolioAuthenticationToken;
use App\Models\ApiUrl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Contracts\Services\Api\ApiServicesInterface;

class OnlineInventoryRequest 
{

	public static function folioOnlineInventoryResponse($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber) {
		$institution_id = Auth::user()->institution_id;
		$token = FolioAuthenticationToken::where('institution_id', $institution_id)->orderByDesc('created_at')->pluck('auth_key')[0];
		$tenant = FolioAuthenticationToken::where('institution_id', $institution_id)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];
		
		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token
			
		])->get("$apiUrl/inventory/items?query=effectiveShelvingOrder >= $leftMostNormalizedCallNumber 
                    and 
                effectiveShelvingOrder <= $rightMostNormalizedCallNumber");

		dd($response->json());
		
	}



}

