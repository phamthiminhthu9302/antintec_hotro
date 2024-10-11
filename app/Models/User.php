<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\BillingInfo;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'phone',
        'role',
        'address'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function requestsAsCustomer(): HasMany
    {
        return $this->hasMany(Request::class, 'customer_id');
    }

    // Một kỹ thuật viên có nhiều yêu cầu dịch vụ
    public function requestsAsTechnician(): HasMany
    {
        return $this->hasMany(Request::class, 'technician_id');
    }

    // Một kỹ thuật viên có thể có một hồ sơ kỹ thuật viên
    public function technicianDetail()
    {
        return $this->hasOne(TechnicianDetail::class, 'technician_id');
    }

    // Một kỹ thuật viên có thể có nhiều khoảng thời gian khả dụng
    public function availabilities(): HasMany
    {
        return $this->hasMany(TechnicianAvailability::class, 'technician_id');
    }

    // Một kỹ thuật viên có thể có nhiều vị trí thời gian thực
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'technician_id');
    }

    // Một người dùng có thể nhận nhiều thông báo
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
    public function billingInfo(): HasMany
    {
        return $this->hasMany(BillingInfo::class, 'customer_id');
    }
}
