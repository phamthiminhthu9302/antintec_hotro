<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianDetail extends Model
{
    use HasFactory;
    protected $table = 'technician_details';
    protected $primaryKey = 'detail_id';
    protected $fillable = [
        'detail_id',
        'technician_id',
        'skills',
        'certifications',
        'work_area'
    ];
    // Thông tin chi tiết kỹ thuật viên thuộc về một kỹ thuật viên
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
