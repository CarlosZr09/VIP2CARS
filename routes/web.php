<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('vehicles', [VehicleController::class,'index'])->name('vehicles.index');
Route::post('vehicle/create', [VehicleController::class,'store'])->name('vehicles.create');
Route::put('vehicle/update/{id?}', [VehicleController::class,'update'])->name('vehicles.update');
Route::get('vehicle/data/{id?}', [VehicleController::class,'show'])->name('customers.show');
Route::delete('vehicle/destroy/{id?}', [VehicleController::class,'destroy'])->name('vehicles.destroy');
Route::get('vehicles/json',[VehicleController::class,'json'])->name('vehicles.json');



Route::get('customers', [CustomerController::class,'index'])->name('customers.index');
Route::get('customer/search/', [CustomerController::class, 'search']);

Route::get('customer/data/{id?}', [CustomerController::class,'show'])->name('customers.show');
Route::post('customer/create', [CustomerController::class,'store'])->name('customers.create');
Route::put('customer/update/{id?}', [CustomerController::class,'update'])->name('customers.update');
Route::delete('customer/destroy/{id?}', [CustomerController::class,'destroy'])->name('customers.destroy');

Route::get('customers/json', [CustomerController::class,'json'])->name('customers.json');
