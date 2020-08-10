<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Clear all
// Route::get('/clear-all', function() {
//     Artisan::call('cache:clear');
//     Artisan::call('route:clear');
//     Artisan::call('config:clear');
//     Artisan::call('view:clear');
//     return "Cache is cleared";
// });

$locale = \Request::segment(2);
Route::prefix($locale.'/v1')->group(function () {

    $locale = \Request::segment(2);
    if (in_array($locale, ['en', 'vi'])) {

        App::setLocale($locale);
        // Auth
        Route::middleware('auth:api')->get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('login', 'Api\Auth\UserController@login');
        Route::post('register', 'Api\Auth\UserController@register');
        Route::post('refreshtoken', 'Api\Auth\UserController@refreshToken');

        Route::get('unauthorized', 'Api\Auth\UserController@unauthorized');
        Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
            Route::get('logout', 'Api\Auth\UserController@logout');
            Route::get('details', 'Api\Auth\UserController@details');
        });
        // End Auth

    } else {
        //  abort(400);
    }
});//Add
