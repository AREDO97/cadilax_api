<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuidanceReply extends Model
{
         use HasFactory;

    // allowed
    protected $fillable = [
        'user_id',
        'guidance_request_id',
        'message'
    ];

    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
