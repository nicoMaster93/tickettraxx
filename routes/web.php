<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get("storage-link", function(){
    File::link(
        storage_path('app/public'), public_path('storage')
    );	
});

Route::get('/migrate', function() {
    $exitCode = Artisan::call('migrate');
    //$exitCode2 = Artisan::call('db:seed');
    return '<h3>Migraci&oacute;n completada '.$exitCode.' </h3>';
});

Route::get('/cache', function() {
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    return '<h3>Cache eliminado</h3>';
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/', 'App\Http\Controllers\Auth\LoginController@login');
Route::get('/user', 'App\Http\Controllers\Auth\LoginController@showUpdateForm')->name('user.update');

Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

Route::get('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Auth\ConfirmPasswordController@confirm');

Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

Route::group(['prefix' => 'tickets'], function(){
    Route::get('/', 'App\Http\Controllers\TicketsController@index')->name('tickets.index');
    Route::get('/search_list', 'App\Http\Controllers\TicketsController@search_list')->name('tickets.search_list');
    Route::get('/create', 'App\Http\Controllers\TicketsController@createForm')->name('tickets.create');
    Route::post('/create', 'App\Http\Controllers\TicketsController@create');
    Route::get('/to_verify', 'App\Http\Controllers\TicketsController@to_verify')->name('tickets.to_verify');
    
    Route::get('/info/{id}', 'App\Http\Controllers\TicketsController@info')->name('tickets.info');
    Route::get('/recheck/{id}', 'App\Http\Controllers\TicketsController@recheck')->name('tickets.recheck');
    Route::post('/change_state', 'App\Http\Controllers\TicketsController@change_state')->name('tickets.change_state');

    Route::post('/upload', 'App\Http\Controllers\TicketsController@upload')->name('tickets.upload');
    Route::get('/createInvoice', 'App\Http\Controllers\TicketsController@createInvoice')->name('tickets.createInvoice');
    Route::post('/createInvoice', 'App\Http\Controllers\TicketsController@createFormInvoice');

    
});

Route::group(['prefix' => 'contractors'], function(){
    Route::get('/', 'App\Http\Controllers\ContractorsController@index')->name('contractors.index');
    Route::get('/create', 'App\Http\Controllers\ContractorsController@createForm')->name('contractors.create');
    Route::post('/create', 'App\Http\Controllers\ContractorsController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\ContractorsController@delete')->name('contractors.delete');
    Route::get('/activate/{id}', 'App\Http\Controllers\ContractorsController@activate')->name('contractors.activate');
    

    Route::get('/edit/{id}', 'App\Http\Controllers\ContractorsController@editForm')->name('contractors.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\ContractorsController@update');

    
    
});

Route::group(['prefix' => 'drivers'], function(){
    Route::get('/{id}', 'App\Http\Controllers\DriversController@index')->name('drivers.index');
    Route::get('/{id}/create', 'App\Http\Controllers\DriversController@createForm')->name('drivers.create');
    Route::post('/{id}/create', 'App\Http\Controllers\DriversController@create');
    Route::post('/delete/{id}', 'App\Http\Controllers\DriversController@delete')->name('drivers.delete');
    
    Route::get('/edit/{id}', 'App\Http\Controllers\DriversController@editForm')->name('drivers.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\DriversController@update');
});



Route::group(['prefix' => 'vehicles'], function(){
    Route::get('/{id}', 'App\Http\Controllers\VehiclesController@index')->name('vehicles.index');
    Route::get('/{id}/create', 'App\Http\Controllers\VehiclesController@createForm')->name('vehicles.create');
    Route::post('/{id}/create', 'App\Http\Controllers\VehiclesController@create');
    Route::post('/delete/{id}', 'App\Http\Controllers\VehiclesController@delete')->name('vehicles.delete');
    
    Route::get('/edit/{id}', 'App\Http\Controllers\VehiclesController@editForm')->name('vehicles.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\VehiclesController@update');


    Route::get('by_contractor/{id}', 'App\Http\Controllers\VehiclesController@by_contractor');
    Route::post('addDriver', 'App\Http\Controllers\VehiclesController@addDriver')->name('vehicles.addDriver');

});


Route::group(['prefix' => 'settlements'], function(){
    Route::get('/', 'App\Http\Controllers\SettlementsController@index')->name('settlements.index');
    Route::post('/liquidate', 'App\Http\Controllers\SettlementsController@liquidate')->name('settlements.liquidate');
    Route::get('/details/{id}', 'App\Http\Controllers\SettlementsController@details')->name('settlements.details');
    
});


Route::group(['prefix' => 'payments'], function(){
    Route::get('/', 'App\Http\Controllers\PaymentsController@index')->name('payments.index');
    Route::get('/details/{id_contrator}/{id_payment}', 'App\Http\Controllers\PaymentsController@details')->name('payments.details');
    

    Route::get('/pdf/{id_contrator}/{id_payment}', 'App\Http\Controllers\PaymentsController@pdf')->name('payments.pdf');

    Route::get('/pdfMail/{id_contrator}/{id_payment}', 'App\Http\Controllers\PaymentsController@sendPdfMail')->name('payments.pdfMail');
    

    Route::post('/reconciliation', 'App\Http\Controllers\PaymentsController@reconciliation')->name('payments.reconciliation');
    

});

Route::group(['prefix' => 'payments_contractor'], function(){
    Route::get('/', 'App\Http\Controllers\PaymentContractorController@index')->name('payments_contractor.index');
    Route::get('/details/{id_payment}', 'App\Http\Controllers\PaymentContractorController@details')->name('payments_contractor.details');
    Route::get('/pdf/{id_payment}', 'App\Http\Controllers\PaymentContractorController@pdf')->name('payments_contractor.pdf');
});


Route::group(['prefix' => 'location'], function(){
    Route::get('/cities_by_state/{idState?}', 'App\Http\Controllers\LocationController@cities_by_state')->name('location.cities_by_state');
});


Route::group(['prefix' => 'materials'], function(){
    Route::get('/', 'App\Http\Controllers\MaterialsController@index')->name('materials.index');
    Route::get('/create', 'App\Http\Controllers\MaterialsController@createForm')->name('materials.create');
    Route::post('/create', 'App\Http\Controllers\MaterialsController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\MaterialsController@delete')->name('materials.delete');

    Route::get('/edit/{id}', 'App\Http\Controllers\MaterialsController@editForm')->name('materials.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\MaterialsController@update');
});


Route::group(['prefix' => 'deductions'], function(){
    Route::get('/', 'App\Http\Controllers\DeductionsController@index')->name('deductions.index');
    Route::get('/create', 'App\Http\Controllers\DeductionsController@createForm')->name('deductions.create');
    Route::post('/create', 'App\Http\Controllers\DeductionsController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\DeductionsController@delete')->name('deductions.delete');

    Route::get('/edit/{id}', 'App\Http\Controllers\DeductionsController@editForm')->name('deductions.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\DeductionsController@update');

    Route::post('/upload', 'App\Http\Controllers\DeductionsController@upload')->name('deductions.upload');
    Route::get('/details/{id}', 'App\Http\Controllers\DeductionsController@details')->name('deductions.details');
});


Route::group(['prefix' => 'settings'], function(){
    Route::get('/', 'App\Http\Controllers\SettingsController@index')->name('settings');
    Route::post('/', 'App\Http\Controllers\SettingsController@update');    
});

Route::group(['prefix' => 'user_control'], function(){
    Route::get('/', 'App\Http\Controllers\UserControlController@index')->name('user_control');
    Route::post('/', 'App\Http\Controllers\UserControlController@update');
});

Route::group(['prefix' => 'users'], function(){
    Route::get('/', 'App\Http\Controllers\UserControlController@indexUsers')->name("users.index");
    Route::get('/create', 'App\Http\Controllers\UserControlController@createUserForm')->name("users.create");
    Route::post('/create', 'App\Http\Controllers\UserControlController@createUser');
    Route::get('/edit/{id}', 'App\Http\Controllers\UserControlController@editUserForm')->name("users.edit");
    Route::post('/edit/{id}', 'App\Http\Controllers\UserControlController@editUser');
    Route::post('/delete/{id}', 'App\Http\Controllers\UserControlController@delete')->name("users.delete");
});


Route::group(['prefix' => 'tickets_contractor'], function(){
    Route::get('/', 'App\Http\Controllers\TicketsContractorController@index')->name('tickets_contractor.index');
    Route::get('/create', 'App\Http\Controllers\TicketsContractorController@createForm')->name('tickets_contractor.create');
    Route::post('/create', 'App\Http\Controllers\TicketsContractorController@create');
    
    
    Route::get('/info/{id}', 'App\Http\Controllers\TicketsContractorController@info')->name('tickets_contractor.info');
    
    Route::post('/upload', 'App\Http\Controllers\TicketsContractorController@upload')->name('tickets_contractor.upload');

    Route::get('/edit/{id}', 'App\Http\Controllers\TicketsContractorController@editForm')->name('tickets_contractor.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\TicketsContractorController@update');
});


Route::group(['prefix' => 'drivers_contractor'], function(){
    Route::get('/', 'App\Http\Controllers\DriversContractorController@index')->name('drivers_contractor.index');
    Route::get('/create', 'App\Http\Controllers\DriversContractorController@createForm')->name('drivers_contractor.create');
    Route::post('/create', 'App\Http\Controllers\DriversContractorController@create');
    Route::post('/delete/{id}', 'App\Http\Controllers\DriversContractorController@delete')->name('drivers_contractor.delete');
    
    Route::get('/edit/{id}', 'App\Http\Controllers\DriversContractorController@editForm')->name('drivers_contractor.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\DriversContractorController@update');
});



Route::group(['prefix' => 'vehicles_contractor'], function(){
    Route::get('/', 'App\Http\Controllers\VehicleContractorController@index')->name('vehicles_contractor.index');
    Route::get('/create', 'App\Http\Controllers\VehicleContractorController@createForm')->name('vehicles_contractor.create');
    Route::post('/create', 'App\Http\Controllers\VehicleContractorController@create');
    Route::post('/delete/{id}', 'App\Http\Controllers\VehicleContractorController@delete')->name('vehicles_contractor.delete');
    
    Route::get('/edit/{id}', 'App\Http\Controllers\VehicleContractorController@editForm')->name('vehicles_contractor.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\VehicleContractorController@update');
});


Route::group(['prefix' => 'other_payments'], function(){
    Route::get('/', 'App\Http\Controllers\OtherPaymentsController@index')->name('other_payments.index');
    Route::get('/create', 'App\Http\Controllers\OtherPaymentsController@createForm')->name('other_payments.create');
    Route::post('/create', 'App\Http\Controllers\OtherPaymentsController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\OtherPaymentsController@delete')->name('other_payments.delete');

});

Route::group(['prefix' => 'po_codes'], function(){
    Route::get('/', 'App\Http\Controllers\PO_CodesController@index')->name('po_codes.index');
    Route::get('/create', 'App\Http\Controllers\PO_CodesController@createForm')->name('po_codes.create');
    Route::post('/create', 'App\Http\Controllers\PO_CodesController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\PO_CodesController@delete')->name('po_codes.delete');

    Route::get('/edit/{id}', 'App\Http\Controllers\PO_CodesController@editForm')->name('po_codes.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\PO_CodesController@update');

    
    Route::get('/rate/{idPickup}/{idDeliver}', 'App\Http\Controllers\PO_CodesController@getRate')->name('po_codes.rate');
});


Route::group(['prefix' => 'pickup_deliver'], function(){
    Route::get('/', 'App\Http\Controllers\PickupDeliverController@index')->name('pickup_deliver.index');
    Route::get('/create', 'App\Http\Controllers\PickupDeliverController@createForm')->name('pickup_deliver.create');
    Route::post('/create', 'App\Http\Controllers\PickupDeliverController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\PickupDeliverController@delete')->name('pickup_deliver.delete');

    Route::get('/edit/{id}', 'App\Http\Controllers\PickupDeliverController@editForm')->name('pickup_deliver.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\PickupDeliverController@update');
});

Route::group(['prefix' => 'deductions_contractor'], function(){
    Route::get('/', 'App\Http\Controllers\DeductionsContractorController@index')->name('deductions_contractor.index');
    Route::get('/create', 'App\Http\Controllers\DeductionsContractorController@createForm')->name('deductions_contractor.create');
    Route::post('/create', 'App\Http\Controllers\DeductionsContractorController@create');

    Route::post('/delete/{id}', 'App\Http\Controllers\DeductionsContractorController@delete')->name('deductions_contractor.delete');

    Route::get('/edit/{id}', 'App\Http\Controllers\DeductionsContractorController@editForm')->name('deductions_contractor.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\DeductionsContractorController@update');

    Route::post('/upload', 'App\Http\Controllers\DeductionsContractorController@upload')->name('deductions_contractor.upload');
    Route::get('/details/{id}', 'App\Http\Controllers\DeductionsContractorController@details')->name('deductions_contractor.details');
});


Route::group(['prefix' => 'quickbooks'], function(){
    Route::get('/', 'App\Http\Controllers\QuickBooksController@index')->name('quickbooks.index');
    Route::get('/token', 'App\Http\Controllers\QuickBooksController@token')->name('quickbooks.token');

    Route::get('/customers/{idCustomer}', 'App\Http\Controllers\QuickBooksController@getTableCostumers')->name('quickbooks.customers');
    Route::post('/insertInvoice', 'App\Http\Controllers\QuickBooksController@insertInvoice')->name('quickbooks.insertInvoice');
});



Route::group(['prefix' => 'fsc'], function(){
    Route::get('/', 'App\Http\Controllers\FscController@index')->name('fsc.index');
    Route::get('/create', 'App\Http\Controllers\FscController@createForm')->name('fsc.create');
    Route::post('/create', 'App\Http\Controllers\FscController@create');

    Route::get('/edit/{id}', 'App\Http\Controllers\FscController@editForm')->name('fsc.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\FscController@update');

    
    Route::post('/delete/{id}', 'App\Http\Controllers\FscController@delete')->name('fsc.delete');
});

Route::group(['prefix' => 'customer'], function(){
    Route::get('/', 'App\Http\Controllers\CustomerController@index')->name('customer.index');
    Route::get('/quickbooks', 'App\Http\Controllers\CustomerController@update_id_quickbooks')->name('customer.quickbooks');

    Route::get('/create', 'App\Http\Controllers\CustomerController@createForm')->name('customer.create');
    Route::post('/create', 'App\Http\Controllers\CustomerController@create');    

    Route::get('/edit/{id}', 'App\Http\Controllers\CustomerController@editForm')->name('customer.edit');
    Route::post('/edit/{id}', 'App\Http\Controllers\CustomerController@update');
});

