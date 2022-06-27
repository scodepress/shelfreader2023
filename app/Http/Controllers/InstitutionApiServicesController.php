<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\InstitutionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionApiServicesController extends Controller
{
	public function index(){
		$user_id = Auth::user()->id;

		$institution_id =  Institution::where('user_id',$user_id)->pluck['id'][0];
		InstitutionApiService::where('user_id',$user_id)->get();

	}

	public function store(Request $request){

		$user_id = $request->auth()->id;
		$institution_id =  Institution::where('user_id',$user_id)->pluck['id'][0];

		$ap = new InstitutionApiService;
		$ap->user_id = $user_id;
		$ap->institution_id = $institution_id;
		$ap->api_service_id = $request->api_service_id;

		$ap->save();
	
	}
}
