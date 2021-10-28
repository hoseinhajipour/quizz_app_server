<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function package()
    {
        return $this->belongsTo('App\Models\Package', 'package_id');
    }
}
