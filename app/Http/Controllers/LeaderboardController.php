<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
      //  $Leaderboards = Leaderboard::with('user')->get()->take(10);
        $Leaderboards =User::orderBy('score', 'DESC')->get()->take(10);
        return ["status" => "ok", "leaderboards" => $Leaderboards];

    }
}
