<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformRevenue extends Model
{
    /*
     user_id
    game_id
percentage
amount
type* */
protected $fillable = [
    'user_id',
    'game_id',
    'percentage',
    'amount',
    'type'
];

// reusable revenue collection function
public static function Collect($userId,$gameId,$percentage,$amount,$type)
{
    $gameRevenue=self::create([
    'user_id'=>$userId,
    'game_id'=>$gameId,
    'percentage'=>$percentage,
    'amount'=>$amount,
    'type'=>$type
    ]);
    return $gameRevenue;
}
// games
public function games()
{
    return $this->hasMany(Game::class);
}
}
