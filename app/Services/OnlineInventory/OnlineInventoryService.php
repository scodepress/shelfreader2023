<?php    
namespace App\Services\OnlineInventory;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\FolioAuthenticationToken;
use App\Models\ApiUrl;
use App\Models\FirstScan;
use App\Models\Shelf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class OnlineInventoryService {

	public function folioOnlineInventoryResponse($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,$effectiveLocationId) 
	{ 
		//$effectiveLocationId = FirstScan::where('user_id',Auth::id())->pluck('effective_location_id')[0];
		$institution_id = Auth::user()->institution_id;
		$token = FolioAuthenticationToken::where('institution_id', $institution_id)
			->orderByDesc('created_at')->pluck('auth_key')[0];
		$tenant = FolioAuthenticationToken::where('institution_id', $institution_id)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];

		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token

		])->get("$apiUrl/inventory/items?query=effectiveShelvingOrder >= $leftMostNormalizedCallNumber 
		and 
		effectiveShelvingOrder <= $rightMostNormalizedCallNumber&sortBy=effectiveShelvingOrder&limit=100;
		");

		$totalRecordCount = $response->json('totalRecords');
		$totalRecordCountLoops = ceil($response->json('totalRecords')/100);


		for($i=0; $i<$totalRecordCountLoops; $i++)
		{
			$offset = $i*100;
			$response = Http::withHeaders([
				'X-Okapi-Tenant' => $tenant,
				'x-okapi-token' => $token

			])->get("$apiUrl/inventory/items?query=effectiveShelvingOrder >= $leftMostNormalizedCallNumber 
			and 
			effectiveShelvingOrder <= $rightMostNormalizedCallNumber&effectiveLocation[id]=$effectiveLocationId&limit=100&offset=$offset;
			");

			$itemRecords[] = $response->json();
		}

		return Arr::flatten($itemRecords,1);

	}

	public function createResponseArray($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,$effectiveLocationId)
	{
		// Is this a complete result?
		// Are there multiple location id's
		DB::table('online_inventory_items')->where('user_id', Auth::id())->truncate();
		$response = $this->folioOnlineInventoryResponse($leftMostNormalizedCallNumber,$rightMostNormalizedCallNumber,
			$effectiveLocationId);


		foreach($response as $key1=>$r)
		{

			if(is_int($r)) { continue; }	
			foreach($r as $key2=>$i)
			{
					if(array_key_exists('barcode',$i))
					{
						$barcode = $i['barcode'];

					} else {

						$barcode = 123;
					}
				$inventory[] = [
					'user_id' => Auth::user()->id,
					'barcode' => $barcode,
					'call_number' => $response[$key1][$key2]['callNumber'],
					'title' => $response[$key1][$key2]['title'],
					'status' => $response[$key1][$key2]['status']['name'],
					'effective_shelving_order' => $response[$key1][$key2]['effectiveShelvingOrder'],
					'effective_location_id' => $response[$key1][$key2]['effectiveLocation']['id'],
					'effective_location_name' => $response[$key1][$key2]['effectiveLocation']['name'],
					'date' => date('Y-m-d'),
				]; 


			}



		}

		if(!empty($inventory)) {

			DB::table('online_inventory_items')->insert($inventory);
		} else {

		}
	}

	public function getMissingItems() 
	{
		$effective_location_id = FirstScan::where('user_id',Auth::id())->pluck('effective_location_id')[0];

		return DB::table('online_inventory_items')
			->select('*')
			->whereNotIn('barcode',function($query){
				$query->select('barcode')->from('shelves')
			     ->where('user_id',Auth::id());

			})
			->orderBy('effective_shelving_order')
			->where('user_id',Auth::id())
			->where('effective_location_id', $effective_location_id)
			->get();
	}


	public function uniqueShelfLocationIds($user_id){

		return DB::table('online_inventory_items')
			->select('effective_location_id')
			->where('user_id', $user_id)
			->groupBy('effective_location_id')
			->get();
	}

	public function uniqueShelfLocationNames($user_id){

		return DB::table('online_inventory_items')
			->select('effective_location_name')
			->where('user_id', $user_id)
			->groupBy('effective_location_name')
			->get();
	}

	public function uniqueStatuses($user_id)
	{

		return DB::table('online_inventory_items')
			->select('status')
			->where('user_id', $user_id)
			->groupBy('status')
			->get();
	}

	public function initialLocationName($user_id)
	{

		return DB::table('shelves')
			->select('effective_location_name')
			->where('user_id', $user_id)
			->orderBy('id')
			->take(1)
			->get();
	}

	public function initialLocationId($user_id)
	{

		return DB::table('shelves')
			->select('effective_location_id')
			->where('user_id', $user_id)
			->orderBy('id')
			->take(1)
			->get();
	}

	public function unexpectedItems($user_id,$effective_location_name,$firstBookBarcode,$lastBookBarcode){

		return  DB::table('shelves as s')
			->select('s.title','s.callnumber','s.barcode','s.effective_shelving_order',
				's.status','s.effective_location_id','s.effective_location_name')
				->join('online_inventory_items as i', function ($join) {
					$join->on('i.user_id','=','s.user_id')
	  ->on('s.barcode','=','i.barcode');
				})
				->where([
					['s.user_id', $user_id],
					['s.effective_location_name','!=',$effective_location_name],
					['s.barcode','<>',$firstBookBarcode],
					['s.barcode','<>',$lastBookBarcode],
				])
				->orWhere([
					['s.user_id', $user_id],
					['s.status','!=','Available'],
					['s.barcode','<>',$firstBookBarcode],
					['s.barcode','<>',$lastBookBarcode],
				])
				->groupBy('s.barcode')
				->groupBy('s.title')
				->groupBy('s.callnumber')
				->groupBy('s.effective_location_id')
				->groupBy('s.status')
				->groupBy('s.effective_shelving_order')
				->groupBy('s.effective_location_name')
				->orderBy('s.effective_shelving_order')
				->get();

	}
}
