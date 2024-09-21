<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    protected $table = 'requests';
    protected $primaryKey = 'request_id';
    protected $fillable = [
        'request_id',
        'customer_id',
        'technician_id',
        'service_id',
        'latitude',
        'longitude',
        'photo',
        'description',
        'status',
        'location',
        'requested_at',
        'completed_at'
    ];

    // Một yêu cầu thuộc về một khách hàng
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Một yêu cầu thuộc về một kỹ thuật viên
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // Một yêu cầu liên kết với một dịch vụ
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Một yêu cầu có thể có một đánh giá
    public function review()
    {
        return $this->hasOne(Review::class, 'request_id');
    }

    // Một yêu cầu có thể có một thanh toán
    public function payment()
    {
        return $this->hasOne(Payment::class, 'request_id');
    }
}
