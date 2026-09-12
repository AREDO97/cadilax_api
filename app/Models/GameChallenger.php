<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameChallenger extends Model
{
             use HasFactory;

    // allowed fields
    protected $fillable = [
        'game_id',
        'challenger_id',
        'color_guess',
        'result',
        'status'
    ];
    // game challenge
    public function game()
    {
        return $this->belongsTo(Game::class,'challenger_id');
    }
}
