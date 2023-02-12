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

Route::group(['prefix' => 'contractor', 'middleware' => ['cors']],function(){
    Route::post("login", "App\Http\Controllers\Api\ContractorController@login");
    
    Route::middleware('auth:api')->get("/info", "App\Http\Controllers\Api\ContractorController@info");
    Route::middleware('auth:api')->put("/update", "App\Http\Controllers\Api\ContractorController@update");
    Route::middleware('auth:api')->post("/logout", "App\Http\Controllers\Api\ContractorController@logout");
});

Route::group(['prefix' => 'tickets_contractor', "middleware" => ['cors','auth:api']],function(){
    Route::get("/", "App\Http\Controllers\Api\TicketsContractorController@show");
    Route::get("/{id}", "App\Http\Controllers\Api\TicketsContractorController@detail");
    Route::post("/create", "App\Http\Controllers\Api\TicketsContractorController@create");
    Route::put("/update/{id}", "App\Http\Controllers\Api\TicketsContractorController@update");
});

Route::group(['prefix' => 'payments'],function(){
    Route::middleware('auth:api')->get("/", "App\Http\Controllers\Api\PaymentsContractorController@index");
    Route::get("/pdf/{id_payment}", "App\Http\Controllers\Api\PaymentsContractorController@pdf");
});


Route::group(['prefix' => 'deductions_contractor', "middleware" => ['cors','auth:api']],function(){
    Route::get("/", "App\Http\Controllers\Api\DeductionsContractorController@show");
    Route::get("/deduction_types", "App\Http\Controllers\Api\DeductionsContractorController@deduction_types");
    
});

Route::get("materials", "App\Http\Controllers\Api\TicketsContractorController@materials");
Route::get("pickup_deliver/{type}", "App\Http\Controllers\Api\TicketsContractorController@pickup_deliver");
Route::middleware('auth:api')->get("/vehicles_by_contractor", "App\Http\Controllers\Api\TicketsContractorController@vehicles_by_contractor");
Route::get("/rate_by_pickup_deliver/{pickup}/{deliver}", "App\Http\Controllers\Api\TicketsContractorController@rate_by_pickup_deliver");
Route::get("/fsc_by_deliver_date/{deliver}/{date}", "App\Http\Controllers\Api\TicketsContractorController@fsc_by_deliver_date");

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});