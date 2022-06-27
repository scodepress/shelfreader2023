<?php

namespace App\Services\RequestHandling;

use App\Services\RequestHandling\FolioRequests;
use Illuminate\Support\ServiceProvider;

class FolioRequestsHandlingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->bind('requestshandling', function(){

		    return new FolioRequests();
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
