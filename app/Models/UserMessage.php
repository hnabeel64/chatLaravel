<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_messages_id', 'id')->orderBy('created_at', 'ASC');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'id','sender_id');
    }
}
