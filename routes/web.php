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
    $logging = new LoggingClient([
        'projectId' => env('PROJECT_ID')
    ]);

    /* $logger = $logging->psrLogger('app');
    $logger->info('log from stack driver'); */

    // The name of the log to write to
    $logName = 'my-log';

    // Selects the log to write to
    $logger = $logging->logger($logName);

    // The data to log
    $text = 'Hello, world!';

    // Creates the log entry
    $entry = $logger->entry($text);

    // Writes the log entry
    $logger->write($entry);

    return view('welcome');
});
