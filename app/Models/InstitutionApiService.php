<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InstitutionApiService extends Model
{
	use HasFactory;

	public function apiService() {

		return $this->belongsTo(ApiService::class);
	}

	public static function getApiServices($institution_id,$user_id) {

		return DB::table('institution_api_services')
			->select('api_service_id','sort_scheme_id','sort_scheme_name')
			->where('institution_id', $institution_id)
			->where('user_id', $user_id)
			->get()
			->unique('sort_scheme_id');
	}

	public static function getLoadedSortSchemeId($user_id)
	{
		$lssi = InstitutionApiService::where('user_id',$user_id)
			->where('loaded',1)
			->get();

		if($lssi->first()) {
			return $lssi[0]->sort_scheme_id;
		} else {

			$user = User::where('id',$user_id)->get();

			$api = new InstitutionApiService;

			$api->user_id = $user_id;
			$api->institution_id = 1;
			$api->library_id = $user->library_id;
			$api->api_service_id = 1;
			$api->loaded = 1;
			$api->sort_scheme_id = 1;
			$api->sort_scheme_name = "LCC";

			$api->save()			;

			return 1;

		}

	}

	public static function getLoadedApiServiceId($user_id)
	{
		return InstitutionApiService::where('user_id',$user_id)
			->where('loaded',1)
			->first()
			->api_service_id;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public static function availableServices($institution_id)
	{
		return DB::table('api_services')
			->select('id','service_name')
			->whereNotIn('id',function($query) use ($institution_id) {
				$query->select('api_service_id')->from('institution_api_services')->where('institution_id',$institution_id);
			})
			->get();
	}

}
