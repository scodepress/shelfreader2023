<?php

namespace App\Traits;

use App\Models\Institution;
use App\Models\MembershipRequestRecord;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AdminTrait {

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function getUnapprovedMembershipRequests()
	{
		return MembershipRequestRecord::where('approved_by',0)->get();
	}

	public function folioInstitutions()
	{
		return DB::table('institution_api_services as i')
			->join('institutions as in','in.id','=','i.institution_id')
			->select('in.id','in.institution_name','i.api_service_id')
			->where('i.api_service_id','=',3)
			->groupBy('in.id','in.institution_name','i.api_service_id')
			->get();
	}


}
