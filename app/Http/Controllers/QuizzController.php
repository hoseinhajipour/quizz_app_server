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

}
