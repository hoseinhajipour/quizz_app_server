<?php

namespace App\Http\Controllers;

use App\Models\CoinUseType;
use App\Models\Quizz;
use App\Models\QuizzCategory;
use App\Models\User;
use Illuminate\Http\Request;

class QuizzController extends Controller
{
    public function GetCetgories()
    {
        $QuizzCategories = QuizzCategory::all();
        return ["status" => "ok", "categories" => $QuizzCategories];
    }

    public function NewQuizz(Request $request)
    {
        $quizz = new Quizz();
        $quizz->description = $request->description;
        $quizz->answer01 = $request->answer01;
        $quizz->answer02 = $request->answer02;
        $quizz->answer03 = $request->answer03;
        $quizz->answer04 = $request->answer04;
        $quizz->true_answer = $request->true_answer;
        $quizz->category = $request->category;
        $quizz->type = $request->type;
        $quizz->user_id = auth()->user()->id;
        $quizz->status = "pending";

        if ($request->file()) {
            if ($request->extension) {
                $fileName = time() . '_' . $request->extension;
            } else {
                $fileName = time() . '_' . $request->file->getClientOriginalName();
            }
            $filePath = $request->file('file')->storeAs('users/' . auth()->user()->id, $fileName, 'public');
            $fileModel = [
                "download_link" => $filePath,
                "original_name" => $request->original_name
            ];
            $quizz->file = '[' . json_encode($fileModel) . ']';
        }

        $quizz->save();

        return ["status" => "ok"];
    }

    public function ApproveQuizze()
    {
        $Quizz = Quizz::where('id', \request("id"))->first();

        if ($Quizz->status == "pending") {
            $Quizz->status = "approve";
        } else if ($Quizz->status == "reject") {
            $Quizz->status = "approve";
        } else if ($Quizz->status == "approve") {
            $Quizz->status = "reject";
        }

        if ($Quizz->status == "approve") {
            $CoinUseType = CoinUseType::where("id", 12)->first();
            $authUser = User::where("id", $Quizz->user_id)->first();
            $authUser->coin += $CoinUseType->amount;
            $authUser->save();

            $TournamentController = new TournamentController();
            $title = "سوال شما تایید شد";
            $message = "$CoinUseType->amount سکه به شما اضافه شد";
            $TournamentController->sendWebNotification($title,$message, $authUser);
        }

        $Quizz->save();
        return redirect()->back();
    }


}
