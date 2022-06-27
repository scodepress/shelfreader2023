<?php

namespace App\Providers;

use App\Contracts\Services\SortKey\SortKeysInterface;
use App\Services\SortKey\Dewey\DeweyStoreSortKeyService;
use App\Services\SortKey\Maps\MapsStoreSortKeyService;
use App\Services\SortKey\LCC\LccStoreSortKeyService;
use Illuminate\Support\ServiceProvider;

class SortKeyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    public function register()
    {
		$this->app->singleton(SortKeysInterface::class, function($app) {

			$request = app(\Illuminate\Http\Request::class);

			if($request->sortSchemeId === 1 ) {

				return new LccStoreSortKeyService();

			} elseif($request->sortSchemeId === 2)

			{
				return new DeweyStoreSortKeyService();
				
			} elseif($request->sortSchemeId === 3)
			{
				return new MapsStoreSortKeyService();
			}




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
