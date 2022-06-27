<?php

namespace App\Http\Controllers;

use App\Models\Alma;
use App\Models\Shelf;
use App\Models\SortKey;
use App\Models\Status;
use App\Models\UnprocessedCallNumber;
use App\Traits\BookShelfTrait;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class AlmaRequestController extends Controller
{
	use UserTrait;
	use BookShelfTrait;

	public function processAlma(Request $request) {
		$barcode = $request->barcode;
		$user_id = $request->user()->id;
		$almaParameters = Alma::almaParameters($barcode);

		$callNumber = $almaParameters['call_number'];
		$title = $almaParameters['title'];
		$status = 'Available';

		if($this->checkNewBook($barcode,$user_id) === 0) { 

			try {
				$this->storeKey($callNumber,$barcode,$user_id);

			} catch (\Illuminate\Database\QueryException $ex) {

				$upc = new UnprocessedCallNumber;
				$upc->user_id = $user_id;
				$upc->barcode = $barcode;
				$upc->call_number = $callNumber;
				$upc->title = $title;
				$upc->save();

				Status::where('user_id',$user_id)->delete();

				if(!$barcode) { $barcode = 'Empty'; }
				$stat = new Status;
				$stat->user_id = $user_id;
				$stat->barcode = $barcode;
				$stat->status = "Call Number '$callNumber' could not be processed";
				$stat->save();

				return Redirect::route('shelf');		 
			}

			$this->placeBookOnShelf($user_id,$barcode,$callNumber,$title,$status);
			$cposition = SortKey::correctShelfPosition($barcode,$user_id);
			Shelf::resetCpositions($barcode,$cposition,$user_id);

			if($this->countShelfErrors($user_id)>1) {

				$this->fillSubsequence($user_id);
				$this->fillMoves($user_id);
			}
			// Insert barcode into clicked_barcodes table

			Status::where('user_id',$user_id)->delete();

			$stat = new Status;
			$stat->user_id = $user_id;
			$stat->barcode = $barcode;
			$stat->status = $status;
			$stat->save();

			return Redirect::route('shelf');		 

		}  else {


			$nextMoverBarcode = $this->nextMoverBarcode($user_id);

			if($nextMoverBarcode->first() && $nextMoverBarcode[0]->barcode === $barcode)
			{
				$title = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('title')[0];
				$callnumber = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('callnumber')[0];



				return Redirect::route('correction',['user_id'=>$user_id,'barcode'=>$barcode]);

			} else {

				return "Rescanned wrong book";
			}
			 
		}	
	}
}
