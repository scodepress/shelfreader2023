<?php

namespace App\Models;

use App\Models\AlmaAuthenticationToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Alma extends Model
{
	use HasFactory;

	public static function almaResponse($barcode)
	{

		$mms_id = $barcode;
		$institution_id = Auth::user()->institution_id;
		$apiKey = AlmaAuthenticationToken::where('institution_id',$institution_id)->pluck('api_key')[0];

		$response = Http::get("https://api-na.hosted.exlibrisgroup.com/almaws/v1/bibs/$mms_id/holdings?apikey=$apiKey&format=json");

		return $response;

	}

	public static function almaParameters($barcode)
	{

		$response = self::almaResponse($barcode);
		$call_number = $response['holding'][0]['call_number'];
		$title = $response['bib_data']['title'];

		return ['barcode'=> $barcode, 'call_number'=>$call_number, 'title'=>$title,'status'=>0];

	}
}
