<?php


use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name("login");
Route::get('/login', [HomeController::class, 'login'])->name("login");
Route::get('/user-search', [UserController::class, 'index'])->name("user_search");
Route::get('/leaderboard', [LeaderboardController::class,'show'])->name("leaderboard");
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('approve-quizze/{id}', [App\Http\Controllers\QuizzController::class, "ApproveQuizze"]);
});

Route::get('/order', [App\Http\Controllers\PaymentController::class, 'verify'])->name('order');
