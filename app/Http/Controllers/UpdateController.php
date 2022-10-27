<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UpdateController extends Controller
{
   public function show() 
   {
	   $message = 'This is the Update Page';

		$users = DB::connection('mysql2')
			->table('users')
			->select('*')
			->get();
	   
	   return Inertia::render('Update/Index', [
		   'message' => $message,
	   ]);
   }

   public function getUsersTable()
   {

   }
   public function loadUsersTable()
   {

   }

   public function getAlerts()
   {

   }

   public function loadAlerts()
   {

   }

   public function getCorrections()
   {

   }

   public function loadCorrections()
   {

   }

   public function getInstitutionApiServices()
   {

   }

   public function loadInstitutionApiServices()
   {

   }

   public 
}
