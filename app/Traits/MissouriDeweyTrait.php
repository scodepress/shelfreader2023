<?php

namespace App\Traits;

use App\Models\Student;
use App\Models\Point;
use App\Models\WeeklyTotal;
use App\Models\Major;
use App\Models\Trend;
use App\Models\Minor;
use App\Models\Step;
use App\Models\Day;
use App\Models\MissouriDeweySortKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

	trait MissouriDeweyTrait {

		public function makeAmask($callNumber) {

			$callNumberArray = str_split($callNumber);

			foreach($callNumberArray as $c) {

				if (ord($c) >= 65 AND ord($c) <= 90) { $amask[] = 'A'; }
				elseif(ord($c) >= 97 AND ord($c)<=122) { $amask[] = 'a'; }
				elseif(ord($c)>=48 AND ord($c)<=57) { $amask[] = 'I'; }
				elseif(ord($c) === 32) { $amask[] = '_'; }
				elseif($c === '.') { $amask[] = 'D'; }

			}
			return $amask;
		}

		public function getDeweyClass($callNumber) {
			$amask = $this->makeAmask($callNumber);
			$classNumber = null;
			if(is_numeric($callNumber[0])) {

				foreach($amask as $key=>$a)  {
					if($a ==='I' || $a === 'D') {
						$classNumber .= $callNumber[$key];
					} else {
						return $classNumber;
					}
				}
			} else {
				return false;
			}
		}



		public function makeSmask($callNumber) {

			$smask = implode('',$this->makeAmask($callNumber));

			return $smask;
		}

		public function hasPrefix($callNumber) {

			if($this->makeAmask($callNumber)[0] === 'A') { return true; }
			else {
				return false;
			}
		}

		public function getPrefix($callNumber) { 

			if($this->getDeweyClass($callNumber)) {
				return $this->getDeweyClass($callNumber);
			}
			$prefix = null;

			foreach($this->makeAmask($callNumber) as $key=>$m) {
				if($m === 'A') { $prefix .= $callNumber[$key]; }
				else {

					return $prefix;
				}

			}
		}

		public function lengthToAuthor($callNumber) 
		{
			return strlen($this->getPrefix($callNumber))+1;
		}

		public function lengthToCutter($callNumber) 
		{
			return $this->lengthToAuthor($callNumber) + strlen($this->getAuthor($callNumber));
		}

		public function lengthToCutterDecimal($callNumber)
		{
			if($this->getCutterDecimal($callNumber) === 0) { return 0; }
			return $this->lengthToAuthor($callNumber) + strlen($this->getCutter($callNumber));
		}

		public function lengthToTitle($callNumber) 
		{
			return $this->lengthToCutter($callNumber) + strlen($this->getCutterDecimal($callNumber));
		}
		
		public function lengthToEndOfTopline($callNumber)
		{
			return $this->lengthToTitle($callNumber) + strlen($this->getTitle($callNumber));
		}


		public function getCutterNumber($callNumber)  {
			$cutterNumber = null;
			$offset = $this->lengthToCutter($callNumber);

			foreach($this->makeAmask($callNumber) as $key=>$a)  {

				if($a === 'I' && $key >= $offset) {
					$cutterNumber .= $callNumber[$key];
				} elseif($cutterNumber AND $a != 'I') {

					return $cutterNumber;
				}
			}

			return $cutterNumber;

		}

		public function getCutter($callNumber) {

			$callNumberArray=str_split($callNumber);
			$offset = $this->lengthToCutter($callNumber);
			$cutter = null;
			foreach($callNumberArray as $key=>$c) {
				if(ctype_digit($c)) {
					$cutter .= $c;
				}

				if(!ctype_digit($c) AND $key > $offset) {
					return $cutter;
				}
			}

			return $cutter;
		}

		public function getCutterDecimal($callNumber) {
			
			$callNumberSegement = substr($callNumber, $this->lengthToCutter($callNumber));
			$callNumberArray=str_split($callNumberSegement);
			$offset = strpos($callNumberSegement,'.');
			$decimalNumber = null;

			if($offset) {
				foreach($callNumberArray as $key=>$c) {
					if(ctype_digit($c) AND $key > $offset) {

						$decimalNumber .= $c;
					}

					if(!ctype_digit($c) AND $key > $offset) {
						return $decimalNumber;
					}
				}

				return $decimalNumber;

			} else {
				return 0;
			}


		}


		public function hasAuthor($callNumber) {

			return strpos($this->makeSmask($callNumber),'IA');
		}

		public function getAuthor($callNumber)  {
			$callNumberArray = str_split($callNumber);
			$prefix = $this->getPrefix($callNumber);
			$offset = strlen($prefix);
			$auth = null;

			foreach($callNumberArray as $key=>$s) {

				if($key>$offset && ctype_alpha($s)) {  
					$auth .= $s;
				}

				if($key>$offset && !ctype_alpha($s)) {
					return $auth;
				}
			}

			if(!$auth) { return 0; }
			return $auth;


		}



		public function getTitle($callNumber) {

			$callNumberArray = str_split($callNumber);
			$offset = $this->lengthToTitle($callNumber);
			$smask = $this->makeSmask($callNumber);
			$title = null;
			foreach($callNumberArray as $key=>$c) {
				if($c === '.')  { continue; }
				if(ctype_alpha($c) AND $key > $offset) {
					$title .= $c;
				}

				if($smask[$key] === '_' AND $key > $offset) {
					return $title;
				}
			}
			
			if(!$title) { return 0; }

			return $title;
		}



		public function getDate($callNumber) {

			$toplineEnd = $this->lengthToEndOfTopline($callNumber);
			$callNumberArray = str_split($callNumber);
			$date = 0;
			foreach($callNumberArray as $key=>$c) {
				if(ctype_digit($c) AND $key >$toplineEnd) {
					$date .= $c;
				}

				if(!ctype_digit($c) AND $key > $toplineEnd) {

					return $date;
				}
			}

			return $date;
		}

			public function hasVolumeNumber($callNumber) {

				return strpos($callNumber, 'v.');
			}


			public function hasDateRange($callNumber) {

				return strpos($this->makeSmask($callNumber), 'IIII-IIII');
			}

			public function getDateRange($callNumber) {

				$offset = $this->hasDateRange($callNumber);
				$dateRange = null;
				if($offset) {
					foreach($this->makeAmask($callNumber) as $key=>$a)  {
						if($callNumber[$key] === '-' OR $callNumber[$key] === " ") { continue; }
						if($key >= $offset && $key <= $offset+9 && $key>=$offset+4) {
							$dateRange .= $callNumber[$key];

						}
					}
					return $dateRange;
				} else {
					return false;
				}
			}

			public function getVolumeNumber($callNumber) {

				$offset = $this->hasVolumeNumber($callNumber);

				$volumeNumber = 0;

				if($offset) {  
					foreach($this->makeAmask($callNumber) as $key=>$a) {
						if($key > $offset+1 && $a === 'I') {
							$volumeNumber .= $callNumber[$key];
						} elseif($key>$offset && $a!='I') {
							return $volumeNumber;
						}
					}
				} else {

					return 0;
				}
				

				return $volumeNumber;

			}

			public function getSecondVolumeNumber($callNumber) {

				$offset = $this->hasVolumeNumber($callNumber) + 2 + strlen($this->getVolumeNumber($callNumber));
				$secondVolumeNumber = 0;
				if($this->hasVolumeNumber($callNumber))  {
					foreach($this->makeAmask($callNumber) as $key=>$a)  {
						if($a === '-') { continue;  }
						if($key>=$offset && $a === 'I')  {
							$secondVolumeNumber .= $callNumber[$key];
						}
					}
				}

				return $secondVolumeNumber;
			}

			public function storeSortKeyString($callNumber,$barcode ) {

				$prefix = $this->getPrefix($callNumber);
				$author = $this->getAuthor($callNumber);
				$cutterNumber = $this->getCutterNumber($callNumber);
				$decimalNumber = $this->getCutterDecimal($callNumber);
				$titleLetters = $this->getTitle($callNumber);
				$date = $this->getDate($callNumber);
				$secondDate = $this->getDateRange($callNumber);
				$firstVolumeNumber = $this->getVolumeNumber($callNumber);
				$secondVolumeNumber = $this->getSecondVolumeNumber($callNumber);

				$skey = new MissouriDeweySortKey;

				$skey->user_id = Auth::user()->id;
				$skey->barcode = $barcode;
				$skey->call_number = $callNumber;
				$skey->prefix = $prefix;
				$skey->author = $author;
				$skey->cutter_number = $cutterNumber;
				$skey->decimal_number = $decimalNumber;
				$skey->title_letters = $titleLetters;
				$skey->date = $date;
				$skey->date2 = $secondDate;
				$skey->volume_number1 = $firstVolumeNumber;
				$skey->volume_number2 = $secondVolumeNumber;

				$skey->save();


			}


		}
