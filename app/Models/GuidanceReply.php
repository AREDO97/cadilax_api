<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuidanceReply extends Model
{
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
