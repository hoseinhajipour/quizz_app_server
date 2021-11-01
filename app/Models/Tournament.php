<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    public function firstUser()
    {
        return $this->belongsTo(User::class,"first_user_id");
    }
    public function secondUser()
    {
        return $this->belongsTo(User::class,"second_user_id");
    }
}

