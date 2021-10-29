<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Quizz extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    protected $table = 'quizzes';

    public function getFileAttribute($file)
    {
        if (json_decode($file)) {
            $download_link = (json_decode($file))[0]->download_link;
            return URL::asset("storage/" . $download_link);
        }else{
            return $file;
        }

    }
}
