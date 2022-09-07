<?php

use App\Jobs\RunLogging;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
    Log::channel('queue')->info('hello');
    // RunLogging::dispatch();
    return now()->toDayDateTimeString();

    return view('welcome');

    // tinker, failed queue
});

Route::get('schedule', function () {
    return Artisan::call('schedule:run');
});
