<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    // allowed
    protected $fillable = [
        'percentage',
        'is_active'
    ];
}
