<?php

use Illuminate\Http\Request;

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

/*
 * Auth router
 */
Route::post('login', 'UserController@authenticate');
Route::get('refresh', 'UserController@refresh');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
 * JWT protected
 */
Route::group(['middleware' => ['jwt.verify']], function() {
    /**
     * Protected get
     */
    Route::group(['middleware' => ['role.admin']], function() {
        Route::post('register', 'UserController@register');
    });

    Route::get('user', 'UserController@getAuthenticatedUser');
    /**
     * Protected post methods
     */
});
