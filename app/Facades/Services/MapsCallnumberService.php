<?php

namespace App\Facades\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Map;
use App\Models\MapKey;
use App\Models\Shelf;
use Carbon\Carbon;

class MapsCallnumberService {

	public function makeAmask(string $callNumber)
	{
		$callNumberArray = str_split($callNumber);

		foreach($callNumberArray as $c) {

			if (ord($c) >= 65 AND ord($c) <= 90) { $amask[] = 'A'; }
			elseif(ord($c) >= 97 AND ord($c)<=122) { $amask[] = 'a'; }
			elseif(ord($c)>=48 AND ord($c)<=57) { $amask[] = 'I'; }
			elseif(ord($c) === 32) { $amask[] = '_'; }
			elseif($c === '.') { $amask[] = 'D'; }
			elseif($c === '-') { $amask[] = '-'; }
			elseif($c === ':') { $amask[] = ':'; }

		}

		return $amask;
	} 

	public function makeSmask($callNumber)
	{
		return str_split($callNumber);
	}

	public function makeStringMask($callNumber)
	{
		return implode('',$this->makeAmask($callNumber));
	}

	public function getPositionOfFirstSpace($callNumber)
	{
		$smask = $this->makeStringMask($callNumber);

		return strpos($smask,'_');
	}

	public function getTopLineString($callNumber)
	{
		$firstSpace = $this->getPositionOfFirstSpace($callNumber);
		if($firstSpace !== false)
		{
			return substr($callNumber,0,$this->getPositionOfFirstSpace($callNumber));
		} else {
			return $callNumber;
		}

	}

	public function getToplineArray($callNumber)
	{
		return str_split($this->getTopLineString($callNumber));

	}


	public function getNextLineString(string $callNumber)
	{
		$offset = strlen($this->getTopLineString($callNumber));
		return substr($callNumber,$offset);
	}

	public function getCallnumberKey($callNumber)
	{
		return ($this->getToplineKeyArray($callNumber) + $this->getNextLineKeyArray($callNumber));
	}

	public function getToplineKeyArray($callNumber)
	{
		//$callNumber = 'G4110.123.P5:3B8E635 s100 1930.U5 1960 sheet 4-25';
		$toplineString = $this->getTopLineString($callNumber);
		$toplineStringArray = str_split($toplineString);
		$toplineArrayMask = $this->makeAmask($toplineString);

		$positionOfFirstCharacterInNeighborhoodCutter = strpos($toplineString,':');

		#Class
		#
		$classLetter = $toplineString[0];
		$character2 = $toplineString[1];

		if($classLetter === 'G' AND is_numeric($character2))
		{
			$classLetter = 'G';
		} else {

			return "This is not a Maps call number";
		}

		$topline[] = null;
		$classNumber = null;

		foreach($toplineStringArray as $key=>$t)
		{
			if($key === 0) { continue; }
			if(is_numeric($t))
			{
				$classNumber .= $t;
			} else {

				break;
			}
		}

		$cut = strlen($classLetter) + strlen($classNumber);

		$toplineString = substr($toplineString,$cut);
		$toplineStringArray = array_slice($toplineStringArray,$cut);
		$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => null,
				'firstDottedCutterLetter' => null,
				'firstDottedCutterDecimal' => null,
				'neighborhoodCutter' => null,
				'firstUndottedCutterLetter' => null,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}
		##

		#Class Decimal

