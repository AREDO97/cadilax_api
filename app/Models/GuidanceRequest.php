<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuidanceRequest extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'message',
        'category'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
