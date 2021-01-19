<?php

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
Auth::routes();

Route::get('login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Web\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes...
Route::get('register', [\App\Http\Controllers\Web\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [\App\Http\Controllers\Web\Auth\RegisterController::class, 'register']);

// Password Reset Routes...
Route::get('password/reset', [\App\Http\Controllers\Web\Auth\ForgotPasswordController::class, 'showLinkRequestForm']);
Route::post('password/email', [\App\Http\Controllers\Web\Auth\ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::get('password/reset/{token}', [\App\Http\Controllers\Web\Auth\ResetPasswordController::class, 'showResetForm']);
Route::post('password/reset', [\App\Http\Controllers\Web\Auth\ResetPasswordController::class, 'reset']);

Route::get('/', [\App\Http\Controllers\Web\IndexController::class, 'index'])->name('index');
Route::get('/home', [\App\Http\Controllers\Web\HomeController::class, 'index'])->name('home');

