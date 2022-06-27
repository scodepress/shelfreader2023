<?php
namespace App\Actions\Maps;


class GetPublicationDateAction 
{
	public function __construct()
	{
		

	}

	public static function execute($tmask,$depthOfPublicationDate) {
		$publicationDate = false;
		foreach ($tmask as $key=>$t) {
			if(ctype_digit($t)) {
				$publicationDate .= $t;
			} else {
				return $publicationDate;
			}
			
		}
		return $publicationDate;
		
	}
}
