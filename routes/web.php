<?php

use Illuminate\Support\Facades\Artisan;
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

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('approve-quizze/{id}', [App\Http\Controllers\QuizzController::class, "ApproveQuizze"]);
});

Route::get('/order', [App\Http\Controllers\PaymentController::class, 'verify'])->name('order');
