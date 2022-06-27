<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApproveUserController extends Controller
{
	public function store(Request $request) {

		$request->validate([
			'checkedNames' => 'required',
			'checkedNames.*' => 'email'
		]);

		$checkedNames = $request->checkedNames;

		User::whereIn('email',$checkedNames)->update(['approved'=>1]);

		return back()->with('message','This user has been approved');

	}

	public function romanNumerals()
	{
		return  'Hey';
	}


}
