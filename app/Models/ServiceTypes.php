<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTypes extends Model
{
    use HasFactory;
    protected $table = 'service_types';
    protected $primaryKey = 'service_types_id';

    protected $fillable = [
        'service_types_name', 
    ];

    // Một loại dịch vụ có thể có nhiều dịch vụ
    public function services()
    {
        return $this->hasMany(Service::class, 'service_types_id');
    }
}
