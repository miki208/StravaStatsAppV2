<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StravaController;
use App\Http\Controllers\RegistrationController;

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

Route::get('/', function () {
    return redirect('register');
});

Route::get(config('strava-service.authorization-callback-url'), [StravaController::class, 'authorization_grant_callback']);

Route::get('/register', [RegistrationController::class, 'register_user']);
Route::get('/registration_successful', [RegistrationController::class, 'registration_successful']);
Route::get('/registration_failed', [RegistrationController::class, 'registration_failed']);
