<?php


namespace App\Facades\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Map;
use App\Models\MapKey;
use App\Models\Shelf;
use Carbon\Carbon;

class MapsCallnumberService1 {


	public function correctShelfPosition()
	{

	}

	public function getFirstComponentPosition($callNumber, $patternMask)
	{
		$amask = $this->makeAmask($callNumber);
		$smask = $this->makeStringMask($callNumber);

		return strpos($smask,$patternMask);

	}

	public function getSecondComponentPosition($callNumber, $patternMask)
	{
		$smask = $this->makeStringMask($callNumber);

		$skip = $this->getFirstComponentPosition($callNumber,$patternMask);

		return strpos($smask,$patternMask,$skip);

	}

	public function sliceArray($arraySegment,$swath)
	{
		return array_slice($arraySegment,$swath);
	}

	public function getPositionOfFirstSpace($callNumber)
	{
		
		$smask = $this->makeStringMask($callNumber);
		
		return strpos($smask,'_');
	}

	public function getTopLineString($callNumber)
	{
		$smask = $this->makeStringMask($callNumber);
		// Get part of call number string preceeding the first space
	
		return substr($callNumber,$this->getPositionOfFirstSpace($callNumber));
		
	}

	public function getNextLineString($callNumber)
	{

		return substr($callNumber,0,$this->getPositionOfFirstSpace($callNumber));
	}

	public function getCallNumberKey($callNumber)
	{
		$toplineString = $this->getTopLineString($callNumber);

		#Class


		$classNumber = $this->classNumber($toplineString);

		##

		#Class Decimal

		$classDecimalNumber = $this->classDecimal($arraySegment);

		##

		#First Dotted Cutter Letter
		
		$firstDottedCutterLetter = $this->firstDottedCutterLetter($callNumber);

		##

		#First Dotted Cutter Decimal

		$firstCutterDecimal = $this->firstCutterDecimal($arraySegment);

		##

		#Neighborhood Cutter

		$neighborhoodCutter = $this->neighborhoodCutter($arraySegment);
		##

		#First Undotted Cutter Decimal

		$firstUndottedCutterLetter = $this->getFirstUndottedCutterLetter($arraySegment);	

		##
		
		##################################################################################################
		#
		# End of Top Line
		#
		#
		#
	}

