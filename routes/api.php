<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutAllController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\JobController;
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

Route::post('/register', [ RegisterController::class, 'store' ]);
Route::post('/login', [ LoginController::class, 'store' ]);

Route::middleware('auth:sanctum')->group(function(){
	Route::post('/logout', [ LogoutController::class, 'store' ]);
	Route::get('/user', [ UserController::class, 'show' ]);
	Route::patch('/user', [ UserController::class, 'update' ]);

	Route::apiResources([
		'collections' => CollectionController::class,
		'workers' => WorkerController::class,
		'jobs' => JobController::class
	]);
});
