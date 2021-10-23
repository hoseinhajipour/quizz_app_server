<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;

class QuizzCategory extends Model
{
    use HasFactory;

    protected $table = 'quizz_categories';
    protected $hidden = ['created_at', 'updated_at'];

    public function getIconAttribute($icon)
    {
        return Voyager::image( $icon ) ;
    }
}
