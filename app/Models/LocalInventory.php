<?php

namespace App\Models;

use App\Actions\Maps\MakeAmaskAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\MissouriStateMasterKey;
use App\Models\Move;
use App\Models\FolioAuthenticationToken;
use App\Traits\BookShelfTrait;

class LocalInventory extends Model
{
	use HasFactory;
	use BookShelfTrait;

	public static function getKeys($callnumber)
	{
		$pre_sort_key = Callnumber::makeKey($callnumber);

		return explode("*", $pre_sort_key);
	}

	public static function folioResponse($barcode) {
		$institution_id = Auth::user()->institution_id;

		$token = FolioAuthenticationToken::where('institution_id', 1)->where('institution_id', 1)->orderByDesc('created_at')->pluck('auth_key')[0];
		$tenant = FolioAuthenticationToken::where('institution_id', 1)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',1)->pluck('api_url')[0];
		
		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token
			
		])->get("$apiUrl/inventory/items?query=barcode = ($barcode)");

		return $response->json();
		
	}

	public static function buildInventory() {
		
		$institution_id = Auth::user()->institution_id;
		$token = FolioAuthenticationToken::where('institution_id', $institution_id)->where('user_id',Auth::id())->orderByDesc('created_at')->pluck('auth_key')[0];
		$tenant = FolioAuthenticationToken::where('institution_id', $institution_id)->pluck('tenant')[0];
		$apiUrl = ApiUrl::where('institution_id',$institution_id)->pluck('api_url')[0];

		$response = Http::withHeaders([
			'X-Okapi-Tenant' => $tenant,
			'x-okapi-token' => $token

		])->get("$apiUrl/inventory/items?limit=100");

		$response = $response->json();


		foreach($response['items'] as $key=>$r) {
			if(!empty($r['barcode']) && !empty($r['callNumber'])) {

				$callnumber = self::processCallNumber($r['callNumber']);

				$sort_key = self::getKeys($callnumber);

				$prefix = trim($sort_key[0]);
				$tp1 = trim($sort_key[1]);
				$tp2 = trim($sort_key[2]);
				$pre_date = trim($sort_key[3]);
				$pvn = trim($sort_key[4]);
				$pvl = trim($sort_key[5]);
				$cutter = trim($sort_key[6]);
				$pcd = trim($sort_key[7]);
				$cutter_date = trim($sort_key[8]);
				$inline_cutter = trim($sort_key[9]);
				$inline_cutter_decimal = trim($sort_key[10]);
				$cutter_date2 = trim($sort_key[11]);
				$cutter2 = trim($sort_key[12]);
				$pcd2 = trim($sort_key[13]);
				$part1 = trim($sort_key[14]);

				$books[] = [ 

					'user_id' => Auth::id(),
					'barcode' => $r['barcode'],
					'title' => $r['title'],
				        'status' => $r['status']['name'],	
					'callno' => $callnumber, 
					'prefix' => $prefix,
					'tp1' => $tp1,
					'tp2' => $tp2,
					'pre_date' => $pre_date,
					'pvn' => $pvn,
					'pvl' => $pvl,
					'cutter' => $cutter,
					'pcd' => $pcd,
					'cutter_date' => $cutter_date,
					'inline_cutter' => $inline_cutter,
					'inline_cutter_decimal' => $inline_cutter_decimal,
					'cutter_date2' => $cutter_date2,
					'cutter2' => $cutter2,
					'pcd2' => $pcd2,
					'part1' => $part1
				];
			}

		}

		DB::table('missouri_state_master_keys')
			->insert($books);

		$orderedBooks = self::getOrderedData();

		foreach($orderedBooks as $b)  
		{
			$book[] = [ 

				'user_id' => Auth::id(),
				'barcode' => $b->barcode,
				'title' => $b->title, 
				'status' => $b->status, 
				'callno' => $b->callno, 
				'prefix' => $b->prefix,
				'tp1' => $b->tp1,
				'tp2' => $b->tp2,
				'pre_date' => $b->pre_date,
				'pvn' => $b->pvn,
				'pvl' => $b->pvl,
				'cutter' => $b->cutter,
				'pcd' => $b->pcd,
				'cutter_date' => $b->cutter_date,
				'inline_cutter' => $b->inline_cutter,
				'inline_cutter_decimal' => $b->inline_cutter_decimal,
				'cutter_date2' => $b->cutter_date2,
				'cutter2' => $b->cutter2,
				'pcd2' => $b->pcd2,
				'part1' => $b->part1
			];

		}

		MissouriStateMasterKey::truncate();

		DB::table('missouri_state_master_keys')
			->insert($book);

	}


	public static function getOrderedData()
	{ 
		return DB::table('missouri_state_master_keys')
			->select('*') // Select all columns from sort_files table
			->orderBy('prefix')
			->orderBy('tp1')
			->orderBy('tp2')
			->orderBy('pre_date')
			->orderBy('pvn')
			->orderBy('pvl')
			->orderBy('cutter')
			->orderBy('pcd')
			->orderBy('cutter_date')
			->orderBy('inline_cutter')
			->orderBy('inline_cutter_decimal')
			->orderBy('cutter_date2')
			->orderBy('cutter2')
			->orderBy('pcd2')
			->orderBy('part1')
			->get();
	}

	public static function isSingleCutter($smask)
	{
		$singleCutter = strpos($smask,'_DA');

		return $singleCutter;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public static function isDoubleCutter($smask)
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
	public static function processCallNumber($callNumber)
	{
		$callNumberArray = str_split($callNumber);
		$amask = MakeAmaskAction::execute($callNumberArray);
		$smask = implode('',$amask);

		$singleCutter = self::isSingleCutter($smask);

		if ($singleCutter != false) {
			$processedCallNumber = 
				self::removeSingleCutterSpace($callNumber,$singleCutter);	
			return $processedCallNumber;
		}

		$doubleCutter = self::isDoubleCutter($smask);

		if($doubleCutter != false) {
			$processedCallNumber =
				self::removeDoubleCutterSpace($callNumber,$doubleCutter);

			return $processedCallNumber;
		}

		return $callNumber;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public static function removeSingleCutterSpace($callNumber,$singleCutter)
	{
		$callNumber = substr_replace($callNumber,"",$singleCutter,1);

		return $callNumber;
	}


	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public static function removeDoubleCutterSpace($callNumber,$doubleCutter)
	{
		$callNumber = substr_replace($callNumber,"",$doubleCutter,1);

		return $callNumber;

	}


    public static function getUnscannedItems() {
		return DB::table('missouri_state_master_keys')
			->select('*')
			->whereNotIn('barcode',function($query){
				$query->select('barcode')->from('local_inventory_outs')->where('user_id',Auth::id());
			})
			->orderBy('id')
			->take(5)
			->get();
    }

	public static function getMissingItems($firstBookPosition,$lastBookPosition) {
		return DB::table('missouri_state_master_keys')
			->select('*')
			->whereNotIn('barcode',function($query){
				$query->select('barcode')->from('shelves')->where('user_id',Auth::id());

			})
			->where('id','>=',$firstBookPosition)
			->where('id','<=',$lastBookPosition)
			->where('status','=','Available')
			->orderBy('id')
			->get();
	
	}

}
