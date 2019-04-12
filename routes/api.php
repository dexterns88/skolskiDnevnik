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
//Route::group(['middleware' => ['cors']], function() {
//  Route::post('login', 'UserController@authenticate');
//  Route::get('refresh', 'UserController@refresh');
//});

Route::post('login', 'UserController@authenticate');
Route::get('refresh', 'UserController@refresh');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
 * JWT protected
 */
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');

    /**
     * Protected get
     */
    Route::group(['middleware' => ['role.admin']], function() {
        Route::post('register', 'UserController@register');
        Route::get('users', 'UserController@getAllUsers');
        Route::get('users/{role}', 'UserController@getAllUsers');
        Route::get('user/delete/{id}', 'UserController@deleteUser');
        Route::post('user/update', 'UserController@updateUser');
    });

    Route::group(['middleware' => ['role.teacher']], function () {
        Route::get('predaje', 'PredmetController@predaje');
        Route::get('predmet/studenti/{id}', 'PredmetController@studentiPredmeta');
        Route::get('predmet/ocene/{pohadjaId}', 'PredmetController@getOcene');
        Route::post('oceni', 'PredmetController@oceni');
    });

    Route::group(['middleware' => ['role.student']], function() {
      Route::get('ocene/{uid}', 'PredmetController@getPredmeteOcena');
    });
    /**
     * Protected post methods
     */
});