		if($toplineArrayMask[0]==='D' AND $toplineArrayMask[1] ==='I' )
		{
			$classDecimal = null;

			foreach($toplineStringArray as $key=>$t)
			{
				if($key === 0 AND $t==='.') { continue; }
				if(is_numeric($t))
				{
					$classDecimal .= $t;
				} else {

					break;
				}
			}

			$classDecimalNumber = $classDecimal;

			$cut = strlen($classDecimalNumber)+1;
			$toplineString = substr($toplineString,$cut);
			$toplineStringArray = array_slice($toplineStringArray,$cut);
			$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		} else {

			$classDecimalNumber = null;

		}


		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => null,
				'firstDottedCutterDecimal' => null,
				'neighborhoodCutter' => null,
				'firstUndottedCutterLetter' => null,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}

		//$toplineArray[] = $classDecimalNumber;


		##

		#First Dotted Cutter Letter


		$firstDottedCutterLetter = null;
		if($toplineArrayMask[0]==='D' AND $toplineArrayMask[1] ==='A' )
		{
			$firstDottedCutterLetter = $toplineStringArray[1];

			$cut = 2;
			$toplineString = substr($toplineString,$cut);
			$toplineStringArray = array_slice($toplineStringArray,$cut);
			$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		} else {

			$firstDottedCutterLetter = null;

		}

		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => null,
				'neighborhoodCutter' => null,
				'firstUndottedCutterLetter' => null,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}

		//$toplineArray[] = $firstDottedCutterLetter;
		##

		#First Dotted Cutter Decimal

		$firstDottedCutterDecimal = null;

		if($toplineArrayMask[0]==='I')
		{

			foreach($toplineStringArray as $key=>$t)
			{
				if(!is_numeric($t)) 
				{ 
					break;
				} else { 

					$firstDottedCutterDecimal .= $t;
				} 

			}

			$cut = strlen($firstDottedCutterDecimal);
			$toplineString = substr($toplineString,$cut);
			$toplineStringArray = array_slice($toplineStringArray,$cut);
			$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		}


		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => $firstDottedCutterDecimal,
				'neighborhoodCutter' => null,
				'firstUndottedCutterLetter' => null,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}

		//	$toplineArray[] = $firstDottedCutterDecimal;

		##

		#Neighborhood Cutter

		$neighborhoodCutter = null;
		if($positionOfFirstCharacterInNeighborhoodCutter !== false)
		{
			$neighborhoodCutter = null;
			foreach($toplineStringArray as $key=>$t)
			{
				if($t === ':') { continue; }
				if($key>3) { break; }
				$neighborhoodCutter .= $t; 
			}

			$cut = 4; // Adding one to account for ':'
			$toplineString = substr($toplineString,$cut);
			$toplineStringArray = array_slice($toplineStringArray,$cut);
			$toplineArrayMask = array_slice($toplineArrayMask,$cut);


		}

		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => $firstDottedCutterDecimal,
				'neighborhoodCutter' => $neighborhoodCutter,
				'firstUndottedCutterLetter' => null,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}

		//	$toplineArray[] = $neighborhoodCutter;
		##

		#First Undotted Cutter Letter

		$firstUndottedCutterLetter = null;	

		if($toplineArrayMask[0]==='A')

		{
			$firstUndottedCutterLetter = $toplineStringArray[0];

			$cut = 1;
			$toplineString = substr($toplineString,$cut);
			$toplineStringArray = array_slice($toplineStringArray,$cut);
			$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		} 

		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => $firstDottedCutterDecimal,
				'neighborhoodCutter' => $neighborhoodCutter,
				'firstUndottedCutterLetter' => $firstUndottedCutterLetter,
				'firstUndottedCutterDecimal' => null,
			]; 

			return $toplineArray;
		}

		//$toplineArray[] = $firstUndottedCutterLetter;
		##

		#First Undotted Cutter Decimal
		$firstUndottedCutterDecimal = null;

		if($toplineArrayMask[0]==='I')
		{
			foreach($toplineStringArray as $key=>$t)
			{
				if(!is_numeric($t)) { break; }

				$firstUndottedCutterDecimal .= $t;
			} 

		}

		$cut = strlen($firstUndottedCutterDecimal);
		$toplineString = substr($toplineString,$cut);
		$toplineStringArray = array_slice($toplineStringArray,$cut);
		$toplineArrayMask = array_slice($toplineArrayMask,$cut);

		##
		if(!$toplineString)
		{
			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => $firstDottedCutterDecimal,
				'neighborhoodCutter' => $neighborhoodCutter,
				'firstUndottedCutterLetter' => $firstUndottedCutterLetter,
				'firstUndottedCutterDecimal' => $firstUndottedCutterDecimal,
			]; 

			return $toplineArray;
		} else {

			$toplineArray = [
				'callNumber' => $callNumber,
				'classLetter' => $classLetter,
				'classNumber' => $classNumber,
				'classDecimalNumber' => $classDecimalNumber,
				'firstDottedCutterLetter' => $firstDottedCutterLetter,
				'firstDottedCutterDecimal' => $firstDottedCutterDecimal,
				'neighborhoodCutter' => $neighborhoodCutter,
				'firstUndottedCutterLetter' => $firstUndottedCutterLetter,
				'firstUndottedCutterDecimal' => $firstUndottedCutterDecimal,
			]; 

			return $toplineArray;

		}


	}

	public function getNextLineKeyArray($callNumber)
	{
		#########################################################################################################
		#G4110.123.P5:3B8E635 s100 1930.U5 1960 sheet 4-25
		$nextlineString = $this->getNextLineString($callNumber);
		if(empty($nextlineString))
		{
			$nextlineArray = [

				'scaleName' => null,
				'scaleNumber' => null,
				'dateOfUse' => null,
				'secondDottedCutterLetter' => null,
				'secondDottedCutterDecimal' => null,
				'dateOfReproduction' => null,
			];

			return $nextlineArray; 
		}

		$nextlineStringArray = str_split($nextlineString);
		$nextlineArrayMask = $this->makeAmask($nextlineString);
		$nextlineStringMask = implode('',$nextlineArrayMask);
		$positionOfSvar = strpos($nextlineString,' svar');

		if($nextlineStringMask === '_IIII')
		{
			$dateOfUse = substr($nextlineString,1,4);

			$nextlineArray = [

				'scaleName' => null,
				'scaleNumber' => null,
				'dateOfUse' => $dateOfUse,
				'secondDottedCutterLetter' => null,
				'secondDottedCutterDecimal' => null,
				'specification' => null,
				'dateOfReproduction' => null,
			];

			return $nextlineArray; 
		}

		$scaleName = null;
		$scaleNumber = null;

		if($positionOfSvar !== false)
		{
			$scaleName = 'svar';
			$toplineArray[] = $scaleName;
			$scaleNumber = null;
			$toplineArray[] = $scaleNumber;

		}

		elseif($nextlineStringArray[1] === 's' AND $nextlineArrayMask[2] === 'I')
		{
			$scaleName = 's';
			$toplineArray[] = $scaleName;

			foreach($nextlineStringArray as $key=>$n)
			{
				if($key<2) { continue; }
				if(ctype_digit($n))
				{
					$scaleNumber .= $n;
				} else {

					break;
				}
			}

			$toplineArray[] = $scaleNumber;

		} else {

			$scaleName = null;
			$toplineArray[] = $scaleName;
			$scaleNumber = null;
			$toplineArray[] = $scaleNumber;
		}

		##
		if($this->hasSecondDottedCutter($nextlineStringMask,$callNumber) !== false)
		{
			//$dateOfUse = $this->getFirstDateWithSecondDottedCutter($nextlineStringMask,$callNumber,$nextlineStringArray); 
			$dateOfUse = $this->getDateOfUse($nextlineStringMask,$nextlineString);
			$toplineArray[] = $dateOfUse;

			$secondDottedCutterLetter = $this->getSecondDottedCutterLetter($nextlineStringMask,$nextlineStringArray);
			$toplineArray[] = $secondDottedCutterLetter;

			$secondDottedCutterDecimal = $this->getSecondDottedCutterDecimal($nextlineStringArray,$nextlineStringMask);
			$toplineArray[] = $secondDottedCutterDecimal;

			$specification = $this->getspecification($nextlineString,$nextlineStringMask,$nextlineStringArray);

			if($specification)
			{
				if($this->normalizeSpecificationNumbers($specification))
				{
					$specification = $this->normalizeSpecificationNumbers($specification);
				}
			}

			$toplineArray[] = $specification;

			//$positionOfDateOfUse = strpos($nextlineStringMask,'_IIII')+1;

			$dateOfReproduction = $this->getDateOfReproduction($nextlineString,$nextlineStringMask,$nextlineStringArray);

			$toplineArray[] = $dateOfReproduction;


		} else {

			//$dateOfUse = $this->getFirstDateWithoutSecondDottedCutter($nextlineStringMask,$callNumberingArray); 
			$dateOfUse = $this->getDateOfUse($nextlineStringMask,$nextlineString);
			$secondDottedCutterLetter = null;
			$secondDottedCutterDecimal = null;
			$specification = null;
			$dateOfReproduction = null;
		}
		##
		$nextlineArray = [

			'scaleName' => $scaleName,
			'scaleNumber' => $scaleNumber,
			'dateOfUse' => $dateOfUse,
			'secondDottedCutterLetter' => $secondDottedCutterLetter,
			'secondDottedCutterDecimal' => $secondDottedCutterDecimal,
			'specification' => $specification,
			'dateOfReproduction' => $dateOfReproduction,
		];

		return $nextlineArray; 

		#Sheet Number

		##


	}

	public function getSpecification($nextlineString,$nextlineStringMask,$nextlineStringArray)
	{
		//Find space after topline dotted cutter 
		$pos = strpos($nextlineStringMask,'DAI');

		$specification = null;

		if($pos !== false)
		{   
			foreach($nextlineStringArray as $key=>$n)
			{
				if($key<= $pos+3) { continue; }
				{
					$specification .= $nextlineString[$key];
				}
			}
		}

		if($specification) {

			$endSpec = substr($specification,-4);

			if(is_numeric($endSpec) AND $endSpec < date('Y'))
			{
				$specification = substr($specification,0,-4);
			}
		}

		return $specification;

	}


	public function printAllEven8CharacterNumericPalindromes()
	{
		//$start = 1000;
		//$end = 9999;

		for($i=1000; $i<9999; $i++)
		{
			if((int)($i/1000) % 2 === 0)
			{
			{

				$pals[] = "$i" . strrev($i);
			}

			}
		}

		print_r($pals);

	}


	public function normalizeSpecificationNumbers($specification)
	{
		preg_match_all('!\d+!', $specification, $matches);

		if($matches[0])
		{
			foreach($matches as $key=>$m)
			{
				$paddedString = str_pad($m[$key],7,'0',STR_PAD_LEFT);
				$specification = str_replace($m[$key],$paddedString,$specification);
			}

		} else {

			return null;
		}

		return $specification;
	}

	public function getDateOfReproduction($nextlineString,$nextlineStringMask,$nextlineStringArray)
	{
		//Find space after topline dotted cutter 
		$pos = strpos($nextlineStringMask,'DAI');

		$dateOfReproduction = null;
		$specification = null;

		if($pos !== false)
		{   
			foreach($nextlineStringArray as $key=>$n)
			{
				if($key<= $pos+3) { continue; }
				{
					$specification .= $nextlineString[$key];
				}
			}
		}

		if($specification) {
			$endSpec = substr($specification,-4);
			if(is_numeric($endSpec) AND $endSpec < date('Y'))
			{
				$dateOfReproduction .= $endSpec;
			}
		}

		return $dateOfReproduction;

	}

	public function getDateOfUse($nextlineStringMask,$nextlineString)
	{
		if(substr($nextlineStringMask,1,4) === 'IIII' AND substr($nextlineString,1,4) <= date('Y'))
		{
			return substr($nextlineString,1,4);
		} else {

			return null;
		}
	}


	public function hasSecondDottedCutter($nextlineStringMask)
	{
		return strpos($nextlineStringMask,'DA');
	}

	public function getSecondDottedCutterLetter($nextlineStringMask,$nextlineStringArray)
	{
		$pos =  strpos($nextlineStringMask,'DA');
		return $nextlineStringArray[$pos+1];
	}

	public function getSecondDottedCutterDecimal($nextlineStringArray,$nextlineStringMask)
	{
		$pos =  strpos($nextlineStringMask,'DA')+1;

		$secondDottedCutterDecimal = null;
		foreach($nextlineStringArray as $key=>$n)
		{
			if($key >= $pos)
			{
				if($nextlineStringMask[$key] === '_') { break; }
				if(is_numeric($n))
				{
					$secondDottedCutterDecimal .= $n;
				}
			}
		}

		return $secondDottedCutterDecimal;

	}

}
