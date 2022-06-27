<?php

namespace App\Http\Controllers;

use App\Models\InstitutionApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SkidmoreCloud;
use Auth;

class SkidmoreCloudController extends Controller
{
	public function show(){
		$books = SkidmoreCloud::get();
		$loaded_service = InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->pluck('api_service_id')[0];
		$sortSchemeId = InstitutionApiService::where('user_id',Auth::user()->id)->where('loaded',1)->pluck('sort_scheme_id')[0];

		return Inertia::render('SkidmoreCloud/Index', 
			[
				'books' => $books,
				'loaded_service' => $loaded_service,
				'sortSchemeId' => $sortSchemeId,
			]);
	}
}
