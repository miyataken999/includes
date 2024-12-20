<?php

namespace App\Http\Middleware;

use Closure;

class check_refasta_server
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $response = $next($request);

        // $response->headers->set('Access-Control-Allow-Headers', '*');
        // $response->headers->set('Access-Control-Allow-Methods', 'GET, POST');

        return $response;
    }
}
