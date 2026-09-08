<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameChallenger;
use Illuminate\Support\Facades\DB;

class GameChallengerController extends Controller
{
    // challenge a game 
    public function challengeGame(Request $request,Game $game)
    {
       return DB::transaction(function () use ($request,$game){
        // concurrency controller
        $game=Game::where('id',$game->id)
        ->lockForUpdate()->first();
            //validation
            $request->validate([
    'color_guess' => ['required', 'in:red,black'],
                            ]);
             // user
        $challenger=$request->user();
        // creator
        $creator=$game->creator;
        // game status check
        if($game->status !== 'open')
            {
                abort(403,'Unauthorised action');
            }
        if ($challenger->id == $creator->id)
            {
                abort(403,'You can not challenge your own game');
            }
            // create challenge
        $challengerBalance=$challenger->wallet->balance;
        // game stake
        $gameStake=$game->stake;
        if ($challengerBalance < $gameStake)
            {
                abort(403,'You have insufficient funds to take on these challenge');
            }

        // challenger balance
        $challenger->wallet->decrement('balance',$gameStake);
        // compute result
        $creatorColor=$game->color;
        $challengerColor=$request->color_guess;
        if($creatorColor == $challengerColor)
            {
                $result="won";
                $challenger->wallet->increment('balance',$gameStake * 2);
            }
            else
                {
                 $result="lost";
                 $creator->wallet->increment('balance',$gameStake * 2);
                }
        // create challenge
        $challenge=GameChallenger::create([
                'game_id'=>$game->id,
                'challenger_id'=>$challenger->id,
                'color_guess'=>$request->color_guess,
                'result'=>$result
        ]);
        // update game
        $game->update([
            'status'=>'resolved'
        ]);
        // response
        return response()->json([
            'message'=>'Game resolved',
            'game'=>$game
        ]);
       });
    }
}
