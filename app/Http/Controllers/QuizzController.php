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

    public function GetLevelsByCategory(Request $request)
    {
        $levels = Quizz::where("category", $request->categoryId)->get()->groupby('level');
        return $levels;
    }

    public function GetLevels(Request $request)
    {
        $levels = Quizz::where("category", $request->categoryId)
            ->where("level", $request->level)
            ->get();
        return $levels;
    }
}
