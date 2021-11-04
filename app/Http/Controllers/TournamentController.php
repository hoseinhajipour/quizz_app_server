<?php

namespace App\Http\Controllers;


use App\Models\Quizz;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;
use Kutia\Larafirebase\Facades\Larafirebase;


class TournamentController extends Controller
{
    public function FindMatchPlayer()
    {
        //find random user online
        //any user not online sugget robot user
        $second_user_id = $this->RandomExceptUser([auth()->user()->id]);
        $second_user = User::where("id", $second_user_id)->first();
        $first_user_id = auth()->user()->id;
        $Tournament = $this->NewTournament($first_user_id, $second_user_id);

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
        $Tournament = Tournament::where("id", $request->tournament_id)
            ->with("firstUser")
            ->with("secondUser")
            ->first();

        if ($Tournament->first_user_id == auth()->user()->id) {
            $Tournament->first_user_true_answer = $request->true_answer;
            $this->UpdateScore($request->true_answer);
        }
        if ($Tournament->second_user_id == auth()->user()->id) {
            $Tournament->second_user_true_answer = $request->true_answer;
            $this->UpdateScore($request->true_answer);
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

    function UpdateScore($true_answer)
    {
        $scoreReward = intval($true_answer) * intval(setting('gamesetting.socre_pre_true_answer'));
        $authUser = auth()->user();
        $authUser->score += $scoreReward;
        $authUser->save();
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
            ->with("firstUser")
            ->with("secondUser")
            ->get();

        return ["status" => "ok", "tournaments" => $tournaments];
    }

    function playwithFriend(Request $request)
    {
        $first_user_id = auth()->user()->id;
        $second_user_id = $request->to_user_id;
        $Tournament = $this->NewTournament($first_user_id, $second_user_id);
        //send push notification to second user
        $token = $Tournament->second_user->notification_id;
        if($token){
            $title = "درخواست بازی از طرف ";
            $message = auth()->user()->username;
            Larafirebase::withTitle($title)
                ->withBody($message)
                ->withPriority('high')
                ->sendMessage($token);
        }

        return [
            "status" => "ok",
            "tournament" => $Tournament];
    }

    function NewTournament($first_user_id, $second_user_id)
    {
        $second_user = User::where("id", $second_user_id)->first();
        $first_user = User::where("id", $first_user_id)->first();
        //select Random Quiz
        $questions = [];
        $quizz = [];
        for ($i = 0; $i < setting('gamesetting.quiz_count_per_tournament'); $i++) {
            array_push($questions, $this->RandomExceptList($questions));
            $quiz = Quizz::where("id", $questions[$i])->first();
            array_push($quizz, $quiz);
        }

        $Tournament = new Tournament();
        $Tournament->first_user_id = $first_user_id;
        $Tournament->second_user_id = $second_user_id;
        $Tournament->questions = json_encode($questions);
        $Tournament->status = "play";
        $Tournament->save();
        $Tournament['first_user'] = $first_user;
        $Tournament['second_user'] = $second_user;

        return $Tournament;
    }

    function test()
    {

        $title = "new request";
        $message = "test";
        $token = "fvuUeOlMQDS2IP4UDIcaC5:APA91bHiWB2amk2SB3YxAxsUB2n1SOh0tzcAQJLFCXyXHSYp18bFBkcl_LIYzppZdJJlu_DQV_M8BH0GkE0ol0DNBpJpH7XrUwZg2fTGm4_ErD5v-D5zuycjPNcwR1iZodYvXJ-mb_z6";

        return Larafirebase::withTitle('Test Title')
            ->withBody('Test body')
            ->sendMessage($token);
    }

}
