<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $primaryKey = 'service_id';
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price'
    ];

    // Một dịch vụ có thể được sử dụng trong nhiều yêu cầu
    public function requests()
    {
        return $this->hasMany(Request::class, 'service_id');
    }
}
