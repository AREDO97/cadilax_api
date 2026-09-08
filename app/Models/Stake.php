<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stake extends Model
{
    // allowed
         use HasFactory;

    protected $fillable = [
        'amount',
        'status',
    ];
    // game
    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
