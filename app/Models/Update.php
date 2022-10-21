<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Update extends Model
{
	use HasFactory;

	public function fillUserTable()
	{
		$users = DB::connection('mysql2')
			->table('users')
			->select('*')
			->get();

		// Create insert array
		foreach($users as $u)
		{
			$new_users[] = [

			];
		}
	}
}
