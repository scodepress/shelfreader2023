<?php

namespace App\Providers;

use App\Services\OnlineInventory\OnlineInventoryService;
use Illuminate\Support\ServiceProvider;

class OnlineInventoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('onlineinventory', function(){
            return new OnlineInventoryService();
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
