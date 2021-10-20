<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        return Leaderboard::with('user')->get()->take(10);
    }
}
