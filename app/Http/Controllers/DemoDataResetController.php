<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Utils\DemoResetHelper;

class DemoDataResetController extends Controller
{
  
    public function __construct()
    {
    }

    /**
     * Show the demo data reset page
     */
    public function show()
    {
        try {
            if (!DemoResetHelper::isResetting()) {
                return redirect('/');
            }
            
            return view('demo_data_reset');
        } catch (\Exception $e) {
            
            return response('<html><head><title>Demo Data Reset</title></head><body style="font-family: Arial, sans-serif; text-align: center; padding-top: 100px;"><h1>Demo Data Reset in Progress</h1><p>Please wait while we reset the demo data. This page will refresh automatically.</p><script>setTimeout(function(){ window.location.reload(); }, 5000);</script></body></html>', 503);
        }
    }

    /**
     * Check if demo data reset is complete
     */
    public function checkStatus(Request $request)
    {
        if (!$request->ajax() && !$request->expectsJson()) {
          return redirect('/');
        }
        
        try {
            $isResetting = DemoResetHelper::isResetting();
            
            if (!$isResetting) {
                return response()->json([
                    'resetting' => false,
                    'message' => 'complete'
                ]);
            }
            
            $message = DemoResetHelper::getProgress();
            
            return response()->json([
                'resetting' => $isResetting,
                'message' => $message
            ]);
        } catch (\Exception $e) {//
            return response()->json([
                'resetting' => true,
                'message' => 'Checking status...'
            ], 200);
        }
    }
}
