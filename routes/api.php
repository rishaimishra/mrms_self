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

Route::group(
    [
        'middleware'=>'auth:api',
        'namespace'=>'API'
    ],
    function () {
        Route::post('assessment-calculate', 'General\CalculatePropertyRateController@Calculate');
        Route::post('get/address-options-by-ward', 'General\PopulateOnWardController@Populate');
        Route::get('get/options', 'General\PopulateAssessmentController@populateField');
        Route::post('save/property', 'General\PropertyController@save');
        Route::get('get/incomplete-property', 'General\PropertyController@getIncompleteProperty');
        Route::post('update/property', 'General\PropertyController@update');
        Route::post('update/user-profile', 'General\AppUserController@editProfile');
    }
);


Route::post('login', 'API\User\AuthController@login');
//Route::post('signup', 'API\User\AuthController@signup');
Route::post('reset/password', 'API\User\AuthController@resetPasswordRequest');
//Route::get('logout', 'API\User\AuthController@logout');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
