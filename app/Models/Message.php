<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_messages_id', 'message'
    ];

    public function user_messages()
    {
        return $this->belongsTo(UserMessage::class, 'user_messages_id', 'id');
    }
}
