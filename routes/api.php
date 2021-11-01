<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MessegeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizzController;
use App\Http\Controllers\TournamentController;
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
Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);

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

Route::middleware('auth:sanctum')->post('buypackages', [PaymentController::class, 'buy']);
Route::get('packages', [PaymentController::class, 'packages']);


// Tournament Controller
Route::middleware('auth:sanctum')->get('tournament/findmatch', [TournamentController::class, 'FindMatchPlayer']);
Route::middleware('auth:sanctum')->get('mytournament', [TournamentController::class, 'myTournaments']);
Route::middleware('auth:sanctum')->post('tournament/update', [TournamentController::class, 'updateTournament']);
Route::middleware('auth:sanctum')->get('tournamentinfo/{id}', [TournamentController::class, 'TournamentInfo']);


// notificaton Controller
Route::middleware('auth:sanctum')->get('notification/update', [NotificationController::class, 'GetUpdate']);
Route::middleware('auth:sanctum')->get('notification/info', [NotificationController::class, 'GetInfo']);
Route::middleware('auth:sanctum')->get('notification/changestatus', [NotificationController::class, 'changestatus']);
Route::middleware('auth:sanctum')->post('notification/requestplay', [NotificationController::class, 'RequestPlayWithUser']);
