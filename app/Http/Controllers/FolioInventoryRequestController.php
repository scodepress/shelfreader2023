<?php

namespace App\Http\Controllers;

use App\Models\FolioRequest;
use App\Models\Shelf;
use App\Models\Move;
use App\Models\DeweySortKey;
use App\Models\Subsequence;
use App\Models\Status;
use App\Models\SortKey;
use App\Models\Unfoundling;
use App\Models\UnprocessedCallNumber;
use App\Models\LocalInventoryOut;
use App\Traits\BookShelfTrait;
use Illuminate\Http\Request;
use App\Traits\FolioTrait;
use App\Traits\SkidmoreTrait;
use App\Exceptions\Handler;
use App\Models\Institution;
use App\Models\LocalInventory;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;

class FolioInventoryRequestController extends Controller
{
	use FolioTrait;
	use SkidmoreTrait;
	use BookShelfTrait;

	public function show() {




	}

	public function processFolio(Request $request) {

		$barcode = $request->barcode;
		$user_id = $request->user()->id;

		$institution_id = Auth::user()->institution_id;

		$itemInfoFromResponse = LocalInventory::folioResponse($barcode);

		if(empty($itemInfoFromResponse['items'])) { 

			Status::where('user_id',$user_id)->delete();

			$stat = new Status;
			$stat->user_id = $user_id;
			$stat->barcode = $barcode;
			$stat->status = 'Item not Found';
			$stat->save();

			$unfound = new Unfoundling;
			$unfound->user_id = $user_id;
			$unfound->barcode = $barcode;
			$unfound->save();

			return Redirect::route('shelfreader-inventory');	 

		}

		$callNumber = $this->parseFolioResponse($itemInfoFromResponse)['callNumber'];
		$callNumber = $this->processCallNumber($callNumber);


		if(empty($callNumber)) {

			$upc = new UnprocessedCallNumber;
			$upc->user_id = $user_id;
			$upc->barcode = $barcode;
			$upc->call_number = 'EMPTY';
			$upc->title = 'EMPTY';
			$upc->save();

			$unfound = new Unfoundling;
			$unfound->user_id = $user_id;
			$unfound->barcode = $barcode;
			$unfound->save();

			Status::where('user_id',$user_id)->delete();

			$stat = new Status;
			$stat->user_id = $user_id;
			$stat->barcode = $barcode;
			$stat->status = 'Empty Call Number';
			$stat->save();

			return Redirect::route('shelfreader-inventory');		 
		}

		if($institution_id === 1 || $institution_id === 2) {
			$callNumber = $this->processCallNumber($callNumber);
		}

		$title = $this->parseFolioResponse($itemInfoFromResponse)['title'];

		$status = $this->parseFolioResponse($itemInfoFromResponse)['status'];

		if($this->checkBook($barcode,$user_id) === 0) { 

			try {
				$key_save = $this->storeKey($callNumber,$barcode,$user_id);

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

				return Redirect::route('shelfreader-inventory');		 
			}	


			$this->placeBookOnShelf($user_id,$barcode,$callNumber,$title,$status,1);

			$lio = new LocalInventoryOut;
			$lio->user_id = $user_id;
			$lio->barcode = $barcode;
			$lio->save();

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

			return Redirect::route('shelfreader-inventory');		 

		} else {

			$nextMoverBarcode = $this->nextMoverBarcode($user_id);

			if($nextMoverBarcode->first() && $nextMoverBarcode[0]->barcode === $barcode)
			{
				$title = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('title')[0];
				$callnumber = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('callnumber')[0];



				return Redirect::route('inventory-correction',['user_id'=>$user_id,'barcode'=>$barcode]);

			} else {

				return "Rescanned wrong book";
			}
		}



	}


	public function correction(Request $request)
	{
		$user_id = $request->user()->id;
		$barcode = $request->barcode;
		$mpos = $this->mpos($user_id);

		//dd($mpos);

		$shelf_position = Shelf::where('barcode', $barcode)->where('user_id',$user_id)->pluck('shelf_position')[0];

		//dd($shelf_position);

		if($mpos > $shelf_position) {

			$book_right = $this->book_right($shelf_position,$mpos,$barcode,$user_id); 

			if($book_right) { Move::where('barcode',$barcode)->where('user_id',$user_id)->delete(); }   

		}

		else {

			$book_left = $this->book_left($shelf_position,$mpos,$barcode,$user_id);

			if($book_left) { Move::where('barcode',$barcode)->where('user_id',$user_id)->delete(); } 

		}



		return Redirect::route('shelfreader-inventory');



	}

	public function deleteMove($barcode)
	{
		Move::where('barcode', $barcode)->where('user_id',$user_id)->delete;

		//Return number of moves left in moves table
	}

	public function truncate(Request $request)
	{
		$user_id = $request->user()->id;
		$this->emptyShelves($user_id);

		return Redirect::route('shelfreader-inventory');
	}


	public function emptyShelves($user_id)
	{
		Shelf::where('user_id',$user_id)->delete();
		SortKey::where('user_id',$user_id)->delete();
		DeweySortKey::where('user_id',$user_id)->delete();
		Subsequence::where('user_id', $user_id)->delete();
		Move::where('user_id',$user_id)->delete();
		Status::where('user_id',$user_id)->delete();
		LocalInventoryOut::where('user_id',$user_id)->delete();
	}

}

