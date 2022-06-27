<?php

namespace App\Traits;

use App\Models\Institution;
use App\Models\InstitutionApiService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait UserTrait {

	/**
	* undocumented function
	*
	* @return void
	*/
	public function hasLoadedService()
	{
		$user_id = Auth::user()->id;
		$loaded = InstitutionApiService::where('user_id',$user_id)->where('loaded',1)->get();
		if($loaded->first()) {
			return $loaded;

		} else {

			//Insert primary service as loaded service
			$primary_service_id = User::where('id', $user_id)->pluck('service_id')[0];
			$loaded = new InstitutionApiService;
			$loaded->user_id = $user_id;
			$loaded->institution_id = Auth::user()->institution_id;
			$loaded->api_service_id = $primary_service_id;
			$loaded->loaded =1;
			$loaded->sort_scheme_id = User::where('id',$user_id)->pluck('scheme_id')[0];
			$loaded->save();	

			return $loaded;
		}
	}

	

	}

