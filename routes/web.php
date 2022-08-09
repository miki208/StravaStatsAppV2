<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StravaController;

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

Route::get('/register', function () {
    return view('register', [
        'client_id' => config('strava-service.client-id'),
        'redirect_uri' => config('app.url') . config('strava-service.authorization-callback-url')
    ]);
});

Route::get(config('strava-service.authorization-callback-url'), [StravaController::class, 'authorization_grant_callback']);

Route::get('/registration_successful', [StravaController::class, 'registration_successful']);

Route::get('/registration_failed', [StravaController::class, 'registration_failed']);
