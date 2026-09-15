<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PlatformRevenue;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    // get total revenue
    public function index(Request $request)
    {
        $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // compute total revenue
       
        $totalRevenue = PlatformRevenue::sum('amount');
        // response
        return response()->json($totalRevenue);

    }
    // revenue summary
    public function revenueSummary(Request $request)
    {
          $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin')
        {
        abort(403,'Unauthorised action');
        }
        // revenue today
        $todayRevenue=PlatformRevenue::whereDate('created_at',today())
        ->sum('amount');
        // revenue yesterday
        $yesterdayRevenue=PlatformRevenue::whereDate('created_at',today()->subDay())
        ->sum('amount');
        //compute summary difference
        $amountDifference=$todayRevenue - $yesterdayRevenue ;
        if($amountDifference > 0)
            {
            $comparison=abs($amountDifference)." more than yesterday";
            if($yesterdayRevenue !== 0)
                {
            $percentageIncreaseDaily=($amountDifference/$yesterdayRevenue) * 100 ;

                }
                else
                    {
                    $percentageIncreaseDaily= 100 . "%";  
                    }

            }
        elseif($amountDifference == 0)
        {
            $comparison=abs($todayRevenue) ." exact amount as yesterday";
        }
        elseif ($amountDifference < 0)
            {
                $comparison=abs($amountDifference). "less than yesterday";
            }

            // monthly computations
            // current month total revenue
        $currentMonthRevenue=PlatformRevenue::whereBetween('created_at',[
           today()->startOfMonth(),
           today()->endOfMonth() 
        ])->sum('amount');
        // last month total revenue
        $lastMonthRevenue=PlatformRevenue::whereBetween('created_at',[
            today()->subMonth()->startOfMonth(),
            today()->subMonth()->endOfMonth()
        ])->sum('amount');
        // monthly difference
        $monthlyDifference=$currentMonthRevenue - $lastMonthRevenue;
  if($monthlyDifference > 0)
            {
            $monthlyComparison=abs($monthlyDifference)." more than last month";
            }
        elseif($monthlyDifference == 0)
        {
            $monthlyComparison=abs($lastMonthRevenue) ." exact amount as last month";
        }
        elseif ($monthlyDifference < 0)
            {
                $monthlyComparison=abs($monthlyDifference). "less than last month";
            }

        // response
        return response()->json([
            'today_revenue'=>$todayRevenue,
            'yesterday_revenue'=>$yesterdayRevenue,
            'difference'=>$comparison,
            'percentage_increase_daily'=>$percentageIncreaseDaily,
            'total_last_month'=>$lastMonthRevenue,
            'total_this_month'=>$currentMonthRevenue,
            'monthly_comparison'=>$monthlyComparison
        ]);
    }
}
