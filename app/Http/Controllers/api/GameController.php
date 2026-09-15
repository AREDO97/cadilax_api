<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\Stake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\json;

class GameController extends Controller
{
    // create game
    public function create(Request $request)
    {
        return DB::transaction(function () use($request) {
            // creator
        $creator=$request->user();
        $creatorWalletBalance=$creator->wallet->balance;
        $stake=Stake::findOrFail($request->stake_id);
        if($stake->amount > $creatorWalletBalance)
            {
                abort(403,'You have insufficient wallet balance for that stake');
            }
        // create game
        $game = Game::create([
            'creator_id'=>$creator->id,
            'hint_id'=>$request->hint_id,
            'stake_id'=>$request->stake_id,
            'color'=>$request->color
        ]);

        // reduce wallet
        $creator->wallet->decrement('balance',$stake->amount);
        // response
        return response()->json([
            'message'=>'Game created successifully',
            'game'=>$game,
            'stake'=>$stake,
            'balance'=>$creatorWalletBalance
        ]);
        });
    }
    // user cancels his game when status still open
    public function cancel(Request $request, Game $game)
    {
      return   DB::transaction(function () use ($game, $request){
        $game = Game::where('id', $game->id)
                 ->lockForUpdate() ->first();
                    // creator of game
        $loggedUser=$request->user();
        $gameCreator=$game->creator;
        // check 
        if($loggedUser->id !== $gameCreator->id)
            {
                abort(403,'Unauthorised');
            }
        // check status
        if ($game->status !== 'open')
            {
                abort(403,'Unauthorised action, game already has challengers');
  
            }
        // cancel game
        $game->update([
            'status'=>'cancelled'
        ]);

        // stake 
        $stake=$game->stake->amount;
        // increment creator wallet
        $gameCreator->wallet->increment('balance',$stake);

         // response
        return response()->json([
            'message'=>'game cancelled successifully',
            'game'=>$game,
            'balance'=>$gameCreator->wallet->balance
        ]);
        });
       
    }
    // view all open games 
    public function index()
    {
        $games=Game::where('status','open')->latest()->paginate(10);
        // response
        return GameResource::collection($games);
    }
    // user games
    public function userGames(Request $request)
    {
        $user=$request->user();
        $userGames=Game::where('creator_id',$user->id)->get();
        return response()->json($userGames);
    }
    // games statics
    public function gameSummary(Request $request)
    {
               $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // overal total games played
        $totalgamesPlayed=Game::all()->count();
        // total games played today
        $totalgamesToday=Game::whereDate('created_at',today())->count();
        // total games played yesterday
        $totalgamesYesterday=Game::whereDate('created_at',today()->subDay())->count();
        // difference
        $gameDifference=$totalgamesToday - $totalgamesYesterday ;
        // comparison
        if($gameDifference > 0)
            {
                $comparison= abs($gameDifference). " more than yesterday" ;
            }
            if($gameDifference == 0)
                {
                $comparison= abs($totalgamesToday). ", which is the same as yesterday" ;   
                }
                   if($gameDifference < 0)
                    {
                        $comparison = abs($gameDifference). "less than yesterday";
                    }
        // response
        return response()->json([
            'total_games_played'=>$totalgamesPlayed,
            'total_games_today'=>$totalgamesToday,
            'total_yesterday_games'=>$totalgamesYesterday,
            'daily_comparisons'=>$comparison
        ]);
    }
}
