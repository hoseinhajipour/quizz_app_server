<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $appends = array('Winstatus', 'AllowPlay');

    public function firstUser()
    {
        return $this->belongsTo(User::class, "first_user_id");
    }

    public function secondUser()
    {
        return $this->belongsTo(User::class, "second_user_id");
    }

    public function getWinstatusAttribute()
    {
        if ($this->status == "equal") {
            return "مساوی";
        } else {
            if ($this->winner_user_id) {
                if ($this->winner_user_id == auth()->user()->id) {
                    return "برد";
                } else {
                    return "باخت";
                }
            } else {
                return "در انتظار";
            }
        }

    }

    public function getAllowPlayAttribute()
    {
        $allow = false;

        if ($this->first_user_id == auth()->user()->id) {
            if (!$this->first_user_true_answer) {
                $allow = true;
            }
        }

        if ($this->second_user_id == auth()->user()->id) {
            if (!$this->second_user_true_answer) {
                $allow = true;
            }
        }

        return $allow;
    }

    public function getquestionsAttribute($questions)
    {
        $questions_array = [];
        foreach (json_decode($questions) as $question) {
            $quiz = Quizz::where("id", $question)->first();
            array_push($questions_array, $quiz);
        }
        return $questions_array;
    }

}

