<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BillingInfo extends Model
{
    use HasFactory;
    protected $table = 'billing_info';
    protected $primaryKey = 'billing_id';
    protected $fillable = [
        'customer_id',
        'payment_method',
        'card_number',
        'card_holder_name',
        'card_expiration_date',
        'card_security_code',
        'billing_address',
    ];
    public function User()
    {
        return $this->belongsTo(User::class,'customer_id');
    }
}
