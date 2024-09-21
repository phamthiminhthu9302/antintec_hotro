<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    protected $fillable = [
        'review_id',
        'request_id',
        'rating',
        'comment'
    ];
    // Một đánh giá thuộc về một yêu cầu
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
