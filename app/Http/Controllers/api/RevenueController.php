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
        $allInPlatformRevenue=PlatformRevenue::all();
        $totalRevenue = $allInPlatformRevenue->sum('amount');
        // response
        return response()->json($totalRevenue);

    }
}