	public function getNextLineCallNumberKey($callNumber)
	{
#
		#Scale Name

		##
		#
		#
	


		#Scale Number
			
		##

		#Date of Use

		##
#
		#Second Dotted Cutter Letter
#		
#		##
#
		#Second Dotted Cutter Decimal

		##
#
		#Date of Reproduction

		##

		#Sheet Number

		##


		$mapKey = [];
		$classLetter = 'G';

		$classNumber = $this->classNumber($callNumber);
		$depthOfClassDecimal = strlen($this->classNumber($callNumber)) +1;
		$arraySegment = array_slice($this->makeSmask($callNumber),$depthOfClassDecimal);
		$classDecimalNumber = $this->classDecimal($arraySegment);

		if($classDecimalNumber)
		{
			$depthOfClassDecimal = strlen($this->classNumber($callNumber)) +1;
		} else {
			$depthOfClassDecimal = 1;
		}
		$arraySegment = array_slice($arraySegment,$depthOfClassDecimal);
		$firstDottedCutterLetter = $this->firstDottedCutterLetter($callNumber);

		$lengthOfFirstCutterLetter = strlen($firstDottedCutterLetter);
		$arraySegment = array_slice($arraySegment,$lengthOfFirstCutterLetter);
		$firstCutterDecimal = $this->firstCutterDecimal($arraySegment);
		$arraySegment = array_slice($arraySegment, strlen($firstCutterDecimal));
		//dd($this->getDateInLastPosition($callNumber));
		$scale = $this->getScale($callNumber);

		if($this->hasNeighborhoodCutter($callNumber))
		{
			$neighborhoodCutterPosition = $this->hasNeighborhoodCutter($callNumber) + 1;
			$arraySegment = array_slice($this->makeSmask($callNumber),$neighborhoodCutterPosition);
			$neighborhoodCutter = $this->neighborhoodCutter($arraySegment);
			$arraySegment = array_slice($arraySegment, strlen($neighborhoodCutter));

			$firstUndottedCutterLetter = $this->getFirstUndottedCutterLetter($arraySegment);	
			$arraySegment = array_slice($arraySegment, strlen(strlen($firstUndottedCutterLetter)));
			$firstUndottedCutterDecimal = $this->getFirstUndottedCutterDecimal($arraySegment);

		} else {
			// Check for undotted cutter after first dotted cutter
			$neighborhoodCutter = 0;
			$firstUndottedCutterLetter = $this->getFirstUndottedCutterLetter($arraySegment);	
			$arraySegment = array_slice($arraySegment, strlen($firstUndottedCutterLetter));

			$firstUndottedCutterDecimal = $this->getFirstUndottedCutterDecimal($arraySegment);

		}


		if($this->hasMapDate($callNumber))
		{
			$arraySegment = array_slice($this->makeSmask($callNumber), $this->hasMapDate($callNumber)+1);
			$mapDate = $this->mapDate($arraySegment);

		} else {
			$mapDate = 0;
		}

		if($this->callNumberHasScale($callNumber))
		{
			$scale = $this->getScale($callNumber);
		}

		if($this->getDateInLastPosition($callNumber))
		{
			$arraySegment = array_slice($this->makeSmask($callNumber),$this->getDateInLastPosition($callNumber)+1);
			$publicationDate = $this->publicationDate($arraySegment);
		} else {
			$publicationDate = 0;
		}

		if($this->countDottedCutters($callNumber) > 1) {


			$secondCutterLetter = $this->getSecondDottedCutterLetter($callNumber);

			$secondDottedCutterDecimal = $this->getSecondDottedCutterDecimal($callNumber) ;
		} else {

			$secondCutterLetter = null;

			$secondDottedCutterDecimal = null;

		}

		$mapKey = [

			'classLetter' => 'G',
			'classNumber' => $classNumber,
			'classDecimalNumber' => $classDecimalNumber,
			'firstCutterLetter' => $firstDottedCutterLetter,
			'firstCutterDecimal' => $firstCutterDecimal,
			'neighborhoodCutter' => $neighborhoodCutter,
			'scale' => $scale,
			'firstUndottedCutterLetter' => $firstUndottedCutterLetter,
			'firstUndottedCutterDecimal' => $firstUndottedCutterDecimal,
			'secondDottedCutterLetter' => $secondCutterLetter,
			'secondDottedCutterDecimal' => $secondDottedCutterDecimal,
			'publicationDate' => $publicationDate,
			'yearOfReproduction' => $mapDate,
		]; 

		return  $mapKey;
	}

	public function sheetName($callNumber)
	{

	}

	public function dateCutterDate($callNumber)
	{
		$dateCutterPosition = strpos($this->makeStringMask($callNumber), '_IIIIDAI_');

		return substr($callNumber,$dateCutterPosition+1,4);


	}

	public function dateCutterCutter($callNumber)
	{
		$dateCutterPosition = strpos($this->makeStringMask($callNumber), '_IIIIDAI_');

		if($dateCutterPosition)
		{

			return substr($callNumber,$dateCutterPosition+6,2);
		} else {

			return false;
		}

	}


	public function findWord($callNumber)
	{
		$wordPosition = strpos($this->makeStringMask($callNumber), '_Aa');

		$sheetName = null;
		if($wordPosition)
		{
			foreach($this->makeAmask($callNumber) as $key=>$w)
			{
				if($key>=$wordPosition)
				{
					if($w === 'A' OR $w ==='a' OR $w === '_')
					{
						$sheetName .= $callNumber[$key];
					} else {

						return $sheetName;
					}
				}
			}
		} else {

			return null;

		}
	}

	public function hasSheetName($callNumber)
	{
		return strpos($callNumber,'sheet');
	}



	public function getDateInLastPosition($callNumber)
	{
		if(strpos($this->makeStringMask($callNumber),'_IIII',-5))

		{
			$pDate = substr($callNumber,-4);

			if(is_numeric($pDate))
			{
				return $this->isDate($pDate);
			} else {

				return null;
			}
		} else {

			return null;
		}
	}

	public function isDate($possibleDate)
	{
		if($possibleDate <= date("Y") )
		{
			return $possibleDate;
		} else {

			return 0;
		}
	}

