<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructionController extends Controller
{
	public function show(){

		$title = "Instructions";

		return Inertia::render('Instructions/Index', [
			'title' => $title,
		]);
	
	}
}
