<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/version_info', function ()
{
	return response()->json([
		'status' => '1',
		'data' => \Auth::user()->hasRole('Admin') ? nl2br(@file_get_contents(base_path('changelog.txt'))) : "",
	]);
})->name('version_info');
Route::get('/admin/dashboard', function ()
{
	return redirect("admin");
});
Route::get('/', function ()
{
	return redirect("admin");
});
Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin'], 'namespace' => 'Admin'], function ()
{
	Route::get('/download/{file}', '\App\ScriptBurn\Http\Controllers\Admin\MainController@download')->name('download');

	Route::get('/', '\App\ScriptBurn\Http\Controllers\Admin\MainController@home')->name('home');
	Route::post('/', '\App\ScriptBurn\Http\Controllers\Admin\MainController@speech')->name('speech');

	Route::any('/_logout', function ()
	{
		return redirect("admin/logout");
	})->name('logout');

	Route::get('password/reset/{token}', '\App\IO\BulkSearch\Http\Controllers\Admin\FailedUrlCrudController@index')->name('password.reset');

});
