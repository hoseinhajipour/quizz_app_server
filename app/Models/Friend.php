<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    function user_info()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

}
