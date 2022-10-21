<?php

namespace App\Providers;
use App\Contracts\Services\Library\LibraryInterface;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\Library\LibraryLccService;
use App\Contracts\Services\Library\LibraryMapsService;

class LibraryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->bind(LibraryInterface::class, function () {

			$request = app(\Illuminate\Http\Request::class);

			if($request->sortSchemeId == 2) {

				return new LibraryMapsService();

			} else {

				return new LibraryLccService();
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
