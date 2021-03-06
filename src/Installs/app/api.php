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

    }
    /* ================== Languages ================== */
    Route::apiResource('languages','Api\Languages\LanguageApiController', ['only' => ['index', 'show']]);
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::apiResource('languages','Api\Languages\LanguageApiController', ['only' => ['update_', 'store_', 'destroy_']]);
    });

    /* ================== organizations ================== */
    Route::apiResource('organizations','Api\Organizations\OrganizationApiController', ['only' => ['index', 'show']]);
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::apiResource('organizations','Api\Organizations\OrganizationApiController', ['only' => ['update_', 'store_', 'destroy_']]);
    });

    /* ================== Categories ================== */
    Route::apiResource('categories','Api\Categories\CategoryApiController', ['only' => ['index', 'show']]);
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::apiResource('categories','Api\Categories\CategoryApiController', ['only' => ['update_', 'store_', 'destroy_']]);
    });

    /* ================== Properties ================== */
    Route::apiResource('properties','Api\Properties\PropertyApiController', ['only' => ['index', 'show']]);
    Route::group(['middleware' => ['CheckClientCredentials','auth:api']], function() {
        Route::apiResource('properties','Api\Properties\PropertyApiController', ['only' => ['update_', 'store_', 'destroy_']]);
    });

});//Add
