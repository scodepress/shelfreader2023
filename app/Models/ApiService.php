<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiService extends Model
{
	use HasFactory;

	public static function institutionServices($institution_id) {

		return	DB::table('api_services as a')
			->join('institution_api_services as i','i.api_service_id','=','a.id')
			->select('service_name','api_service_id')
			->where('i.institution_id',$institution_id)
			->groupBy('api_service_id','service_name')
			->get();
	}

}
