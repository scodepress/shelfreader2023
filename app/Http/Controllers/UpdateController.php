<?php

namespace App\Http\Controllers;

use App\Models\InstitutionApiService;
use App\Models\Library;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UpdateController extends Controller
{
	public function show() 
	{
		$message = 'This is the Update Page';


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
	public function loadUsersTable()
	{
		$oldUsers = $this->getUsersTable();

		foreach($oldUsers as $o)
		{
			$newUsers[] = [

				'id' => $o->id,
				'role_id' => 4,
				'name' => $o->name,
				'privs' => $o->privs,
				'email' => $o->email,
				'avatar' => 'users/default.png',
				'institution_id' => $o->institution,
				'library_id' => $o->institution,
				'service_id' => 1,
				'scheme_id' => 1,
				'approved' => 1,
				'password' => $o->password,
				'created_at' => $o->created_at,
				'updated_at' => $o->updated_at,
			];
		}

		DB::table('users')->insert($newUsers);
	}

	public function getAlerts()
	{

		return DB::connection('mysql2')
			->table('users')
			->select('*')
			->get();
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
		$rows = DB::table('master_keys')
			->where('library_id',9)
			->select('*')
			->get();

		foreach($rows as $o) {	
		$eng[] = [

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

		DB::table('master_shelf_lcc')->insert($eng);
	}

	public function getEmsItems()
	{
		$rows = null;
		$rows = DB::table('master_keys')
			->where('library_id',10)
			->select('*')
			->get();

		foreach($rows as $o) {	
		$ems[] = [

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

		DB::table('master_shelf_lcc')->insert($ems);
	}

	public function loadMapsUsers()
	{
		
	}

	public function loadMasterShelfLcc()
	{

	}

	public function loadMasterShelfMaps()
	{

	}

}
