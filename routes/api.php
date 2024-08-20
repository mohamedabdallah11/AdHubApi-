<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\DomainController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::get('/settings',SettingController::class);
Route::get('/cities',CityController::class);
Route::get('/districts/{city_id}',DistrictController::class);
Route::post('/message',MessageController::class);
Route::get('/domains',DomainController::class); 

Route::prefix('ads')->controller(AdController::class)->group(function () {
    // Basic endpoints
    Route::get('/', 'index');
    Route::get('/latest', 'latest');
    Route::get('/domain/{domain_id}', 'domain');
    Route::get('/search', 'search');

    // User-specific ads endpoints without middleware for the token
    Route::post('create', 'create');
    Route::post('update/{adId}', 'update');
    Route::get('delete/{adId}', 'delete');
    Route::get('myads', 'myads');
});
