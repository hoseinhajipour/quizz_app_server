<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MessegeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizzController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('login', [AuthController::class, 'authenticate']);
Route::get('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('profile', [ProfileController::class, 'profile']);
Route::middleware('auth:sanctum')->get('profile/update', [ProfileController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->get('profile/myfriends', [ProfileController::class, 'myfriends']);
Route::middleware('auth:sanctum')->get('friends/followunfollow', [ProfileController::class, 'followunfollowfriends']);
Route::middleware('auth:sanctum')->get('user/search', [ProfileController::class, 'searchUser']);

Route::middleware('auth:sanctum')->get('leaderboard', [LeaderboardController::class, 'index']);

Route::middleware('auth:sanctum')->get('messege/inbox', [MessegeController::class, 'inbox']);
Route::middleware('auth:sanctum')->get('messege/history', [MessegeController::class, 'history']);
Route::middleware('auth:sanctum')->get('messege/send', [MessegeController::class, 'send']);

Route::get('game/GetCetgories', [QuizzController::class, 'GetCetgories']);
Route::get('game/GetLevelsByCategory', [QuizzController::class, 'GetLevelsByCategory']);
Route::get('game/GetLevels', [QuizzController::class, 'GetLevels']);

Route::get('packages', [PaymentController::class, 'packages']);
Route::middleware('auth:sanctum')->get('packages/buy', [PaymentController::class, 'buy']);
