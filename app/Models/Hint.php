<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hint extends Model
{
     use HasFactory;
    // allowed
    protected $fillable = [
        'text',
        'status'
    ];
}
