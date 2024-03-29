<?php

namespace App\Http\Controllers;


use App\Models\Quizz;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kutia\Larafirebase\Facades\Larafirebase;


class TournamentController extends Controller
{
    public function FindMatchPlayer(Request $request)
    {
        $to_user_id = intval($request->to_user_id);
        //find random user online
        //any user not online sugget robot user
        if ($to_user_id != 0) {
            $second_user_id = $request->to_user_id;
        } else {
            $otherUser = User::where("id", "!=", 1)->inRandomOrder()->get()->take(1);
            $second_user_id = $otherUser[0]->id;
            //   $second_user_id = $this->RandomExceptUser([auth()->user()->id]);
        }

        $first_user_id = auth()->user()->id;
        $Tournament = $this->NewTournament($first_user_id, $second_user_id, $request->category_id);

        return ["status" => "ok", "tournament" => $Tournament];
    }


    function RandomExceptUser($exceptNr = [])
    {
        $fromNr = 2;
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
            $ProfileController = new ProfileController();
            if ($Tournament->first_user_true_answer > $Tournament->second_user_true_answer) {
                $Tournament->winner_user_id = $Tournament->first_user_id;
                $Tournament->status = "complete";

                $ProfileController->UpdateUserCoinWallet("6", $Tournament->winner_user_id);

                $title = "شما بردید";
                $message = $Tournament->firstUser->username . " vs " . $Tournament->secondUser->username;
                $this->sendWebNotification($title, $message, $Tournament->firstUser);
                $title = "شما باختید";
                $this->sendWebNotification($title, $message, $Tournament->secondUser);

            } else if ($Tournament->first_user_true_answer < $Tournament->second_user_true_answer) {
                $Tournament->winner_user_id = $Tournament->second_user_id;
                $Tournament->status = "complete";

                $ProfileController->UpdateUserCoinWallet("6", $Tournament->winner_user_id);

                $title = "شما باختید";
                $message = $Tournament->firstUser->username . " vs " . $Tournament->secondUser->username;
                $this->sendWebNotification($title, $message, $Tournament->firstUser);
                $title = "شما بردید";
                $this->sendWebNotification($title, $message, $Tournament->secondUser);
            } else {
                $Tournament->winner_user_id = -1;
                $Tournament->status = "equal";
                //equal
                $ProfileController->UpdateUserCoinWallet("11", $Tournament->first_user_id);
                $ProfileController->UpdateUserCoinWallet("11", $Tournament->second_user_id);

                $title = "مساوی";
                $message = $Tournament->firstUser->username . " vs " . $Tournament->secondUser->username;
                $this->sendWebNotification($title, $message, $Tournament->firstUser);
                $this->sendWebNotification($title, $message, $Tournament->secondUser);
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

    function myTournaments(Request $request)
    {

        if($request->status){
            $tournaments = Tournament::where("first_user_id", auth()->user()->id)
                ->where('status', $request->status)
                ->OrWhere("second_user_id", auth()->user()->id)
                ->whereDate('created_at', Carbon::today())
                ->with("firstUser")
                ->with("secondUser")
                ->get();
        }else{
            $tournaments = Tournament::where("first_user_id", auth()->user()->id)
                ->OrWhere("second_user_id", auth()->user()->id)
                ->with("firstUser")
                ->with("secondUser")
                ->get();
        }


        return ["status" => "ok", "tournaments" => $tournaments];
    }

    function playwithFriend(Request $request)
    {
        $first_user_id = auth()->user()->id;
        $second_user_id = $request->to_user_id;
        $Tournament = $this->NewTournament($first_user_id, $second_user_id);
        //send push notification to second user
        $title = "درخواست بازی از طرف ";
        $message = auth()->user()->username;
        $this->sendWebNotification($title, $message, $Tournament->second_user);

        return [
            "status" => "ok",
            "tournament" => $Tournament];
    }

    function NewTournament($first_user_id, $second_user_id, $category_id)
    {
        $second_user = User::where("id", $second_user_id)->first();
        $first_user = User::where("id", $first_user_id)->first();
        //select Random Quiz
        $questions = Quizz::where("category", $category_id)
            ->where('status','approve')
            ->inRandomOrder()
            ->get()
            ->take(setting('gamesetting.quiz_count_per_tournament'));
        $quizz = $questions->pluck("id");
        $Tournament = new Tournament();
        $Tournament->first_user_id = $first_user_id;
        $Tournament->second_user_id = $second_user_id;
        $Tournament->questions = json_encode($quizz);
        $Tournament->status = "play";
        $Tournament->save();
        $Tournament['first_user'] = $first_user;
        $Tournament['second_user'] = $second_user;

        return $Tournament;
    }

    function test()
    {
        $questions = Quizz::where("category", 3)->inRandomOrder()->get()->take(3);
        $quizz = $questions->pluck("id");
        return ["quizz" => $quizz, "questions" => $questions];

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

    public function sendWebNotification($title, $message, $user)
    {
        if ($user->notification && $user->notification_id) {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $FcmToken = [$user->notification_id];
            $serverKey = setting('firebase.token');
            $data = [
                "registration_ids" => $FcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $message,
                ]
            ];
            $encodedData = json_encode($data);
            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

            // Execute post
            $result = curl_exec($ch);

            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            // FCM response
            return $result;
        }
    }

}
