<?php
use Illuminate\Support\Facades\Artisan;


// Route::get('/create-storage-link', function () {
//     Artisan::call('storage:link');
//     return response()->json(['message' => 'Storage link created successfully']);
// });
Route::get('property/loadgmap', 'PropertyController@loadGMap')->name('properties.property.loadgmap');
Route::get('properties_import', 'PropertyController@read_excel')->name('properties_import');
Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function () {

    Route::get('login', 'LoginController@showForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');
    Route::get('forgot-password', 'ForgotPasswordController@showLinkRequestForm')->name('forgot-password');
    Route::post('forgot-password', 'ForgotPasswordController@sendResetLinkEmail');
    Route::get('reset-password', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::POST('reset-password', 'ResetPasswordController@reset')->name('password.update');
});

Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('boundary', 'BoundaryDelimitationController@createUpload');
    Route::post('boundary/import', 'BoundaryDelimitationController@import')->name('boundary.import');
    Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::get('profile', 'AdminUserController@profilePhoto')->name('profile');
    Route::post('profile', 'AdminUserController@storeProfilePhoto')->name('profile');
    Route::get('digitaladdress', 'DashboardController@autoCompleteDigitaladress')->name('digital');
    Route::get('digitaladdressrep', 'DashboardController@autoCompleteDigitaladressReport')->name('digitalrep');
    Route::get('olddigitaladdress', 'DashboardController@autoCompleteDigitaladressOld')->name('olddigital');
    Route::get('openlocationcode', 'DashboardController@autoCompleteOpenLocationCode')->name('openlocationcode');
    Route::get('town', 'DashboardController@autoCompleteTown')->name('town');
    Route::get('first-name', 'DashboardController@autoCompleteFirstName')->name('first-name');
    Route::get('open-location-code', 'DashboardController@autoOpenLocationCode')->name('open-location-code');
    Route::get('last-name', 'DashboardController@autoCompleteSurname')->name('last-name');
    Route::get('middle-name', 'DashboardController@autoCompleteMiddleName')->name('middle-name');
    Route::get('tenant-first-name', 'DashboardController@autoCompleteTenantFirstName')->name('tenant-first-name');
    Route::get('tenant-last-name', 'DashboardController@autoCompleteTenantSurname')->name('tenant-last-name');
    Route::get('tenant-middle-name', 'DashboardController@autoCompleteTenantMiddleName')->name('tenant-middle-name');
    Route::get('postcode', 'DashboardController@autoCompletePostcode')->name('postcode');
    Route::get('name', 'DashboardController@autoCompleteUserName')->name('name');
    Route::get('compound-name', 'DashboardController@autoCompleteCompoundName')->name('compound.name');
    Route::get('get/ward/options', 'AjaxController@getWardOptions')->name('ward.options');
    Route::get('get/calculation', 'AjaxController@calculateRate')->name('calculation');
    Route::get('get/calculation-new', 'AjaxController@calculateNewRate')->name('calculation-new');

    Route::group(['middleware' => ['role:Super Admin|Admin|manager']], function () {

        //////////////////////////////////////////////////////bilal

        Route::get('cep/forms_resourses', 'FormAndResourseController@index')->name('forms-resourses');
        Route::get('cep/information_tips', 'FormAndResourseController@information_tips_index')->name('information-tips');
        Route::get('cep/newsletter', 'FormAndResourseController@newsletter')->name('newsletter');
        Route::get('cep/newsletter_show', 'FormAndResourseController@newsletter_show')->name('newsletter_show');
        Route::get('cep/newsletter_delete', 'FormAndResourseController@newsletter_delete')->name('newsletter-delete');
        Route::get('cep/garbage_collection_listing', 'FormAndResourseController@garbage_collection_list')->name('garbage-collection-list');
        Route::get('cep/garbage_collection', 'FormAndResourseController@garbage_collection')->name('garbage-collection');
        Route::post('cep/change_garbage_collection', 'FormAndResourseController@change_garbage_collection')->name('change-garbage-collection');
        Route::post('cep/check_garbage_collection', 'FormAndResourseController@check_garbage_collection')->name('check-garbage-collection');
        Route::get('cep/complaint_listing', 'FormAndResourseController@complaint_listing')->name('complaint-listing');
        Route::get('cep/complaint_listing_show', 'FormAndResourseController@complaint_listing_show')->name('complaint-listing-show');
        Route::get('cep/complaint_listing_delete', 'FormAndResourseController@complaint_listing_delete')->name('complaint-listing-delete');
        Route::post('cep/forms_resourses_store', 'FormAndResourseController@form_store')->name('forms-resourses-store');
        Route::post('cep/headline_store', 'FormAndResourseController@headline_store')->name('head-line-store');
        Route::get('cep/form_resources/{id}', 'FormAndResourseController@delete_form_resources')->name('delete-form-resources');
        Route::get('cep/edit_form_resources/{id}', 'FormAndResourseController@edit_form_resources')->name('edit-form-resources');
        Route::post('cep/update_form_resources', 'FormAndResourseController@update_form_resources')->name('update-form-resources');

        ////////////////////////////////////////////////////////////

        Route::get('meta', 'MetaValueController')->name('meta.value');
        Route::get('meta/list/first-name', 'MetaValueController@firstName')->name('meta.value.first-name');
        Route::get('meta/list/street-name', 'MetaValueController@streetName')->name('meta.value.street-name');
        Route::get('meta/list/surname', 'MetaValueController@surname')->name('meta.value.surname');
        Route::get('meta/edit', 'MetaValueController@edit')->name('meta.value.edit');
        Route::post('meta/edit', 'MetaValueController@update')->name('meta.value.update');
        Route::get('meta/delete', 'MetaValueController@delete')->name('meta.value.delete');
        Route::post('meta', 'MetaValueController@store')->name('store.meta.value');
        Route::get('forgot-request', 'DashboardController@forgotRequest')->name('forgot.request');
        Route::get('properties', 'PropertyController@list')->name('properties');
        Route::get('property/details', 'PropertyController@show')->name('properties.show');
        Route::get('property/create', 'PropertyController@create')->name('properties.create');
        Route::get('property/destroy', 'PropertyController@destroy')->name('properties.destroy');
        Route::get('assign/property', 'PropertyController@assignProperty')->name('assign.property');
        Route::post('assign/save', 'PropertyController@saveAssignProperty')->name('properties.assign.save');
        // get material
        Route::post('property/get_data_material', 'PropertyController@get_material')->name('get_material');
        Route::post('property/get_data_material_roof', 'PropertyController@get_material_roof')->name('get_material_roof');
        Route::post('property/get_data_material_window', 'PropertyController@get_material_window')->name('get_material_window');
        Route::post('property/get_data_value_added', 'PropertyController@get_value_added')->name('get_value_added');
        Route::post('property/get_data_property_category', 'PropertyController@get_property_category')->name('get_property_category');
        Route::get('property/meter/delete/{id}', 'PropertyController@deleteMeter')->name('properties.meter.delete');
        //landlord Verification
        Route::get('verify/landlorddetails', 'PropertyController@verifyLandlord')->name('verify.landlord');
        Route::get('verify/landlord/approve/{id}', 'PropertyController@approveLandlord')->name('verify.landlord.approve');
        Route::get('verify/landlord/reject/{id}', 'PropertyController@rejectLandlord')->name('verify.landlord.reject');
        Route::get('verify/landlorddetails/rejected', 'PropertyController@rejectedLandlord')->name('verify.landlord.rejected');
        Route::get('verify/landlorddetails/approved', 'PropertyController@approvedLandlord')->name('verify.landlord.approved');

        //property Verification
        Route::get('verify/propertydetails', 'PropertyController@verifyProperty')->name('verify.property');
        Route::get('verify/property/approve/{id}', 'PropertyController@approveProperty')->name('verify.property.approve');
        Route::get('verify/property/reject/{id}', 'PropertyController@rejectProperty')->name('verify.property.reject');
        Route::get('verify/propertydetails/rejected', 'PropertyController@rejectedProperty')->name('verify.property.rejected');
        Route::get('verify/propertydetails/approved', 'PropertyController@approvedProperty')->name('verify.property.approved');



        Route::post('landlord/save', 'PropertyController@saveLandlord')->name('properties.landlord.save');
        Route::post('property/save', 'PropertyController@saveProperty')->name('properties.property.save');
        Route::post('occupancy/save', 'PropertyController@saveOccupancy')->name('properties.occupancy.save');
        Route::post('assessment/save', 'PropertyController@saveAssessment')->name('properties.assessment.save');
        Route::get('assessment/update', 'PropertyController@updatePropertyAssessmentDetail')->name('properties.assessment.update');
        Route::post('geo-registry/save', 'PropertyController@saveGeoRegistry')->name('properties.geo-registry.save');
        Route::post('landlord/send-sms', 'PropertyController@sensSmsLandlord')->name('properties.landlord.sendsms');


        Route::get('system-user/create', 'AdminUserController@showUserForm')->name('system-user.create');
        Route::get('system-user/list', 'AdminUserController@list')->name('system-user.list');
        Route::get('system-user/show', 'AdminUserController@show')->name('system-user.show');
        Route::post('system-user/update', 'AdminUserController@update')->name('system-user.update');
        Route::get('system-user/delete', 'AdminUserController@destroy')->name('system-user.delete');
        Route::post('system-user/create', 'AdminUserController@store');


        Route::get('app-user/create', 'AppUserController@create')->name('app-user.create');
        Route::get('app-user/list', 'AppUserController@list')->name('app-user.list');
        Route::get('app-user/show', 'AppUserController@show')->name('app-user.show');
        Route::post('app-user/update', 'AppUserController@update')->name('app-user.update');
        Route::get('app-user/delete', 'AppUserController@destroy')->name('app-user.delete');
        Route::post('app-user/create', 'AppUserController@store');
        // Cep user routes
        Route::get('app-cep-user/list', 'AppUserController@cep_user_list')->name('cep-user.list');


        //Guest user routes
        Route::get('guest-user/create', 'GuestUserController@create')->name('guest-user.create');
        Route::get('guest-user/list', 'GuestUserController@list')->name('guest-user.list');
        Route::get('guest-user/show', 'GuestUserController@show')->name('guest-user.show');
        Route::post('guest-user/update', 'GuestUserController@update')->name('guest-user.update');
        Route::get('guest-user/delete', 'GuestUserController@destroy')->name('guest-user.delete');
        Route::post('guest-user/create', 'GuestUserController@store');

        Route::get('report', 'ReportController@index')->name('report');


        Route::post('change/password', 'AppUserController@resetPassword')->name('change.password');






        Route::get('property/category/list', 'AssessmentOptionController@propertyCategories')->name('list.property.category');
        Route::get('property/category', 'AssessmentOptionController@propertyCategoryCreate')->name('create.property.category');
        Route::post('property/category', 'AssessmentOptionController@propertyCategoryStore')->name('store.property.category');
        Route::get('property/category/delete', 'AssessmentOptionController@propertyCategoryDelete')->name('destroy.property.category');

        Route::get('property/dimension/list', 'AssessmentOptionController@propertyDimensions')->name('list.property.dimension');
        Route::get('property/dimension', 'AssessmentOptionController@propertyDimensionCreate')->name('create.property.dimension');
        Route::post('property/dimension', 'AssessmentOptionController@propertyDimensionStore')->name('store.property.dimension');
        Route::get('property/dimension/delete', 'AssessmentOptionController@propertyDimensionDelete')->name('destroy.property.dimension');

        Route::get('property/type/list', 'AssessmentOptionController@propertyTypes')->name('list.property.type');
        Route::get('property/type', 'AssessmentOptionController@propertyTypeCreate')->name('create.property.type');
        Route::post('property/type', 'AssessmentOptionController@propertyTypeStore')->name('store.property.type');
        Route::get('property/type/delete', 'AssessmentOptionController@propertyTypeDelete')->name('destroy.property.type');


        Route::get('property/roof-material/list', 'AssessmentOptionController@propertyRoofMaterials')->name('list.property.roof-material');
        Route::get('property/roof-material', 'AssessmentOptionController@propertyRoofMaterialCreate')->name('create.property.roof-material');
        Route::post('property/roof-material', 'AssessmentOptionController@propertyRoofMaterialStore')->name('store.property.roof-material');
        Route::get('property/roof-material/delete', 'AssessmentOptionController@propertyRoofMaterialDelete')->name('destroy.property.roof-material');


        Route::get('property/wall-material/list', 'AssessmentOptionController@propertyWallMaterials')->name('list.property.wall-material');
        Route::get('property/wall-material', 'AssessmentOptionController@propertyWallMaterialCreate')->name('create.property.wall-material');
        Route::post('property/wall-material', 'AssessmentOptionController@propertyWallMaterialStore')->name('store.property.wall-material');
        Route::get('property/wall-material/delete', 'AssessmentOptionController@propertyWallMaterialDelete')->name('destroy.property.wall-material');

        Route::get('property/zone/list', 'AssessmentOptionController@propertyZones')->name('list.property.zone');
        Route::get('property/zone', 'AssessmentOptionController@propertyZoneCreate')->name('create.property.zone');
        Route::post('property/zone', 'AssessmentOptionController@propertyZoneStore')->name('store.property.zone');
        Route::get('property/zone/delete', 'AssessmentOptionController@propertyZoneDelete')->name('destroy.property.zone');

        Route::get('property/swimming/list', 'AssessmentOptionController@propertySwimmings')->name('list.property.swimming');
        Route::get('property/swimming', 'AssessmentOptionController@propertySwimmingCreate')->name('create.property.swimming');
        Route::post('property/swimming', 'AssessmentOptionController@propertySwimmingStore')->name('store.property.swimming');
        Route::get('property/swimming/delete', 'AssessmentOptionController@propertySwimmingDelete')->name('destroy.property.swimming');

        Route::get('property/sanitation/list', 'AssessmentOptionController@propertySanitation')->name('list.property.sanitation');
        Route::get('property/sanitation', 'AssessmentOptionController@propertySanitationCreate')->name('create.property.sanitation');
        Route::post('property/sanitation', 'AssessmentOptionController@propertySanitationStore')->name('store.property.sanitation');
        Route::get('property/sanitation/delete', 'AssessmentOptionController@propertySanitationDelete')->name('destroy.property.sanitation');

        Route::get('property/window/list', 'AssessmentOptionController@propertyWindow')->name('list.property.window');
        Route::get('property/window', 'AssessmentOptionController@propertyWindowCreate')->name('create.property.window');
        Route::post('property/window', 'AssessmentOptionController@propertyWindowStore')->name('store.property.window');
        Route::get('property/window/delete', 'AssessmentOptionController@propertyWindowDelete')->name('destroy.property.window');


        Route::get('property/inaccessible/list', 'AssessmentOptionController@propertyInaccessible')->name('list.property.inaccessible');
        Route::get('property/inaccessible', 'AssessmentOptionController@propertyInaccessibleCreate')->name('create.property.inaccessible');
        Route::post('property/inaccessible', 'AssessmentOptionController@propertyInaccessibleStore')->name('store.property.inaccessible');
        Route::get('property/inaccessible/delete', 'AssessmentOptionController@propertyInaccessibleDelete')->name('destroy.property.inaccessible');

        Route::get('property/use/list', 'AssessmentOptionController@propertyUse')->name('list.property.use');
        Route::get('property/use', 'AssessmentOptionController@propertyUseCreate')->name('create.property.use');
        Route::post('property/use', 'AssessmentOptionController@propertyUseStore')->name('store.property.use');
        Route::get('property/use/delete', 'AssessmentOptionController@propertyUseDelete')->name('destroy.property.use');

        Route::get('property/value-added/list', 'AssessmentOptionController@propertyValueAdded')->name('list.property.value-added');
        Route::get('property/value-added', 'AssessmentOptionController@propertyValueAddedCreate')->name('create.property.value-added');
        Route::post('property/value-added', 'AssessmentOptionController@propertyValueAddedStore')->name('store.property.value-added');
        Route::get('property/value-added/delete', 'AssessmentOptionController@propertyValueAddedDelete')->name('destroy.property.value-added');


        Route::get('payment/edit/{id}', 'PaymentController@edit')->name('payment.edit');
        Route::get('payment_reverse/edit/{id}', 'PaymentController@reverse_edit')->name('payment_reverse.edit');
        Route::get('payment/verify/{id}', 'PaymentController@verify')->name('payment.verify');
        Route::post('payment/edit/{id}', 'PaymentController@update')->name('payment.update');
        Route::get('payment/delete/{id}', 'PaymentController@delete')->name('payment.delete');

        Route::get('disability/approve/{id}', 'PropertyController@updatePropertyAssessmentDisabilityDiscount')->name('disability.approve');
        Route::get('disability/reject/{id}', 'PropertyController@rejectPropertyAssessmentDisabilityDiscount')->name('disability.reject');
        Route::get('pensioner/approve/{id}', 'PropertyController@updatePropertyAssessmentPensionDiscount')->name('pensioner.approve');
        Route::get('pensioner/reject/{id}', 'PropertyController@rejectPropertyAssessmentPensionDiscount')->name('pensioner.reject');



    });

    Route::group(['middleware' => ['role:Super Admin|Admin|manager|cashiers|supervisor']], function () {
        Route::post('payment/{id}', 'PaymentController@store')->name('payment.store');
        Route::get('payments', 'PaymentController@show')->name('payment');
        Route::get('reverse_payments', 'PaymentController@show_reverse')->name('reverse_payment');
        Route::get('reverse/{id}', 'PaymentController@reverse')->name('reverse');
        Route::get('/text-payer', 'TaxPayerController@index')->name('tax-payer');
        Route::group(['prefix' => 'account', 'as' => 'account.', 'namespace' => 'Account'], function () {
            Route::get('reset-password', 'ResetPasswordController')->name('reset-password');
            Route::post('reset-password', 'ResetPasswordController@update')->name('update-password');
        });
    });


    Route::group(['middleware' => ['role:Super Admin|Admin|manager|cashiers|supervisor']], function () {
        Route::get('payment/pos/receipt/{id}/{payment_id}', 'PaymentController@getPosReceipt')->name('payment.pos.receipt');
    });

    Route::group(['middleware' => ['role:Super Admin|Admin|supervisor|manager']], function () {
        Route::get('report', 'ReportController@index')->name('report');
        Route::get('properties', 'PropertyController@list')->name('properties');
       
        Route::get('inaccessibleproperties', 'PropertyController@listInaccessibleProperties')->name('inaccessibleproperties');
        Route::get('unfinishedproperties', 'PropertyController@listUnfinishedProperties')->name('unfinishedproperties');
        Route::get('property/details', 'PropertyController@show')->name('properties.show');
        Route::get('payment/receipt/{id}/{year?}', 'PaymentController@getReceipt')->name('payment.receipt');
        Route::get('email/payment/receipt/{id}/{year?}', 'PaymentController@emailReceipt')->name('email.payment.receipt');
        Route::get('stickers/{id}/{year?}', 'PaymentController@getStickers')->name('stickers');
        Route::get('properties/download/pdf', 'PropertyController@downloadPdf')->name('download.pdf');
        Route::get('properties/download/envelope/{id}/{year?}', 'PropertyController@downloadEnvelope')->name('download.envelope');

        Route::group(['as' => 'audit.', 'namespace' => 'Audit'], function () {
            Route::get('user', 'LoginLog@userLoginAudit')->name('user');
            Route::get('admin', 'LoginLog@adminLoginAudit')->name('admin');
            Route::get('property', 'PropertyLog@index')->name('property');
            Route::get('properti-assessment', 'PropertyLog@assessment')->name('property.assessment');
            Route::get('property-payment', 'PropertyLog@payment')->name('property.payment');
            Route::get('properti-landlord', 'PropertyLog@landlord')->name('property.landlord');
            Route::get('properti-occupancy', 'PropertyLog@occupancy')->name('property.occupancy');
            Route::get('properti-occupancy-detail', 'PropertyLog@occupancyDetail')->name('property.occupancyDetail');
            Route::get('properti-geoRegistry', 'PropertyLog@geoRegistry')->name('property.geoRegistry');
            Route::get('properti-registry-meter', 'PropertyLog@registryMeter')->name('property.registryMeter');
            Route::get('property_assigned_officers', 'PropertyLog@property_assigned_officers')->name('property.property_assigned_officers');


            Route::get('assessment/property-categories', 'AssessmentOptionsLog@propertyCategories')->name('assessment.property.categories');
            Route::get('assessment/property-types', 'AssessmentOptionsLog@propertyTypes')->name('assessment.property.types');
            Route::get('assessment/wall-material', 'AssessmentOptionsLog@wallMaterial')->name('assessment.wall.material');
            Route::get('assessment/roof-material', 'AssessmentOptionsLog@roofMaterial')->name('assessment.roof.material');
            Route::get('assessment/property-dimensions', 'AssessmentOptionsLog@propertyDimensions')->name('assessment.property.dimensions');
            Route::get('assessment/value-added', 'AssessmentOptionsLog@valueAdded')->name('assessment.value.added');
            Route::get('assessment/property-use', 'AssessmentOptionsLog@propertyUse')->name('assessment.property.use');
            Route::get('assessment/property-zones', 'AssessmentOptionsLog@zones')->name('assessment.property.zones');
            Route::get('assessment/swimming-pool', 'AssessmentOptionsLog@swimmingPool')->name('assessment.property.swimmingpool');
            Route::get('assessment/property-inaccessible', 'AssessmentOptionsLog@propertyInaccessible')->name('assessment.property.inaccessible');
        });
    });

    Route::group(['prefix' => 'system/config', 'namespace' => 'Config', 'as' => 'config.'], function () {
        Route::get('setting', 'CommunityController')->name("community");
        Route::post('setting', 'CommunityController@save');
    });
    Route::resource('notification', 'NotificationController');
    Route::resource('districts', 'DistrictController');
    Route::get('district/delete', 'DistrictController@destroy')->name('district.delete');
    Route::resource('envelopes', 'EnvelopeController');
    Route::resource('adjustments', 'AdjustmentController');
    Route::resource('adjustmentValues', 'AdjustmentValueController');
    Route::resource('millrates', 'MillRateController');
    Route::resource('propertyCharacteristics', 'PropertyCharacteristicController');
    Route::resource('propertyCharacteristicValues', 'PropertyCharacteristicValueController');


});
Route::get('change_entries', 'PropertyController@update_entries')->name('update_entries');
Route::get('delete_entries', 'PropertyController@delete_selected_prop')->name('update_entries.del');
