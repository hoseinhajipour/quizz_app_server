<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use App\Models\QuizzCategory;
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
        $quizz->type = "fouranswer";
        $quizz->user_id = auth()->user()->id;
        $quizz->status = "pending";
        $quizz->save();
    }


}
