<?php

namespace App\Contracts\Services\Library;


interface LibraryInterface {

	public function getTotalScanCount($libraryId);

	public function getCorrectionCount($libraryId);

}
