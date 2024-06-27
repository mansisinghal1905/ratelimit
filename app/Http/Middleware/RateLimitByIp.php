<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DB;
class RateLimitByIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        $ipAddress = $request->ip();

        $activeSession = DB::table('sessions')
                            ->where('ip_address', $ipAddress)
                            ->where('is_active', 1)
                            ->first();
                            
        if ($activeSession) {
            return response()->view('session.exists', ['ip' => $ipAddress, 'session_id' => $activeSession->id]);
        }

        return $next($request);
    }
}
