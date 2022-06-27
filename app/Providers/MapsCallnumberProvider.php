<?php

namespace App\Providers;

use App\Facades\Services\MapsCallnumberService;
use Illuminate\Support\ServiceProvider;

class MapsCallnumberProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

	    $this->app->bind('mapscallnumber', function(){

		   return new MapsCallnumberService;
	    });
		    
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
