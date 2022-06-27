<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MembershipRequest;
use App\Models\MembershipRequestRecords;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MembershipRequestController extends Controller
{
	public function show(){


		return Inertia::render('Auth/MembershipRequest', [


		]);	


	}

	public function store(Request $request){

		$request->validate(
			[
				'institution_name' => 'required | max:50',
				'inquirer_name' => 'required | max:35',
				'email' => 'required | email',
				'notes' => 'max:100',
			
			
			]);

		$mrequest = new MembershipRequestRecords;
		$mrequest->institution_name = $request->institution_name;
		$mrequest->inquirer_name = $request->inquirer_name;
		$mrequest->email = $request->email;
		$mrequest->notes = $request->notes;
		$mrequest->save();

		$recipient_email = 'gildedpage@comcast.net';

		$membershipRequest = [
			'institution_name' => $request->institution_name,
			'inquirer_name' => $request->inquirer_name,
			'email' => $request->email,
			'notes' => $request->notes,
		];

		//Mail::to($recipient_email)->send(new MembershipRequest($membershipRequest));

		return Redirect::route('membership-request')->with('message','Your request has been received!');

	} 
}
