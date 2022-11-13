<?php

namespace App\Http\Controllers;

use App\Models\InstitutionApiService;
use Illuminate\Http\Request;
use App\Traits\BookShelfTrait;
use App\Models\Shelf;
use App\Models\Correction;
use Illuminate\Support\Facades\Redirect;
use App\Models\Move;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShelfCorrectionController extends Controller
{
    use BookShelfTrait; 
	
    public function correction($user_id,$barcode)
    {
    	
        $mpos = $this->mpos($user_id);
	$itemInfo = Shelf::where('user_id',$user_id)->where('barcode',$barcode)->get();
	$call_number = $itemInfo[0]->callnumber;
	$title = $itemInfo[0]->title;
	$libraryId = User::where('id',$user_id)->pluck('library_id')[0];
	$sortSchemeId = User::where('id',$user_id)->pluck('scheme_id')[0];

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

	// Insert item into corrections table

			$correction = new Correction; 
			$correction->user_id = $user_id;
			$correction->library_id = $libraryId;
			$correction->sort_scheme_id = $sortSchemeId;
			$correction->barcode = $barcode;
			$correction->call_number = $call_number;
			$correction->title = $title;
			$correction->save();
	

	if($sortSchemeId === 2)
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
