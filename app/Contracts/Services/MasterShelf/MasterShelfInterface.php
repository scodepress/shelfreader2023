<?php


namespace App\Contracts\Services\MasterShelf;

interface MasterShelfInterface  {

	public function insertNewItems($userId,$libraryId);

	public function getNewItemsWithKeys($userId,$libraryId);
	
	public function getSortedItemsFromMasterShelf($userId,$libraryId);

	public function getSortedItemsByCallNumber($userId,$libraryId,$beginningCallNumber,$endingCallNumber);

	public function getSortedItemsByDateRange($userId,$libraryId,$beginningDate,$endingDate);
	
	public function getSortedItemsByCallNumberAndDateRange($userId,$libraryId,$beginningCallNumber,$endingCallNumber,$beginningDate
		,$endingDate);

	public function insertSearchResultsForDisplay($userId,$libraryId);

	public function getDefaultBeginningCallNumber($libraryId);

	public function getDefaultEndingCallNumber($libraryId);

	public function getDefaultBeginningDate($libraryId);

	public function getDefaultEndingDate($libraryId);
	
	public function getCallNumberFromBarcode($barcode);
}
