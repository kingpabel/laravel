<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Google\Cloud\Logging\LoggingClient;

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
    Log::info('hello test log');

    $logging = new LoggingClient();
    $logger  = $logging->psrLogger('hello-app-name');
    $logger->info('log from stack driver');

    // throw new Exception('Error Processing Request');
    return view('welcome');
});
