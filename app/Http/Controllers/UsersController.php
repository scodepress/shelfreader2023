<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\ImpersonateUser;
use App\Models\Institution;
use App\Models\User;
use App\Models\InstitutionApiService;
use DB;
use Illuminate\Support\Facades\Auth;

use Lab404\Impersonate\Models\Impersonate;

class UsersController extends Controller

{ 
	use Impersonate;

	public function impersonate(Request $request) 
	{
		$user = User::findOrFail($request->user_id);
		Auth::user()->impersonate($user);

		ImpersonateUser::truncate();

		$imp = new ImpersonateUser;

		$imp->user_id = $user->id;

		$imp->save();

		$user_id = Auth::user()->id;
		$loaded = InstitutionApiService::where('user_id',$user_id)->where('loaded',1)->get();

		if($loaded[0]->sort_scheme_id === 3) {

			return redirect()->route('maps');
		} else {

			return redirect()->route('shelf');
		}
	}

	public function leaveImpersonate() 
	{
		Auth::user()->leaveImpersonation();

		ImpersonateUser::truncate();

		return redirect()->route('admin'); 
	}

}
