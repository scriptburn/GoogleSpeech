<?php

namespace App\Scriptburn\Providers;

use Illuminate\Support\ServiceProvider;

class ScriptBurnServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */

	public function boot()
	{
		\Schema::defaultStringLength(191);

	}
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['router']->middleware('role', '\App\ScriptBurn\Http\Middleware\HasRole');
 		$this->app->singleton('speech', function ($app)
		{
			return new \App\Scriptburn\GoogleApi(

				realpath(__DIR__."/../../../storage") ."/output",
				true,
				realpath(__DIR__."/../../../") . "/".getenv('GOOGLE_APP_CRED')
			);});

		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('IOHelper', '\App\ScriptBurn\Facades\ScriptBurnHelper');

	}

}
