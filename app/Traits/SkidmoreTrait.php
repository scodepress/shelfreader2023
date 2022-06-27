<?php

namespace App\Traits;

use App\Actions\Maps\MakeAmaskAction;
use App\Models\Shelf;
use App\Models\SortKey;
use App\Models\DeweySortKey;
use App\Models\Lis;
use App\Models\Move;
use App\Models\Subsequence;
use App\Models\Alma;
use App\Models\Sirsi;
use App\Models\Callnumber;
use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait SkidmoreTrait {

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function isSingleCutter($smask)
	{
		$singleCutter = strpos($smask,'_DA');

		return $singleCutter;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function isDoubleCutter($smask)
	{
		$firstCutter = strpos($smask,'DA');
		$doubleCutter = strpos($smask,'_AI');

		if($firstCutter != false AND $doubleCutter != false) {
			return $doubleCutter;
		} else {

			return false;
		}
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function processCallNumber($callNumber)
	{
		$callNumberArray = str_split($callNumber);
		$amask = MakeAmaskAction::execute($callNumberArray);
		$smask = implode('',$amask);

		$singleCutter = $this->isSingleCutter($smask);

		if ($singleCutter != false) {
			$processedCallNumber = 
				$this->removeSingleCutterSpace($callNumber,$singleCutter);	
			return $processedCallNumber;
		}

		$doubleCutter = $this->isDoubleCutter($smask);

		if($doubleCutter != false) {
			$processedCallNumber =
				$this->removeDoubleCutterSpace($callNumber,$doubleCutter);

			return $processedCallNumber;
		}

		return $callNumber;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function removeSingleCutterSpace($callNumber,$singleCutter)
	{
		$callNumber = substr_replace($callNumber,"",$singleCutter,1);

		return $callNumber;
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function removeDoubleCutterSpace($callNumber,$doubleCutter)
	{
		$callNumber = substr_replace($callNumber,"",$doubleCutter,1);

		return $callNumber;

	}
}
