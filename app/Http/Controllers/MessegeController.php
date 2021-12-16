<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\MessageInbox;
use App\Models\Messege;
use App\Models\User;
use Illuminate\Http\Request;

class MessegeController extends Controller
{
    public function inbox()
    {
        $inbox = MessageInbox::where("user_id", auth()->user()->id)
            //->with("lastmessage")
            ->with("firstUser")
            ->with("secondUser")
            ->distinct()
            ->latest()
            ->get();
        return ['status' => "ok", "inbox" => $inbox];
    }

    public function history(Request $request)
    {
        $messages = Messege::where("from", auth()->user()->id)
            ->OrWhere("to", $request->user_id)
            ->latest()
            ->get();
        $otherUser = User::where("id", $request->user_id)->first();
        return [
            'status' => "ok",
            "messages" => $messages,
            "userinfo" => $otherUser
        ];
    }

    public function send(Request $request)
    {

        $Messege = new Messege();
        $Messege->from = auth()->user()->id;
        $Messege->to = $request->to_user_id;
        $Messege->text = $request->text;
        $Messege->save();

        $checkIsRelative = MessageInbox::where("user_id", auth()->user()->id)
            ->where("other_user_id", $request->user_id)->first();

        if (!$checkIsRelative) {
            $newMessageInbox = new MessageInbox();
            $newMessageInbox->user_id = auth()->user()->id;
            $newMessageInbox->other_user_id = $request->to_user_id;
            $newMessageInbox->save();
        }

        return ['status' => "ok"];

    }
}
