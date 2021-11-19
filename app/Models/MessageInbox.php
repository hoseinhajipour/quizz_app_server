<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageInbox extends Model
{
    use HasFactory;

    protected $table = "message_inboxes";

/*
    public function lastmessage()
    {
        return $this->belongsTo(m::class, "second_user_id");
    }
*/

    public function firstUser()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function secondUser()
    {
        return $this->belongsTo(User::class, "other_user_id");
    }
}
