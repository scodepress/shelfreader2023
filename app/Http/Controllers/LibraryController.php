<?php

namespace App\Http\Controllers;

use App\Contracts\Services\Library\LibraryInterface;
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
		$libraryId = Auth::user()->library_id;
		$correctionCount = $this->li->getCorrectionCount($libraryId);
		$totalScans = $this->li->getTotalScanCount($libraryId);
		$errorRate = round($correctionCount/$totalScans*100,2);


		return Inertia::render('Library/Index',[
			'message' => 'This is the Library page',
			'errorRate' => $errorRate,
			'totalScans' => $totalScans,
			'correctionCount' => $correctionCount,
		]);
	}
}