	public function classNumber($callNumber)
	{
		$mapClassNumber = null;

		foreach($this->makeSmask($callNumber) as $key=>$c)
		{
			if($key === 0) {continue;}
			if(is_numeric($c)) {
				$mapClassNumber .= $c;
			} else {
				return $mapClassNumber;
			}
		}
		return $mapClassNumber;
	}

	public function classDecimal($arraySegment) {
		if($arraySegment[0] != '.') { return null; }
		$classDecimal = null;
		foreach ($arraySegment as $key=>$s) {
			if($s === '.') { continue; }
			if (is_numeric($s)) {
				$classDecimal .= $s;
			} else {

				return $classDecimal;
			}


		}

		return $classDecimal;
	}

	public function positionOfFirstSpace($callNumber)
	{
		return strpos($this->makeStringMask($callNumber),'_');
	}
	//Find the '.A' pattern
	public function hasFirstDottedCutter($callNumber)
	{
		$firstDottedCutterPatternPosition = strpos($this->makeStringMask($callNumber), 'DAI');
		
			return $firstDottedCutterPatternPosition; 
		
	}

	public function firstDottedCutterLetter($callNumber) 
	{
		$skip = $this->hasFirstDottedCutter($callNumber);
		if(!$skip) { return null; }
	
		$firstDottedCutterLetter = null;

		foreach($this->makeAmask($callNumber) as $key=>$c)
		{
			if($key<= $skip) { continue;}
				if($key>$skip AND ctype_alpha($callNumber[$key]))
				{
					$firstDottedCutterLetter .= $callNumber[$key];
				} else {
					return $firstDottedCutterLetter;
				}
			}

		}

	public function hasMapDate($callNumber)
	{

		return strpos($this->makeStringMask($callNumber),'_IIIID');
	}

	public function mapDate($arraySegment)
	{
		$mapDate = null;
		foreach($arraySegment as $a)	
		{
			if(ctype_digit($a))
			{
				$mapDate .= $a;
			} else {
				return $mapDate;
			}
		}
	}
	public function publicationDate($arraySegment) {
		$publicationDate = false;
		foreach ($arraySegment as $key=>$t) {
			if(ctype_digit($t)) {
				$publicationDate .= $t;
			} 

		}
		$year = date('Y');
		if(strlen($publicationDate) === 4 AND $publicationDate <= $year)
		{
			return $publicationDate;
		}

	}

	public function hasSvar($callNumber)
	{
		return strpos($callNumber,'svar');
	}

	public function cutterLetter($tmask,$depthOfSecondCutter) {
		$cutterLetter = false;
		foreach ($tmask as $key=>$t) {
			if(ctype_alpha($t)) {
				$cutterLetter .= $t;
			} else {
				return $cutterLetter;
			}

		}
		return $cutterLetter;

	}

	public function firstCutterDecimal($arraySegment) {
		$firstCutterDecimal = false;

		foreach ($arraySegment as $key=>$s) {
			if(is_numeric($s)) {
				$firstCutterDecimal .= $s;
			}  else {

				return $firstCutterDecimal;
			}

		}
		return $firstCutterDecimal;
	}

	public function neighborhoodCutter($arraySegment) {
		$neighborhoodCutter = false;
		foreach ($arraySegment as $key=>$s) {

			if ($key >= 0  AND $key <= 2) {
				$neighborhoodCutter .= $s;
			} elseif($key > 2) {

				return $neighborhoodCutter;

			}
		}
		return $neighborhoodCutter;
	}

	public function callNumberHasScale($callNumber)
	{
		$position = strpos($this->makeStringMask($callNumber),'_aII');

		if($position AND $this->makeSmask($callNumber)[$position+1] === 's')
		{
			return $position+1;
		} else {
			return false;
		}
	}

	public function hasSecondDottedCutter($callNumber)
	{
		return strpos($this->makeStringMask($callNumber),'DA', strpos($this->makeStringMask($callNumber),'DA')+1);
	}

	public function getSecondDottedCutterLetter($callNumber)
	{
		$secondDottedCutterLetter = null;
		foreach($this->makeSmask($callNumber) as $key=>$c)
		{
			if($key > $this->hasSecondDottedCutter($callNumber) AND ctype_alpha($c) )
			{
				$secondDottedCutterLetter .= $c;
			} elseif ($key > $this->hasSecondDottedCutter($callNumber) AND !ctype_alpha($c) )
			{ 
				return $secondDottedCutterLetter;				
			}	

		}

		return $secondDottedCutterLetter;
	}

