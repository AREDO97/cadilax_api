<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameInvitation extends Model
{
    /*
    game_id
invited_by
invited_user_id
status* */
    protected $fillable = [
        'invitedBy_id',
        'invitedUser_id',
        'status',
        'game_id'
    ];
    // invited user
    public function invitedUser()
    {
        return $this->belongsTo(User::class,'invitedUser_id');
    }
    // invited by 
    public function invitingUser()
    {
        return $this->belongsTo(User::class,'invitedBy_id');
    }
    // game 
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
