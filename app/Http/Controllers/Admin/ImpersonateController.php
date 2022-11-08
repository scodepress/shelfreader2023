<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ShelvesController;
use App\Http\Requests\ImpersonateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Guard;
use App\Models\User;
use App\Models\Institution;
use Lab404\Impersonate\Models\Impersonate;
class impersonatecontroller extends controller
{

	public function index()

	{

		$emails = db::table('users')->select('email','name')->orderby('name')->get();

		return view('admin.impersonate', compact('emails'));
	}

	public function store(ImpersonateRequest $request)

	{

		session()->put('impersonate', $request['user_id']);

	}

	public function destroy()

	{
		session()->forget('impersonate');

		return redirect('dashboard');
	}

	public function impersonate(User $user) 
	{

		auth()->user()->impersonate($user);

		return redirect()->route('home.index');
	}


}