	public function getSecondDottedCutterDecimal($callNumber)
	{
		$secondDottedCutterDecimal = null;
		$offset = $this->hasSecondDottedCutter($callNumber) + strlen($this->getSecondDottedCutterLetter($callNumber));
		foreach($this->makeSmask($callNumber) as $key=>$c)
		{
			if($key > $offset AND ctype_digit($c) )
			{
				$secondDottedCutterDecimal .= $c;
			} elseif ($key > $this->hasSecondDottedCutter($callNumber) AND !ctype_alpha($c) )
			{ 
				return $secondDottedCutterDecimal;				
			}	

		}

		return $secondDottedCutterDecimal;

	}

	public function makeAmask($callNumber)
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

	public function countDottedCutters($callNumber)
	{
		return substr_count($this->makeStringMask($callNumber),'DA');
	}



	public function getClass($callNumber)
	{
		$callNumberArray = str_split($callNumber);
		$callNumberClass = null;
		foreach($callNumberArray as $c)
		{
			if(ctype_alpha($c))
			{
				$callNumberClass .= $c;
			} else {
				return $callNumberClass;
			}
		}
	}


	public function processAlphabeticString($segment)
	{
		$segmentArray = str_split($segment);
		$alphabeticSegment = null; 
		foreach($segmentArray as $key=>$c)
		{
			if(ctype_alpha($c)) 
			{
				$alphabeticSegment .= $c;

			} else {
				return $alphabeticSegment;
			}
		}

		return null;
	}
	public function hasNeighborhoodCutter($callNumber)
	{
		return strpos($callNumber, ':');
	}
	public function processNumericString($segment)
	{
		$segmentArray = str_split($segment);
		$numericSegment = null;
		foreach($segmentArray as $key=>$c)
		{
			if(ctype_digit($c)) 
			{
				$numericSegment .= $c;

			} else {
				return $numericSegment;
			}
		}

		return null;
	}

	public function getScale($callNumber)
	{
		$offset = $this->callNumberHasScale($callNumber);

		$scale =false;

		foreach($this->makeAmask($callNumber) as $key=>$t)
		{
			if($key > $offset AND ctype_digit($this->makeSmask($callNumber)[$key]))
			{
				$scale .= $this->makeSmask($callNumber)[$key];
			} 

			if($key > $offset AND !ctype_digit($this->makeSmask($callNumber)[$key]))
			{
				break;
			} 

		}

		return $scale;
	}


	public function scale($tmask) {
		$scale = false;
		foreach ($tmask as $key=>$t) {
			if($key === 0 AND $t === 's') {
				continue;
			}
			if(ctype_digit($t)) {
				$scale .= $t;
			} else {
				return $scale;
			}

		}
		return $scale;

	}
	public function secondCutterDecimal($tmask,$depthOfSecondCutterDecimal) {
		$secondCutterDecimal = false;
		foreach ($tmask as $key=>$t) {
			if(ctype_digit($t)) {
				$secondCutterDecimal .= $t;
			} else {
				return $secondCutterDecimal;
			}

		}
		return $secondCutterDecimal;

	}
	public function callNumberArray($callNumberString) {


		$callNumberArray = str_split($callNumberString);
		$smask = str_split($callNumberString);


		return $this->aclass->execute($callNumberArray);



	}



	public	function getDottedCutters($astring, $needle, $offset = 0, &$results = array()) {               

		$offset = strpos($astring, $needle, $offset);
		if($offset === false) {
			return $results;           
		} else {
			$results[] = $offset;
			return $this->getDottedCutters($astring, $needle, ($offset + 1), $results);
		}


	}

	public function getFirstUndottedCutterLetter($arraySegment){

		$firstUndottedCutterLetter = null;	
		foreach($arraySegment as $a)
		{
			if(ctype_alpha($a))
			{
				if($a === 's')
				$firstUndottedCutterLetter .= $a;
			} else {
				return $firstUndottedCutterLetter;
			}
		}

	}

	public function getFirstUndottedCutterDecimal($arraySegment){

		$firstUndottedCutterDecimal = null;	
		foreach($arraySegment as $a)
		{
			if(ctype_digit($a))
			{
				$firstUndottedCutterDecimal .= $a;
			} else {
				return $firstUndottedCutterDecimal;
			}
		}

	}
	public function getTruncatedAmask($amask, $depth){


	}

