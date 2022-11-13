<?php

namespace App\Http\Controllers;

use App\Contracts\Services\MasterShelf\Services\MasterShelfMaps;
use App\Models\InstitutionApiService;
use App\Models\Library;
use App\Models\User;
use App\Models\MasterShelfMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UpdateController extends Controller
{
	public function show() 
	{
		$message = 'This is the Update Page.';


		return Inertia::render('Update/Index', [
			'message' => $message,
		]);
	}

	public function getUsersTable()
	{
		return DB::connection('mysql2')
			->table('users')
			->select('*')
			->get();
	
	}
	public function getShelfErrorsTable()
	{
		return DB::connection('mysql2')
			->table('shelf_errors')
			->select('*')
			->get();
	
	}
	public function loadUsersTable()
	{
		$oldUsers = $this->getUsersTable();

		foreach($oldUsers as $o)
		{
			if($o->id === 1) { continue; }
			$newUsers[] = [

				'id' => $o->id,
				'role_id' => 2,
				'name' => $o->name,
				'privs' => $o->privs,
				'email' => $o->email,
				'avatar' => 'users/default.png',
				'institution_id' => $o->institution,
				'library_id' => $o->institution,
				'service_id' => 1,
				'scheme_id' => 1,
				'approved' => 0,
				'password' => $o->password,
				'created_at' => $o->created_at,
				'updated_at' => $o->updated_at,
			];
		}

		DB::table('users')->insert($newUsers);
	}

	public function loadCorrectionsTable()
	{
		$shelfErrors = $this->getShelfErrorsTable();

		foreach($shelfErrors as $o)
		{
			$serrors[] = [

				'id' => $o->id,
				'user_id' => $o->user_id,
				'library_id' => 0,
				'barcode' => $o->barcode,
				'created_at' => $o->created_at,
				'updated_at' => $o->updated_at,
			];
		}

		DB::table('corrections')->insert($serrors);
	}
	public function getAlerts()
	{

		return DB::connection('mysql2')
			->table('users')
			->select('*')
			->get();
	}

	public function createMapUsers()
	{
		$u = new User;
		$u->name = 'Aaron';
		$u->privs = 3;
		$u->email = 'awp3@psu.edu';
		$u->avatar = 'users/default.png';
		$u->institution_id = 1;
		$u->library_id = 10;
		$u->service_id = 1;
		$u->scheme_id = 2;
		$u->approved = 1;
		$u->password = '$2y$10$YF/XQeLP4FspxpepOgEuZu.VdZd9oFXvj9gxEpNhvAAHXzESNtWiS';
		$u->save();

		$lid = $u->id;

		// Make entry in IAPI table (LCC and Maps)

				$i = new InstitutionApiService;
				$i->user_id = $lid;
				$i->institution_id = 1;
				$i->library_id = 10;
				$i->api_service_id = 1;
				$i->loaded = 1;
				$i->sort_scheme_id = 2;
				$i->sort_scheme_name = 'Maps';

				$i->save();

				$i = new InstitutionApiService;
				$i->user_id = $lid;
				$i->institution_id = 1;
				$i->library_id = 10;
				$i->api_service_id = 1;
				$i->loaded = 0;
				$i->sort_scheme_id = 1;
				$i->sort_scheme_name = 'LCC';

				$i->save();
 
		// Update master_shelf_maps
		MasterShelfMap::where('library_id',3)->update(['user_id' => $lid,'library_id' =>10]);
		
	
	}

	public function loadAlerts()
	{
		$users = User::get();
		$preports = DB::connection('mysql2')
			->table('preports')
			->select('user_id','barcode','title','callnum','location_id','created_at')
			->get();
		foreach($preports as $p)
		{
			$library_id = User::where('id',$p->user_id)->pluck('library_id')[0];
			$palerts[] = [
				'user_id' => $p->user_id,
				'library_id' => $library_id,			
				'sort_scheme_id' => 1,			
				'barcode' => $p->barcode,
				'call_number' => $p->callnum,
				'title' => $p->title,
				'alert' => $p->location_id,
				'created_at' => $p->created_at,
				];
		}
		DB::table('alerts')->insert($palerts);


		$reports = DB::connection('mysql2')
			->table('reports')
			->select('user_id','barcode','title','callnum','location_id','shelf','created_at')
			->get();

		foreach($reports as $p)
		{
			$library_id = User::where('id',$p->user_id)->get();
			if(!$library_id->first()) {
				$library_id = 5000;
			} else { $library_id = $library_id[0]->library_id; }
			$ralerts[] = [
				'user_id' => $p->user_id,
				'library_id' => $library_id,			
				'sort_scheme_id' => 1,			
				'barcode' => $p->barcode,
				'call_number' => $p->callnum,
				'title' => $p->title,
				'alert' => $p->location_id,
				'created_at' => $p->created_at,
				];

		}
		DB::table('alerts')->insert($ralerts);


		$item_alerts = DB::connection('mysql2')
			->table('item_alerts')
			->select('user_id','barcode','call_number','title','current_location','home_location','created_at')
			->get();

		foreach($item_alerts as $p)
		{
			$library_id = User::where('id',$p->user_id)->pluck('library_id')[0];
			$ialerts[] = [
				'user_id' => $p->user_id,
				'library_id' => $library_id,			
				'sort_scheme_id' => 1,			
				'barcode' => $p->barcode,
				'call_number' => $p->call_number,
				'title' => $p->title,
				'alert' => $p->current_location,
				'created_at' => $p->created_at,
				];

		}
		DB::table('alerts')->insert($ialerts);


			
		$shadows = DB::connection('mysql2')
			->table('shadows')
			->select('user_id','barcode','title','created_at')
			->get();

		foreach($shadows as $p)
		{
			$library_id = User::where('id',$p->user_id)->pluck('library_id')[0];
			$salerts[] = [
				'user_id' => $p->user_id,
				'library_id' => $library_id,			
				'sort_scheme_id' => 1,			
				'barcode' => $p->barcode,
				'alert' => 'SHADOW',
				'created_at' => $p->created_at,
				];

		}
		DB::table('alerts')->insert($salerts);

		
	}

	public function getCorrections()
	{

	}

	public function loadCorrections()
	{

	}

	public function loadLccInstitutionApiServices()
	{

		$users = DB::table('users')
			->select('*')
			->get();

		$libraries = Library::get();

		foreach($users as $o)
		{
			$institution_id = Library::where('id',$o->library_id)->pluck('institution_id')[0];
			$entries[] = [

				'user_id' => $o->id,
				'institution_id' => $institution_id,
				'library_id' => $o->library_id,
				'api_service_id' => 1,
				'created_at' => $o->created_at,
				'updated_at' => $o->updated_at,
				'loaded' => 1,
				'sort_scheme_id' => 1,
				'sort_scheme_name' => 'LCC',
			];
		}

		DB::table('institution_api_services')->insert($entries);
	}

	public function updateLccInstitutionApiServices()
	{

		$users = DB::table('users')
			->select('*')
			->get();

		$libraries = Library::get();

		foreach($users as $o)
		{
			$institution_id = Library::where('id',$o->library_id)->pluck('institution_id')[0];

			User::where('id',$o->id)->update(['institution_id' => $institution_id]);
		}

	}

	public function getEngineeringItems()
	{
	


		$rows = null;
		DB::table('master_keys')
			->where('library_id',9)
			->select('user_id','library_id','title','barcode','callno','prefix','tp1','tp2','pre_date','pvn',
			'pvl','cutter','pcd','cutter_date','inline_cutter','inline_cutter_decimal','cutter_date2','cutter2',
			'pcd2','part1','created_at')
			->orderByDesc('user_id','library_id','title','barcode','callno','prefix','tp1','tp2','pre_date','pvn',
			'pvl','cutter','pcd','cutter_date','inline_cutter','inline_cutter_decimal','cutter_date2','cutter2',
			'pcd2','part1','created_at')
			->chunk(1000, function($master_keys)
			{
		foreach($master_keys as $key=>$o) {	

		$eng[] = [

				'user_id' => 107,
				'library_id' => 7,
				'barcode' => $o->barcode,
				'title' => $o->title,
				'call_number' => $o->callno,
				'date' => substr($o->created_at,0,10),
				'prefix' => $o->prefix,
				'tp1' => $o->tp1,
				'tp2' => $o->tp2,
				'pre_date' => $o->pre_date,
				'pvn' => $o->pvn,
				'pvl' => $o->pvl,
				'cutter' => $o->cutter,
				'pcd' => $o->pcd,
				'cutter_date' => $o->cutter_date,
				'inline_cutter' => $o->inline_cutter,
				'inline_cutter_decimal' => $o->inline_cutter_decimal,
				'cutter_date2' => $o->cutter_date2,
				'cutter2' => $o->cutter2,
				'pcd2' => $o->pcd2,
				'part1' => $o->part1,
				'created_at' => $o->created_at,
		];
			
		}

		DB::table('master_shelf_lcc')->insert($eng);

		});
	}

	public function getEmsItems()
	{
		$u = new User;
		$u->name = 'EMS';
		$u->privs = 3;
		$u->email = 'eal17@psu.edu';
		$u->avatar = 'users/default.png';
		$u->institution_id = 1;
		$u->library_id = 5;
		$u->service_id = 1;
		$u->scheme_id = 1;
		$u->approved = 1;
		$u->password = '$2y$10$2X6IjirOiTWbx3x1wIkz3ezIgfBREwxXMXMCWIDAAj8sqKGoJPW92';
		$u->save();


		$rows = null;
		DB::table('master_keys')
			->select('user_id','library_id','title','barcode','callno','prefix','tp1','tp2','pre_date','pvn',
			'pvl','cutter','pcd','cutter_date','inline_cutter','inline_cutter_decimal','cutter_date2','cutter2',
			'pcd2','part1','created_at')->where('library_id',10)
			->orderByDesc('user_id','library_id','title','barcode','callno','prefix','tp1','tp2','pre_date','pvn',
			'pvl','cutter','pcd','cutter_date','inline_cutter','inline_cutter_decimal','cutter_date2','cutter2',
			'pcd2','part1','created_at')
			->chunk(1000, function($master_keys)
			{
				$lid = User::orderByDesc('id')->pluck('id')[0];
		foreach($master_keys as $o) {	
		$ems[] = [
				'user_id' => $lid,
				'library_id' => 5,
				'barcode' => $o->barcode,
				'title' => $o->title,
				'call_number' => $o->callno,
				'date' => substr($o->created_at,0,10),
				'prefix' => $o->prefix,
				'tp1' => $o->tp1,
				'tp2' => $o->tp2,
				'pre_date' => $o->pre_date,
				'pvn' => $o->pvn,
				'pvl' => $o->pvl,
				'cutter' => $o->cutter,
				'pcd' => $o->pcd,
				'cutter_date' => $o->cutter_date,
				'inline_cutter' => $o->inline_cutter,
				'inline_cutter_decimal' => $o->inline_cutter_decimal,
				'cutter_date2' => $o->cutter_date2,
				'cutter2' => $o->cutter2,
				'pcd2' => $o->pcd2,
				'part1' => $o->part1,
				'created_at' => $o->created_at,
		];
		}

		DB::table('master_shelf_lcc')->insert($ems);
			
			
			});
	}


	public function fillShelvesTable()
	{
		$sorts = DB::connection('mysql2')
			->table('sorts')
			->select('id','user_id','barcode','title','callno','position','cposition','created_at','updated_at')
			->get();

		foreach($sorts as $s)
		{
			$shelf[] = [
			
				'scan_order' => $s->id,
				'user_id' => $s->user_id,
				'callnumber' => $s->callno,
				'barcode' => $s->barcode,
				'title' => $s->title,
				'shelf_position' => $s->position,
				'correct_position' => $s->cposition,
				'status' => 'Available',
				'effective_location_id' => 'Unknown',
				'created_at' => $s->created_at,
				'updated_at' => $s->updated_at,
			];
		}


		DB::table('shelves')->insert($shelf);


	}
}
