<?php

namespace App\Contracts\Services\SortKey;


interface SortKeysInterface
{
	public function index($callNumber,$barcode);

	public function storeSortKey($keys,$callNumber,$barcode);
	
	public function correctShelfPosition($barcode,$user_id);

	public function correctPositions($user_id);
}
