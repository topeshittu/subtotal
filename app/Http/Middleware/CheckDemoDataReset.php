<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Utils\DemoResetHelper;

class CheckDemoDataReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (DemoResetHelper::isResetting()) {
            try {
                if (Auth::check()) {
                    Auth::logout();
                }
                Session::flush();
            } catch (\Exception $e) {
            }
            
            if ($request->is('demo-data-reset') || $request->is('check-demo-status')) {
                try {
                    return $next($request);
                } catch (\Exception $e) {
                    //
                    return response('Demo data is being reset. Please wait...', 503);
                }
            }
            
            return redirect()->route('demo.data.reset');
        }

        return $next($request);
    }
}
