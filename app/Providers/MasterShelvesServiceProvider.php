<?php

namespace App\Providers;

use App\Contracts\Services\MasterShelf\MasterShelfInterface;
use App\Contracts\Services\MasterShelf\Services\MasterShelfMaps;
use App\Contracts\Services\MasterShelf\Services\MasterShelfServiceLcc;
use Illuminate\Support\ServiceProvider;

class MasterShelvesServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(MasterShelfInterface::class, function () {

			$request = app(\Illuminate\Http\Request::class);

			if($request->sortSchemeId === 2) {

				return new MasterShelfMaps();

			} else {

				return new MasterShelfServiceLcc();
			}

		});
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}
}
