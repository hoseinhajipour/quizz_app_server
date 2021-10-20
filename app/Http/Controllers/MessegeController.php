<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\MessageInbox;
use App\Models\Messege;
use Illuminate\Http\Request;

class MessegeController extends Controller
{
    public function inbox()
    {
        return MessageInbox::where("user_id", auth()->user()->id)->get();
    }

    public function history(Request $request)
    {
        return Messege::where("from", auth()->user()->id)->where("to", $request->user_id)->get();
    }

    public function send(Request $request)
    {
        $Messege = new Messege();
        $Messege->from = auth()->user()->id;
        $Messege->to = $request->to_user_id;
        $Messege->text = $request->text;
        $Messege->save();
        return ['status' => "ok"];
    }
}
