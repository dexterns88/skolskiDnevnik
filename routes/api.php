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
    Route::get('user', 'UserController@getAuthenticatedUser');
//    Route::get('closed', 'DataController@closed');

    /**
     * Protected post methods
     */
    Route::post('register', 'UserController@register');
//    Route::post('product/create', 'ProductController@createProduct');
//    Route::post('product/promote', 'ProductController@promoteProduct');
});
