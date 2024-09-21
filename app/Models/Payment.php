<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'payment_id',
        'request_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date'
    ];
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
