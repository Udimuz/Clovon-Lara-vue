<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/get-csrf-token', [\App\Http\Controllers\HomeController::class, 'getCSRFToken']);
//Route::get('csrf', function() {
//	return csrf_token();
//});

//Route::get('/admin/dashboard', function () {
//    return view('dashboard');
//});

Route::get('/api/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
Route::post('/api/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
Route::get('/api/users/search', [\App\Http\Controllers\Admin\UserController::class, 'search']);
Route::patch('/api/users/{user}/change-role', [\App\Http\Controllers\Admin\UserController::class, 'changeRole']);
Route::put('/api/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
Route::delete('/api/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
Route::delete('/api/users', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete']);

Route::get('{view}', \App\Http\Controllers\ApplicationController::class)->where('view', '(.*)');
