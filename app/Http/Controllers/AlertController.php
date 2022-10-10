<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AlertController extends Controller
{
	public function show()
	{
		$user_id=Auth::user()->id;
		$libraryId = User::where('id',$user_id)->pluck('library_id')[0];
		$alerts=Alert::where('user_id',$user_id)->orderBy('created_at')->paginate(20);

		return Inertia('Alerts/Index', [
			'alerts' => $alerts,
		]);
	}
}
