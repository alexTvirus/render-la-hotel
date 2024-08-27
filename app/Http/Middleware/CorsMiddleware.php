<?php
namespace App\Http\Middleware;

use Closure;


class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
 
		$response = $next($request);
		$response = $response instanceof RedirectResponse ? $response : response($response);
		$response->header('Access-Control-Allow-Origin', '*');
		
		return $next($request);
    }
}