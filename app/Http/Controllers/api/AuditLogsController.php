<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    // view all
    public function index()
    {
        $logs=AuditLog::latest()->paginate(10);
        // response
        return response()->json($logs);
    }
    // delete logs
    public function destroy(AuditLog $log)
    {
        $log->delete();
        // response 
        return response()->json([
            'message'=>'Log deleted successifully'
        ]);
    }
    // delete all
   public function clearAll()
{
    AuditLog::where('created_at', '<', now()->subHours(24))
        ->delete();

    return response()->json([
        'message' => 'Old logs deleted successfully'
    ]);
}
}
