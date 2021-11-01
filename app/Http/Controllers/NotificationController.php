<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Quizz;
use App\Models\Tournament;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function GetUpdate()
    {
        $notification = Notification::where("to_user_id", auth()->user()->id)
            ->where("type", "request_play")
            ->whereBetween('created_at', [now()->subSecond(5), now()])->first();
        return ["status" => "ok", "notification" => $notification];
    }

    public function RequestPlayWithUser(Request $request)
    {
        $Notification = new Notification();
        $Notification->from_user_id = auth()->user()->id;
        $Notification->to_user_id = $request->to_user_id;
        $Notification->type = "request_play";
        $Notification->status = "wait";
        $Notification->save();
        return ["status" => "ok", "notification" => $Notification];
    }

    public function GetInfo(Request $request)
    {
        $notification = Notification::where("id", $request->id)->first();
        $Tournament = null;
        if ($notification->status == "yes") {
            $Tournament = Tournament::where("id", $notification->tournament_id)->first();
            $questions = [];
            foreach (json_decode($Tournament->questions) as $question) {
                $quiz = Quizz::where("id", $question)->first();
                array_push($questions, $quiz);
            }
            $Tournament['questions'] = $questions;
        }
        return [
            "status" => "ok",
            "notification" => $notification,
            "tournament" => $Tournament
        ];
    }

    public function changestatus(Request $request)
    {
        $notification = Notification::where("id", $request->id)->first();
        $notification->status = $request->status;

        $Tournament = null;
        if ($notification->status == "yes") {
            //new NewTournament
            $TournamentController = new  TournamentController();
            $Tournament = $TournamentController->NewTournament($notification->from_user_id, $notification->to_user_id);
            $notification->tournament_id = $Tournament->id;
        }
        $notification->save();
        return [
            "status" => "ok",
            "notification" => $notification,
            "tournament" => $Tournament];
    }

}
