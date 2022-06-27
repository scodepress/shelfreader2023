<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Shelf extends Model
{
	use HasFactory;
	 public static function offShelf($user_id,$shelf_count)
    {
        $onshelf = DB::table('shelves')
        ->pluck('barcode');
        //dd($onshelf);

        return DB::table('books')
        ->select('*')
        ->whereNotIn('barcode',$onshelf)
        ->get();
    
    }

    public static function resetCpositions($new_barcode,$cposition,$user_id)
    {
        DB::table('shelves')
        ->where('correct_position','>=',$cposition)
        ->where('barcode','!=',$new_barcode)
        ->where('user_id','=',$user_id)
        ->increment('correct_position');
    }

    public static function cpositionByPosition($user_id)
    {
    
        return DB::table('shelves')
        ->where('user_id', $user_id)
        ->orderBy('shelf_position')
        ->pluck('correct_position')
        ->toArray();
    
    }

}
