<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStepTwoRequest;
use App\Models\ApiService;
use App\Models\Institution;
use App\Models\InstitutionApiService;
use App\Models\Library;
use App\Models\SortScheme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Redirect;

class RegisterStep2Controller extends Controller
{
	public function show() 
	{
		$user = User::where('id',Auth::user()->id)->get();
		$libraries = Library::orderBy('library_name')->get();
		$sortSchemes = SortScheme::get();

		return Inertia::render('Auth/RegisterStepTwo', 
			[
				'user' => $user,
				'libraries' => $libraries,
				'sortSchemes' => $sortSchemes,
			]
		);
	}

	public function store(RegisterStepTwoRequest $request)
	{
		$libraryId = $request->libraryId;
		$lcc = $request->lcc;
		$maps = $request->maps;
		$user_id = Auth::user()->id;

		if($lcc === null && $maps === null) {
		
			return Redirect::route('register.step2')->with('message','You must choose at least one sorting method.');
		}

		// Update user table
		if($lcc === 'on' && $maps != 'on') {

			User::where('id',$user_id)
			->update([
				'library_id' => $libraryId,
				'approved'=>1,
				'scheme_id' =>1, 
			]);

		$ap = new InstitutionApiService;

		$ap->user_id = $user_id;
		$ap->institution_id = 1;
		$ap->library_id = $libraryId;
		$ap->api_service_id = 1;
		$ap->loaded = 1;
		$ap->sort_scheme_id = 1;
		$ap->sort_scheme_name = 'LCC';

		$ap->save();

		return Redirect::route('shelf');

		}
		
	elseif($lcc === 'on' && $maps === 'on') {

		User::where('id',$user_id)
			->update([
				'library_id' => $libraryId,
				'approved'=>1,
				'scheme_id' =>1, 
			]);

		$ap = new InstitutionApiService;

		$ap->user_id = $user_id;
		$ap->institution_id = 1;
		$ap->library_id = $libraryId;
		$ap->api_service_id = 1;
		$ap->loaded = 1;
		$ap->sort_scheme_id = 1;
		$ap->sort_scheme_name = 'LCC';

		$ap->save();

		$ap = new InstitutionApiService;

		$ap->user_id = $user_id;
		$ap->institution_id = 1;
		$ap->library_id = $libraryId;
		$ap->api_service_id = 2;
		$ap->loaded = 0;
		$ap->sort_scheme_id = 2;
		$ap->sort_scheme_name = 'Maps';

		$ap->save();


		return Redirect::route('shelf');

	} else {
		
		User::where('id',$user_id)
			->update([
				'library_id' => $libraryId,
				'approved'=> 1,
				'scheme_id' =>2, 
			]);

		$ap = new InstitutionApiService;

		$ap->user_id = $user_id;
		$ap->institution_id = 1;
		$ap->library_id = $libraryId;
		$ap->api_service_id = 2;
		$ap->loaded = 1;
		$ap->sort_scheme_id = 2;
		$ap->sort_scheme_name = 'Maps';

		$ap->save();


		return Redirect::route('maps');
	 }
		

}
}
