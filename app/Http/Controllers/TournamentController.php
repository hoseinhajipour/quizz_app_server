<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function FindMatchPlayer()
    {
        //find random user online
        //any user not online sugget robot user
        $second_user_id = $this->RandomExceptUser([auth()->user()->id]);
        $second_user = User::where("id", $second_user_id)->first();

        //select Random Quiz
        $questions = [];
        $quizz = [];
        for ($i = 0; $i < 7; $i++) {
            array_push($questions, $this->RandomExceptList($questions));
            $quiz = Quizz::where("id", $questions[$i])->first();
            array_push($quizz, $quiz);
        }


        $Tournament = new Tournament();
        $Tournament->first_user_id = auth()->user()->id;
        $Tournament->second_user_id = $second_user_id;
        $Tournament->questions = json_encode($questions);
        $Tournament->status = "play";
        $Tournament->save();
        $Tournament['questions'] = $quizz;
        $Tournament['first_user'] = auth()->user();
        $Tournament['second_user'] = $second_user;

        //start play robot

        return ["status" => "ok", "tournament" => $Tournament];
    }

    function RandomExceptList($exceptNr = [])
    {
        $fromNr = 1;
        $exclusiveToNr = Quizz::all()->count() - 1;
        do {
            $n = rand($fromNr, $exclusiveToNr);

        } while (in_array($n, $exceptNr));
        return $n;
    }

    function RandomExceptUser($exceptNr = [])
    {
        $fromNr = 1;
        $exclusiveToNr = User::all()->count() - 1;
        do {
            $n = rand($fromNr, $exclusiveToNr);

        } while (in_array($n, $exceptNr));
        return $n;
    }

    function updateTournament(Request $request)
    {
        $Tournament = Tournament::where("id", $request->tournament_id)->first();

        if ($Tournament->first_user_id == auth()->user()->id) {
            $Tournament->first_user_true_answer = $request->true_answer;
        }
        if ($Tournament->second_user_id == auth()->user()->id) {
            $Tournament->second_user_true_answer = $request->true_answer;
        }

        if (isset($Tournament->first_user_true_answer) && isset($Tournament->second_user_true_answer)) {
            if ($Tournament->first_user_true_answer > $Tournament->second_user_true_answer) {
                $Tournament->winner_user_id = $Tournament->first_user_id;
                $Tournament->status = "complete";
            } else if ($Tournament->first_user_true_answer < $Tournament->second_user_true_answer) {
                $Tournament->winner_user_id = $Tournament->second_user_id;
                $Tournament->status = "complete";
            } else {
                $Tournament->winner_user_id = -1;
                $Tournament->status = "equal";
                //equal
            }
        }
        $Tournament->save();
        return ["status" => "ok", "tournament" => $Tournament];
    }

    function TournamentInfo($id)
    {
        $Tournament = Tournament::where("id", $id)->first();
        $youWin = "wait";
        if ($Tournament->winner_user_id) {
            if ($Tournament->winner_user_id == auth()->user()->id) {
                $youWin = "yes";
            } else {
                $youWin = "no";
            }
        }

        return [
            "status" => "ok",
            "tournament" => $Tournament,
            "youWin" => $youWin
        ];

    }

    function myTournaments()
    {
        $tournaments = Tournament::where("first_user_id", auth()->user()->id)
            ->OrWhere("second_user_id", auth()->user()->id)
            ->get();

        return ["status" => "ok", "tournaments" => $tournaments];
    }



}
