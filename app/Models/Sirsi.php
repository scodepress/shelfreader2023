<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\SirsiCredential;
use Illuminate\Support\Facades\Redirect;

class Sirsi extends Model
{
    

    public static function sirsiResponse($barcode)
    {
	    $user_id = Auth::user()->id;
	    $psurl = SirsiCredential::where('user_id',$user_id)->pluck('url')[0];
	    $psid = SirsiCredential::where('user_id',$user_id)->pluck('client_id')[0];

    	$response = Http::post("https://$psurl/symwsbc/rest/standard/lookupTitleInfo?clientID=$psid&itemID=$barcode&includeOPACInfo=true&includeItemInfo=true&json=true");

            $response = $response->getBody()->getContents();

            $response = collect(json_decode($response, true));

            return $response;

    }

    public static function sirsiParameters($barcode)
    {
        $response = self::sirsiResponse($barcode);
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
                    $call_number = strtoupper($r[0]['CallInfo'][$i]['callNumber']);
                    

                }
            }

        }


    }

        return ['barcode', $barcode, 'call_number'=>$call_number, 'title'=>$title];
    }


	public static function itemParameters($barcode)
	{
		$response = self::sirsiResponse($barcode);

	    if($response['TitleInfo'][0]['title'] === null)
	    {
		    return;
	    }
		$title = $response['TitleInfo'][0]['title'];
		$num = $response['TitleInfo'][0]['numberOfCallNumbers']-1;
		$ids = array();
		$ids = array('LOST-ASSUM','CHECKEDOUT','MISSING','LOST','LOST-CLAIM','Z-MISSING','WITHDRAWN','CANCELED',
			'Z-REMOVED','INTRANSIT','DISCARD','PALCI','SHADOW');

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
						$current_location_id = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['currentLocationID'];
						if(in_array($current_location_id,$ids) === true)
						{ 
							$status = 'Unavailable';
							$effectiveLocationId = $current_location_id;
						} else {

							$status = 'Available';
							$effectiveLocationId = $current_location_id;
						}

						if(substr($current_location_id, 0, 7) == 'ONHOLD-')
						{
							$status = 'Unavailable';
							$effectiveLocationId = 'On Hold';

						}


					}
				}

			}


		}

		return ['callNumber'=>$call_number, 'title'=>$title, 'status' =>$status,'effectiveShelvingOrder'=>0, 
			'effectiveLocationId'=>$effectiveLocationId,'effectiveLocationName'=>0];
	}
}
