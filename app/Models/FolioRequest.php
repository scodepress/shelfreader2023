<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use App\Models\FolioAuthenticationToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FolioRequest extends Model
{
	use HasFactory;

	public static function folioResponse($barcode) {
		$institution_id = Auth::user()->institution_id;
		$token = FolioAuthenticationToken::where('institution_id', $institution_id)->orderByDesc('created_at')->pluck('auth_key')[0];
		$tenant = FolioAuthenticationToken::where('institution_id', $institution_id)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];
		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token
			
		])->get("$apiUrl/inventory/items?query=barcode = ($barcode)");

		return $response->json();
		
	}



}

