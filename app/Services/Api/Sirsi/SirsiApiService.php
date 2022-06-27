<?php

namespace App\Services\Api\Sirsi;

use App\Contracts\Services\Api\ApiServicesInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SirsiCredential;

class SirsiApiService implements ApiServicesInterface
{

	public function index($barcode)
	{
		$user_id = Auth::user()->id;
		$psurl = SirsiCredential::where('user_id',$user_id)->pluck('url')[0];
		$psid = SirsiCredential::where('user_id',$user_id)->pluck('client_id')[0];

		$response = Http::post("https://$psurl/symwsbc/rest/standard/lookupTitleInfo?clientID=$psid&itemID=$barcode&includeOPACInfo=true&includeItemInfo=true&json=true");

		$response = $response->getBody()->getContents();

		$response = collect(json_decode($response, true));

		return $response;

	}

	public function itemParameters($barcode)
	{
		$response = $this->index($barcode);
		$title = $response['TitleInfo'][0]['title'];
		$num = $response['TitleInfo'][0]['numberOfCallNumbers']-1;

		foreach ($response as $key => $r)
		{
			for($i=0; $i<=$num; $i++)
			{
				$num_copies = $r[0]['CallInfo'][$i]['numberOfCopies']-1;
				for($v=0; $v <= $num_copies; $v++)
				{


					if($r[0]['CallInfo'][$i]['ItemInfo'][$v]['itemID'] == $barcode)
					{
						//$alt_info[] = $title;
						$call_number = $r[0]['CallInfo'][$i]['callNumber'];


					}
				}

			}


		}

		return ['callNumber'=>$call_number, 'title'=>$title, 'status' =>'Available' ,'effectiveShelvingOrder'=>0, 
			'effectiveLocationId'=>0,'effectiveLocationName'=>0];
	}

}
