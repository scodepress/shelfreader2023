<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Correction;
use App\Models\UsageCount;
use App\Models\MasterShelfLcc;
use App\Models\MasterShelfMap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
	public function show(User $user) 
	{
		$user_id = Auth::user()->id;
		$name = $user->where('id',$user_id)->pluck('name')[0];
		$alertCount = Alert::where('user_id',$user_id)->count();
		$mapsCount = MasterShelfMap::where('user_id',$user_id)->count();
		$lccCount = MasterShelfLcc::where('user_id',$user_id)->count();
		$corrections = Correction::where('user_id',$user_id)->count();
		$oldUsageCount = UsageCount::where('user_id',$user_id)->get();

		if($oldUsageCount->first()) 
		{
			$addedUsageCount = UsageCount::where('user_id',$user_id)->pluck('count')[0];
		} else {

			$addedUsageCount = 0;

		}
		$total = $alertCount + $mapsCount + $lccCount + $addedUsageCount;

		return Inertia::render('Dashboard',[

			'name' => $name,
			'alertCount' => $alertCount,
			'mapsCount' => $mapsCount,
			'lccCount' => $lccCount,
			'total' => $total,
			'corrections' => $corrections,
		]);


	}
}
