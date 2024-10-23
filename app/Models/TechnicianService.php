<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianService extends Model
{
    use HasFactory;

    protected $table = 'technician_service';

    protected $fillable = ['technician_id', 'service_id', 'status', 'available_from', 'available_to'];

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
