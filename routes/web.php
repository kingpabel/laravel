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
    Log::info('Logging from docker ' . now()->toDateTimeString());

    $logging = new LoggingClient();
    $logger  = $logging->logger('my-log');
    $logger->entry('My Log message from docker container of GCP cloud run');

    return $_ENV;

    // why error flayer show like this
    // cloud log working or not
    // schedule & queue
});
