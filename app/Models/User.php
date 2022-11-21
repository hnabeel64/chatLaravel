<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_messages()
    {
        return $this->belongsTo(UserMessage::class, 'id','sender_id');
    }
    public function getMessages($id)
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userchat = $user->user_messages::with('messages')
        ->where(['sender_id' => $user->id, 'receiver_id' => $id])
        ->orWhere(['receiver_id' => $user->id, 'sender_id' => $id])
        ->get()->pluck('id');
        $chat = Message::with('user_messages')->whereIn('user_messages_id', $userchat)
        ->orderBy('created_at', 'ASC')
        ->get();
        return $chat;
    }
    public function sendMessages($sender, $receiver, $message)
    {
        $usermessage = UserMessage::where(['sender_id' => $sender, 'receiver_id' => $receiver])->first();
        if(!$usermessage)
        {
            $usermessage = UserMessage::create([
                'sender_id' => $sender,
                'receiver_id' => $receiver
            ]);
            $send = Message::create([
                'user_messages_id' => $usermessage->id,
                'message' => $message
            ]);
            return $send;
        }
        $usermessage->update([
            'updated_at' => now()
        ]);
        $send = Message::create([
            'user_messages_id' => $usermessage->id,
            'message' => $message
        ]);
        return $send;
    }
    public function getChatUsers()
    {
        $chatusers = $this->whereNot('id' , auth()->user()->id)->get();
        return $chatusers;
    }

    public function getRefreshMessage($request)
    {
        if($request->has('message_id') && !empty($request->has('message_id'))){
            $ids = [];
            foreach($request->message_id as $key => $value)
            {
                    foreach($value as $k => $v)
                    {
                        array_push($ids, $v);
                    }
            }
                $usermessage = UserMessage::where([
                    'sender_id' => $request->receiver_id,
                    'receiver_id' => $request->sender_id])
                ->orWhere([
                    'sender_id' => $request->receiver_id,
                    'receiver_id' => $request->sender_id])
                ->first();
                $newmessage = Message::where('user_messages_id', $usermessage->id)
                ->with('user_messages')->whereNotIn('id', $ids)
                ->get();
                return $newmessage;
        }
        else{
            return null;
        }
    }
}
