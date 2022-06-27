<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApiUrl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FolioAuthenticationToken extends Model
{
	use HasFactory;

	public static function getToken($folioTenant,$folioUserName,$folioPassword,$institution_id) {

		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];
		$response = Http::withHeaders([
			"Content-Type" => "application/json",
			"Accept" => "application/json",
			"X-Okapi-Tenant" => $folioTenant
		])->post("$apiUrl/authn/login", [
			"tenant" => $folioTenant,
			"username" => $folioUserName,
			"password" => $folioPassword 
		]);

		return $response->getHeader('x-okapi-token');
	}
}