	public function getTruncatedSmask($smask, $depth){

		return array_slice($smask,$depth);


	}

	public function normalizeWholeNumbersStringsToTenPlaces($wholeNumberString){

		$numberOfAddedZeros = 10 - strlen($wholeNumberString);
		$a = '0';
		$b = $wholeNumberString;
		for ($i = 0; $i < $numberOfAddedZeros; $i++) {
			$normalizedNumberString = $a . $b;
			$b = $normalizedNumberString;
		}

		return $normalizedNumberString;
	}

	public function getFirstSpace($astring){
		return strpos($astring,'_');

	}

	public function getWords($astring,$smask){

		$depthOfFirstSpace = $this->getFirstSpace($astring); 

	}


	public function getPositionOfScale($astring,$callNumberString){

		$svar = strpos($callNumberString,' svar');
		$scale = strpos($astring,'_sII');

		if($svar) {
			return $svar;
		} elseif($scale) {
			return $scale;

		} else {

			return false;
		}


	}

	public function getSheetName($callNumber)
	{
		if(strpos($this->makeStringMask($callNumber),'I_'));
		{
			foreach($this->makeAmask($callNumber) as $key=>$s)
			{
				if($s === 'A' OR $s === '_')
				{
					$sheet .= $s;
				}
			}

		}

	}


	public function processCallNumber($callNumberString, GetClassAction $classAction, MakeAmaskAction $mask){

		// Array of literal call number characters
		$characterArray = str_split($callNumberString);
		$smask = str_split($callNumberString);
		// Array of character type placeholders
		$maskArray = $mask->execute($characterArray);
		$amask = $mask->execute($smask);
		// String of character type placeholders
		$maskString = implode('', $maskArray);
		$astring = implode('', $amask);

		$callNumberClass = GetClassAction::execute($smask);
		$classDepth = strlen($callNumberClass) +1;
		$tmask = array_slice($smask,$classDepth);

		$classDecimal = GetClassDecimalAction::execute($tmask,$astring,$classDepth);

		$depthOfNeighborhoodCutter = strpos($astring,'C');
		if($depthOfNeighborhoodCutter) {

			$tmask = array_slice($smask,$depthOfNeighborhoodCutter+1);
			$neighborhoodCutter = GetNeighborhoodCutterAction::execute($tmask,$depthOfNeighborhoodCutter);
			//Get depth
			$depthOfUndottedCutter = $depthOfNeighborhoodCutter + strlen($neighborhoodCutter);  

			$undottedSubjectCutter = $this->getUndottedSubjectCutter();

		} else {

			$neighborhoodCutter = null;
		}

		// Immediately after 1st cutter decimal look for AI pattern in amask

		$depthOfPublicationDate = strpos($astring,'_IIII')+1;
		$tmask = array_slice($smask,$depthOfPublicationDate);
		$publicationDate = GetPublicationDateAction::execute($tmask,$depthOfPublicationDate);

		$depthOfScale = $this->getPositionOfScale($astring,$callNumberString);

		$depthOfScale = strpos($astring,'_sII');
		$depthOfSvar = strpos($callNumberString, 'svar');

		if($depthOfSvar) { 

			$scale = 'svar';
		}

		if($depthOfScale) {
			$dos = $depthOfScale + 1;
			$tmask = array_slice($smask, $dos);
			$scale = GetScaleAction::execute($tmask);

		}


		$dottedCutterPositions = $this->getDottedCutters($astring,'DA');
		$dottedCutterLetter1 = '';

		for ($i = 0; $i < 2; $i++) {
			$variableNumber = $i+1;
			if($dottedCutterPositions[$i]) {
				$depth = $dottedCutterPositions[$i] + 1;
				${"dottedCutterLetter$variableNumber"} = GetConcurrentLetterStringAction::execute									($smask,$depth);
				$decimalDepth = $depth+strlen(${"dottedCutterLetter$variableNumber"});
				${"dottedCutterDecimal$variableNumber"} = GetConcurrentNumberStringAction::execute($smask,$decimalDepth);
			} else {

				${"dottedCutterLetter$variableNumber"} = '';
				${"dottedCutterDecimal$variableNumber"} = '';
			} 

		}

		$wholeNumber = $this->normalizeWholeNumbersStringsToTenPlaces(22);

	}

}
