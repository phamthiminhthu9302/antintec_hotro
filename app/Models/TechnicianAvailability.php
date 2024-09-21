<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianAvailability extends Model
{
    use HasFactory;
    protected $table = 'technician_availability';
    protected $primaryKey = 'availability_id';
    protected $fillable = [
        'availability_id ',
        'technician_id',
        'available_from',
        'available_to',
        'day_of_week'
    ];
    // Thông tin khả dụng thuộc về một kỹ thuật viên
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
