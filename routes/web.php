<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\AthleteController;
use App\Http\Controllers\Channels\ReliveController;
use App\Http\Controllers\Channels\StravaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login/{driver}', [AthleteController::class, 'login'])->name('athlete.login');
Route::get('/authenticated/{driver}', [AthleteController::class, 'callback'])->name('athlete.callback');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('user', [UserController::class, 'index'])->name('user');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    
    Route::get('setting', [SettingController::class, 'index'])->name('setting');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    
    Route::get('/strava/sync', [StravaController::class, 'sync'])->name('strava.sync');
    
    Route::get('/relive/sync', [ReliveController::class, 'index'])->name('relive.sync');
});

require __DIR__.'/auth.php';
