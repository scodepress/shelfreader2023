<?php

namespace App\Http\Controllers;

use App\Models\FolioAuthenticationToken;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Redirect as GlobalRedirect;

class FolioAuthenticationTokenController extends Controller
{
	protected $user;
	/**
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function show()
	{
		$user = Auth::user(); 
		$userName = $user->name;
		$token = FolioAuthenticationToken::where('user_id',$user->id)->get();

		if(!$token->first()) { $token = 'When your token is successfully generated, it will appear here.'; }
		else {

			$token = $token[0]->auth_key;
		}

		return Inertia::render('AuthenticationKeys/Folio/Index', ['userName' =>$userName,'token' => $token]);
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function store(Request $request)
	{

	}

	public function adminCreateToken(Request $request)
	{
		$user_id = $request->user_id;
		$admin_id = Auth::user()->id;

		$user = User::where('id',$user_id)->get();
		$folioUser = FolioAuthenticationToken::where('user_id',$user_id)->get();

		$tenant = $folioUser->tenant;
		$username = $folioUser->username;
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

	public function adminUpdateToken(Request $request)
	{
		//dd($request->institution_id);
		$userActionIds = User::where('institution_id',$request->institution_id)->orderBy('id')->take(1)->get();
		$userActionId = $userActionIds[0]->id;

		$hasAuthenticationKey = FolioAuthenticationToken::where('institution_id',$request->institution_id)->get();

		if(!$hasAuthenticationKey->first()) {

			return redirect()->route('admin',['user'=>$userActionId,'institution'=>$request->institution_id]);
		} else {

			$credentials = FolioAuthenticationToken::where('institution_id',$request->institution_id)->get();
			$folioTenant = $credentials[0]->tenant;
			$folioUserName = $credentials[0]->folio_username;
			$folioPassword = $credentials[0]->folio_password;

			$token = FolioAuthenticationToken::getToken($folioTenant,$folioUserName,$folioPassword,$request->institution_id);

			FolioAuthenticationToken::where('institution_id',$request->institution_id)->update(['auth_key' => $token]);

			return redirect()->route('admin',['user'=>Auth::user()->id,'institution'=>$request->institution_id]);
		}



	}

}
