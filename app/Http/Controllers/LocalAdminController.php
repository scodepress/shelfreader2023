<?php

namespace App\Http\Controllers;

use App\Models\FolioAuthenticationToken;
use App\Models\Institution;
use App\Models\InstitutionApiService;
use App\Models\Library;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\In;

class LocalAdminController extends Controller
{
	public function index(Request $request){
		
		$user = Auth::user(); 
		$userName = $user->name;
		$user_id = $user->id;
		$institution_id = $user->institution_id;

		$impersonatingId = $request->session()->get('impersonated_by');

		if($impersonatingId)
		{
			$impId = $impersonatingId;
		} else {
			$impId = 0;
		}
		
		if($impId >0)
		{
			$impersonatingUserPrivs = User::where('id',$impId)->pluck('privs')[0];
			if($impersonatingUserPrivs === 1) {
				$show_link = 1;
			} else {
				$show_link = 0;
			}
		} else {
			$show_link = 0;
		}

		$userCandidates = User::where('institution_id',$institution_id)->where('approved',0)->orderBy('id')->get();
		$libraries = Library::where('institution_id',$institution_id)->get();
		$token = FolioAuthenticationToken::where('institution_id',$institution_id)->get();

		if($token->first())  {
			$token = $token[0]->auth_key;
		} else {

			$token = 'Your token will appear here.';
		}

		$availableServices = InstitutionApiService::availableServices($institution_id);

		return Inertia::render('LocalAdmin/Folio/Index', [
			
			'userName' => $userName,
			'token' => $token,
			'availableServices' => $availableServices,
			'userCandidates' => $userCandidates,
			'libraries' => $libraries,
			'showLink' => $show_link,
		]);	
	
	}
}
