<?php

namespace App\Services;
use App\Models\User;

use App\Actions\Dewey\MakeAmask;
use App\Actions\Dewey\GetPrefixType;
use Carbon\Carbon;

class DeweyCallNumber {

		public $user;

		public function __construct(User $user)  {
				$this->user = $user;
		}

		public function getAmask($callNumber) {
				
				$callNumberArray = str_split($callNumber);

				foreach($callNumberArray as $c) {

						if (ord($c) >= 65 AND ord($c) <= 90) { $amask[] = 'A'; }
						elseif(ord($c) >= 97 AND ord($c)<=122) { $amask[] = 'a'; }
						elseif(ord($c)>=48 AND ord($c)<=57) { $amask[] = 'I'; }
						elseif(ord($c) === 32) { $amask[] = '_'; }
						elseif($c === '.') { $amask[] = 'D'; }
						elseif($c === '-') { $amask[] = '-'; }

				}
				return $amask;
		}

		public function getUserEmail($id) {
				return $this->user->find($id)->email; 
		}


		public function makeSmask($callNumber) {

				$smask = implode('', $this->getAmask($callNumber));

				return $smask;
		}

		public function getPrefixType($callNumber) {

				return GetPrefixType::execute($callNumber);
		}

		public function hasDecimal($callNumber)  {
				return strpos($callNumber,'.');
		}

		public function getPrefix($callNumber) {
				$prefix = null;

				foreach(MakeAmask::execute($callNumber) as $key=>$m) {
						if($m === 'A') { $prefix .= $callNumber[$key]; }
						else {

								return $prefix;
						}

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

				return $auth;

		}

		public function getCutter($callNumber) {

				$callNumberArray=str_split($callNumber);
				$offset = strpos($this->makeSmask($callNumber),'I');
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

		public function getDecimal($callNumber) {

				$callNumberArray=str_split($callNumber);
				$offset = strpos($this->makeSmask($callNumber),'D');
				$decimalNumber = null;
				foreach($callNumberArray as $key=>$c) {
						if(ctype_digit($c) AND $key > $offset) {
								$decimalNumber .= $c;
						}

						if(!ctype_digit($c) AND $key > $offset) {
								return $decimalNumber;
						}
				}

				return $decimalNumber;


		}

		public function getTitle($callNumber) {

				$callNumberArray=str_split($callNumber);
				$offset = strpos($this->makeSmask($callNumber),'I');
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

				return $title;
		}

		public function getDate($callNumber) {
				$title = $this->getTitle($callNumber);

				$toplineEnd = strpos($callNumber,$title)+strlen($title);
				
				$callNumberArray = str_split($callNumber);
				$date = null;
				foreach($callNumberArray as $key=>$c) {
						if(ctype_digit($c) AND $key >$toplineEnd) {
								$date .= $c;
						}

						if(!ctype_digit($c) AND $key > $toplineEnd) {

								return $date;
						}
				}

				$currentYear = Carbon::now()->year;

				if(strlen($date) === 4 AND $date<=$currentYear AND $date>1200) {
						return $date;
				}  else {
						return false;
				}

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
						foreach(MakeAmask::execute($callNumber) as $key=>$a)  {
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
				$offset = $this->hasVolumeNumber($callNumber)+1;

				$volumeNumber = null;

				if($offset) {  
						foreach(MakeAmask::execute($callNumber) as $key=>$a) {
								if($key > $offset && $a === 'I') {
										$volumeNumber .= $callNumber[$key];
								} elseif($key>$offset && $a!='I') {
										return $volumeNumber;
								}
						}
				}

				return $volumeNumber;

		}

		public function getSecondVolumeNumber($callNumber) {

				$offset = $this->hasVolumeNumber($callNumber) + 2 + strlen($this->getVolumeNumber($callNumber));
				$secondVolumeNumber = null;
				if($this->hasVolumeNumber($callNumber))  {
						foreach(MakeAmask::execute($callNumber) as $key=>$a)  {
								if($a === '-') { continue;  }
								if($key>=$offset && $a === 'I')  {
										$secondVolumeNumber .= $callNumber[$key];
								}
						}
				} 

				return $secondVolumeNumber;
		}
		 
		
}



