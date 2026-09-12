<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameChallenger;
use App\Models\PlatformRevenue;
use App\Models\PlatformSetting;
use App\Models\WalletTransaction;
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
        $gameStake=$game->stake->amount;
        if ($challengerBalance < $gameStake)
            {
                abort(403,'You have insufficient funds to take on these challenge');
            }

        // challenger balance
          $challenger->wallet->decrement('balance',$gameStake);
          $challengerInitialAmount=$challenger->wallet->fresh()->balance;
        /*
         platform revenue computation
         $userId,$gameId,$percentage,$amount,$type
            */
         // deduct from game creator platform revenue
         // compute percenatge
         $percentage=PlatformSetting::where('is_active',true)->first();
         // compute amount
         $amountCharged=($percentage->percentage/100) * $gameStake; 
         PlatformRevenue::Collect(
            $challenger->id,
            $game->id,
            $percentage->percentage,
            $amountCharged,
            'charged game creator'
         );

         // deduct from game creator platform revenue
          PlatformRevenue::Collect(
            $creator->id,
            $game->id,
            $percentage->percentage,
            $amountCharged,
            'charged game challenger'
         );
        // compute result
        $creatorColor=$game->color;
        $challengerColor=$request->color_guess;
        // compute actuall amount won
        $amountBeforeTax=$gameStake * 2;
        $amountAfterTax=$amountBeforeTax - ($amountCharged * 2);

         // creator wallet amount
            $creatorWalletAmount = $creator->wallet->balance;
        /*
        Wallet transactions
        $walletId,$type,$amount,$balance_after,$reference* */
        if($creatorColor == $challengerColor)
            {
                $result="won";
                $challenger->wallet->increment('balance',$amountAfterTax);
                $challengerWalletAmount =$challenger->wallet->fresh()->balance;
                // wallet transaction for challenger
            WalletTransaction::Transaction(
                $challenger->wallet->id,
                'won game',
                $amountAfterTax,
                $challengerWalletAmount,
                $game->id.' #'
            );
            // game creator transaction
           
             WalletTransaction::Transaction(
                $creator->wallet->id,
                'lost game',
                $gameStake,
                $creatorWalletAmount,
                $game->id.' #'
            );
            }
            else
                {
                 $result="lost";
                 $creator->wallet->increment('balance',$amountAfterTax);
                 $creatorWalletAmount = $creator->wallet->fresh()->balance;
                 // compute creator transactions
                WalletTransaction::Transaction(
                $creator->wallet->id,
                'won game',
                $amountAfterTax,
                $creatorWalletAmount,
                $game->id.' #'
            ); 

            // challenger transaction computation
              WalletTransaction::Transaction(
                $challenger->wallet->id,
                'lost game',
                $gameStake,
                $challengerInitialAmount,
                $game->id.' #'
            );
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
