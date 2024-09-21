<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    protected $fillable = [
        'notification_id',
        'user_id',
        'message',
        'is_read'
    ];
    // Một thông báo thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
