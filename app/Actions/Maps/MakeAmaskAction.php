<?php
namespace App\Actions\Maps;


class MakeAmaskAction
{


	public static function execute($callno)
	{    

		foreach($callno as $key=>$c) // Get existing pattern in Ascii Decimal form
		{


			if (ord($c) >= 48 AND ord($c) <= 57)  { $amask[] = "I";  }
			if (ord($c) >= 65 AND ord($c) <= 90)  { $amask[] = "A";  }
			if (ord($c) != 115 AND ord($c) >= 97 AND ord($c) <= 122)  { $amask[] = "a"; }
			if (ord($c) === 115)  { $amask[] = "s"; }
			if (ord($c) === 46)  { $amask[] = "D"; } // Decimal point or period
			if (ord($c) === 58)   { $amask[] = 'C'; } // Colon
			if (ord($c) === 45)  { $amask[] = "-"; }
			if (ord($c) === 47)  { $amask[] = "/"; }
			if (ord($c) === 44)  { $amask[] = ","; }
			if (ord($c) === 32)  { $amask[] = "_"; } // This is a space

			}

			return $amask;

		}

	}
