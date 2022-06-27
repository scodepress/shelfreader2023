<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Shelf;

class DeweySortKey extends Model
{
    public static function createKey($callnumber)
    {
    	$category = null;
    	$decimel = null;
    	$letter = null;

        $dot = strpos($callnumber, '.');

        if($dot === FALSE) {

            $callnumber = str_split($callnumber);


        foreach($callnumber as $key=>$c) {



            if(is_numeric($c)) {
                $category .= $c;
            } else {

                if($c != '') { 

                   $letter .= $c; 

                 }
            }
        } 

            $decimel = 0;

            return "$category*$decimel*$letter";


        }

        else { // Callnumber has a decimal

 		$callnumber = str_split($callnumber);


    	foreach($callnumber as $key=>$c) {



    		if(is_numeric($c)) {
    			$category .= $c;
    		} else {

    			if($c == '.') { break; }
    		}
    	} // End of category foreach loop

  

    	$progress = $key;

    	foreach($callnumber as $key=>$d) {
    		 if($key>$progress) {
    		 	if(is_numeric($d)) {
    		 		$decimel .= $d;
    		 	} elseif($d != '') {

    		 		$letter .= $d;
    		 	}
    		 }
    	}

    	return "$category*$decimel*$letter";

    }
    
    }

    public static function correctShelfPosition($barcode,$user_id)
    {

        $cpositions = self::correctPositions($user_id);

    	foreach($cpositions as $key=>$c) 
        {
            if($c->barcode == $barcode) {
                return $key+1;
            }
        }
    }

    

    public static function correctPositions($user_id)
    {
        
        return \DB::table('dewey_sort_keys')
        ->select('barcode')
        ->orderBy('class')
        ->orderBy('subdivision')
        ->orderBy('letters')
        ->where('user_id',$user_id)
        ->get();
    }
}
