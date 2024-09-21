<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'location_id';
    protected $fillable = [
        'location_id',
        'technician_id',
        'latitude',
        'longitude',
        'updated_at'
    ];
    // Vị trí thuộc về một kỹ thuật viên
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
