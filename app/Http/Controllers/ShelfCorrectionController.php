<?php

namespace App\Http\Controllers;

use App\Models\InstitutionApiService;
use Illuminate\Http\Request;
use App\Traits\BookShelfTrait;
use App\Models\Shelf;
use Illuminate\Support\Facades\Redirect;
use App\Models\Move;
use Illuminate\Support\Facades\Auth;

class ShelfCorrectionController extends Controller
{
    use BookShelfTrait; 
	
    public function correction($user_id,$barcode)
    {
    	
        $mpos = $this->mpos($user_id);

        $shelf_position = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('shelf_position')[0];

        //dd($shelf_position);

        if($mpos > $shelf_position) {

            $book_right = $this->book_right($shelf_position,$mpos,$barcode,$user_id); 

            if($book_right) { Move::where('barcode',$barcode)->where('user_id',$user_id)->delete(); }   
        }

        else {

            $book_left = $this->book_left($shelf_position,$mpos,$barcode,$user_id);

            if($book_left) { Move::where('barcode',$barcode)->where('user_id',$user_id)->delete(); } 

        }

     

	if(InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->pluck('sort_scheme_id')[0] === 3)
	{
              return Redirect::route('maps');

	} else {
	
              return Redirect::route('shelf');
	}
    

        
    }

    public function deleteMove($barcode)
    {
    	Move::where('barcode', $barcode)->where('user_id',$user_id)->delete;

        //Return number of moves left in moves table
    }
}
