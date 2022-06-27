<?php

namespace App\Traits;

use App\Models\FolioAuthenticationToken;
use App\Services\Api\Folio\FolioApiService;
use Auth;
use Request;

trait FolioAuthenticationTrait {

	public function store(Request $request)
	{
		$tenant = $request->tenant;
		$username = $request->username;
		$password = $request->password;
		$institution_id = Auth::user()->institution_id;

		$token = FolioAuthenticationToken::getToken($tenant,$username,$password); 

		$ftoken = new FolioAuthenticationToken;
		$ftoken->user_id = Auth::user()->id;
		$ftoken->institution_id = $institution_id;
		$ftoken->auth_key = $token[0];
		$ftoken->tenant = $tenant;
		$ftoken->save();


		return redirect()->back()->with('error','Your Token was created!');

	}
}
