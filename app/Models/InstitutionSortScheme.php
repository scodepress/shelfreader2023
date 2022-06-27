<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InstitutionSortScheme extends Model
{
    use HasFactory;

    public static function getSortSchemes($institution_id) {
    
	    return DB::table('institution_sort_schemes as i')
		    ->join('sort_schemes as s','s.id','=','i.institution_id')
		    ->select('s.id','i.sort_scheme_name')
		    ->where('i.institution_id',$institution_id)
		    ->get();

	    		
    }

}
