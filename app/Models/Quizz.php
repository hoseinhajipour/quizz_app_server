<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    protected $table = 'quizzes';
}
