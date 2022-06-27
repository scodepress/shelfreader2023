<?php

namespace App\Services\MasterShelf;

use App\Services\MasterShelf\MasterShelfService;
use Illuminate\Support\ServiceProvider;

class MasterShelfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->bind('mastershelf', function(){

		    return new MasterShelfService();
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
