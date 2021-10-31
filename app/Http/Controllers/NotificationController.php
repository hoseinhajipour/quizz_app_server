<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function GetUpdate()
    {
        $notifications = Notification::where("to_user_id", auth()->user()->id)
            ->where("type", "request_play")
            ->whereBetween('created_at', [now()->subSecond(5), now()])->first();
        return ["status" => "ok", "notifications" => $notifications];
    }

    public function RequestPlayWithUser(Request $request)
    {
        $Notification = new Notification();
        $Notification->from_user_id = auth()->user()->id;
        $Notification->to_user_id = $request->to_user_id;
        $Notification->type = "request_play";
        $Notification->save();
        return ["status" => "ok", "notification" => $Notification];

    }
}
