<?php

namespace App\Http\Controllers;

use App\Mail\MembershipRequest;
use App\Models\FolioAuthenticationToken;
use App\Models\ImpersonateUser;
use App\Models\Institution;
use App\Models\MembershipRequestRecord;
use App\Models\User;
use App\Traits\AdminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AdminController extends Controller
{
	use AdminTrait;

	public function show(User $user,Institution $institution) {
		if($user->institution_id == 3) {
			$userActionId = Auth::user()->id;
		} else {
			$userActionId = $user->id;
		}
		$impersonatedUser = ImpersonateUser::get();

		if($impersonatedUser->first())
		{
			$impersonatedUserId = $impersonatedUser[0]->user_id;

		} else {
			 
			$impersonatedUserId = 0;
		}

		$hasAuthenticationKey = FolioAuthenticationToken::where('institution_id',$institution->id)->get();
		//dd($hasAuthenticationKey);
		if($hasAuthenticationKey->first() && $user->service_id == 3)	
		{
			$newInstitutionId = 1;
		} else {
		
			$newInstitutionId = 0;
		}

		//dd($newInstitutionId);
		$userInstitutions = User::userInstitution();
		$owner = User::where('id',Auth::user()->id);
		$unapprovedRequests = $this->getUnapprovedMembershipRequests();
		$allUsers = User::userLibrary();
		$institutions = Institution::get();
		$returned_institution_id = null;
		$folioInstitutions = $this->folioInstitutions();

		return Inertia::render('SuperAdmin/Index', [
			'users' => $userInstitutions,
			'allUsers' => $allUsers,
			'owner' => $owner,
			'unapprovedRequests' => $unapprovedRequests,
			'institutions' => $institutions,
			'returned_institution_id' => $returned_institution_id,
			'userActionId' => $userActionId,
			'newInstitutionId' => $newInstitutionId,
			'folioInstitutions' => $folioInstitutions,
			'impersonatedUserId' => $impersonatedUserId,
			'aUser' => Auth::user()->id,
		]);

	}

	public function approveMembers(Request $request) {

		$email = $request->email;

		$request->validate([
			'email' => 'email'
		]);


		DB::table('membership_request_records')
			->where('email',$email)
			->update(['approved_by' => Auth::user()->id]);

		return back();
	}


	public function destroy(Request $request)
	{
		$email = $request->email;

		$request->validate([
			'email' => 'email'
		]);

		User::where('email',$email)->delete();

		return back();
	}



}
