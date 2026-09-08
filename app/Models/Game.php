<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
             use HasFactory;

    // allowed
    protected $fillable = [
        'creator_id',
        'hint_id',
        'color',
        'stake_id',
        'status'
    ];
    // user
    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }
    // game challenge
    public function gameChallenge()
    {
        return $this->hasOne(GameChallenger::class,'challenger_id');
    }
    // stakes
    public function stake()
    {
        return $this->belongsTo(Stake::class);
    }
    // hints
    public function hint()
    {
        return $this->belongsTo(Hint::class);
    }
}
