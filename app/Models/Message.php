<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    protected $fillable = [
        'message_id',
        'sender_id',
        'receiver_id',
        'request_id',
        'message',
        'is_seen'
    ];

    // Quan hệ với bảng users (người gửi)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Quan hệ với bảng users (người nhận)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Quan hệ với bảng requests
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
