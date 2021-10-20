<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizzCategory extends Model
{
    use HasFactory;

    protected $table = 'quizz_categories';
    protected $hidden = ['created_at', 'updated_at'];
}
