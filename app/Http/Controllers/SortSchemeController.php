<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use App\Models\InstitutionApiService;
use App\Models\SearchParameter;
use App\Traits\BookShelfTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SortSchemeController extends Controller
{
	use BookShelfTrait;

	public function put(Request $request)  {
		$sortSchemeId = $request->sort;

		InstitutionApiService::where('user_id',$request->user()->id)
			->where('sort_scheme_id',$request->sort)
			->update(['loaded'=>1]);

		$this->clearShelf(Auth::user()->id);

		InstitutionApiService::where('user_id',$request->user()->id)
			->where('sort_scheme_id','!=',$request->sort)
			->update(['loaded'=>0]);

		User::where('id',$request->user()->id)->update(['scheme_id'=>$request->sort]);
		SearchParameter::where('user_id',$request->user()->id)->delete();

		if($request->sort === 2)
		{
			return Redirect::route('maps');

		} else {

			return Redirect::route('shelf');
		}
	}
} 
