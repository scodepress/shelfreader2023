<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Library\LibraryInterface;
use App\Models\Library;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LibraryController extends Controller
{
    
	public function __construct(LibraryInterface $li)
	{
		$this->li = $li;
	}

	public function show()
	{
		$userId = Auth::user()->id;
		$sortSchemeId = User::where('id',$userId)->pluck('scheme_id')[0];
		$libraryId = Auth::user()->library_id;
		$libraryName = Library::where('id',$libraryId)->pluck('library_name')[0];
		$correctionCount = $this->li->getCorrectionCount($libraryId);
		$totalScans = $this->li->getTotalScanCount($libraryId);
		$userInfo = $this->li->getTotalScanCountByUser($libraryId);

		if(!$totalScans) {

			$errorRate = 0;

		} else {
		
			$errorRate = round($correctionCount/$totalScans*100,2);

		}


		return Inertia::render('Library/Index',[
			'libraryName' => $libraryName,
			'errorRate' => $errorRate,
			'totalScans' => $totalScans,
			'correctionCount' => $correctionCount,
			'userInfo' => $userInfo,
			'sortSchemeId' => $sortSchemeId,
		]);
	}
}
