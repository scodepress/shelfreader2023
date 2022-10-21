<?php

namespace App\Contracts\Services\Admin;

class AdminService {

	public function getErrorRate($correctionCount,$totalErrorCount)
	{
		return round($correctionCount/$totalErrorCount,2);
	}


}
