<?php

use Illuminate\Http\Request;



Route::get('update/assessments', 'APIV2\General\PropertyController@updatePropertyAssessmentDetail');
Route::get('update/pensiondiscount', 'APIV2\General\PropertyController@updatePropertyAssessmentPensionDiscount');
Route::get('update/disabilitydiscount', 'APIV2\General\PropertyController@updatePropertyAssessmentDisabilityDiscount');
Route::get('get/options', 'APIV2\General\PopulateAssessmentController@populateField');
Route::get('get/district', 'APIV2\General\DistrictController@getDistrict');
Route::post('create/inaccessibleproperty', 'APIV2\General\PropertyController@createInAccessibleProperties');
//Route::post('save/property', 'APIV2\General\PropertyController@save');

Route::group(
    [
        'middleware' => 'auth:api',
        'namespace' => 'APIV2'
    ],
    function () {
        Route::post('assessment-calculate', 'General\CalculatePropertyRateController@Calculate');
        Route::post('get/address-options-by-ward', 'General\PopulateOnWardController@Populate');
        Route::get('get/meta', 'General\PopulateAssessmentController@getMeta');
        Route::post('save/property', 'General\PropertyController@save');
        Route::get('get/incomplete-property', 'General\PropertyController@getIncompleteProperty');
        Route::post('update/property', 'General\PropertyController@update');
        Route::post('update/user-profile', 'General\AppUserController@editProfile');
        Route::get('get/my/district', 'General\PropertyController@getMyDistrict');
    }
);

Route::post('save/image', 'APIV2\General\PropertyController@saveImage');

Route::post('login', 'APIV2\User\AuthController@login');
//Route::post('signup', 'API\User\AuthController@signup');
Route::post('reset/password', 'APIV2\User\AuthController@resetPasswordRequest');
//Route::get('logout', 'API\User\AuthController@logout');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('admin/login', 'APIV2\Admin\AuthController@login');
//Route::get('admin/logout', 'APIV2\Admin\AuthController@login');

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin-api', 'namespace' => 'APIV2'], function () {
    Route::post('payment/{id}', 'Admin\PaymentController@store');
    Route::post('landlord/{id}', 'Admin\PaymentController@storeLandLord');
    Route::post('search-property', 'Admin\PaymentController@show');
});


Route::post('landlord/login', 'APIV2\Landlord\AuthController@login');
Route::post('landlord/otp', 'APIV2\Landlord\AuthController@mobileVerification');

Route::group(['prefix' => 'landlord', 'middleware' => 'auth:landlord-api', 'namespace' => 'APIV2'], function () {
    Route::post('payment/{id}', 'Landlord\PaymentController@payWithpaypal');
    Route::post('landlord/{id}', 'Landlord\PaymentController@storeLandLord');
    Route::post('propertyapprove/{id}', 'Landlord\PaymentController@storeProperty');
    Route::post('search-property', 'Landlord\PaymentController@show');
});


Route::post('pldc', 'APIV2\General\PropertyController@pldcCouncilAdjustment');

Route::post('propsanitation', 'APIV2\General\PropertyController@setPropSanitation');

Route::post('updateEnumerator', 'APIV2\General\PropertyController@updateEnumerator');

Route::get('deleteproperties', 'APIV2\General\PropertyController@deleteProperty');


Route::get('getcount', 'APIV2\General\PropertyController@getCount');