<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class DetectLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept-Language')) {
            if (in_array($request->header('Accept-Language'), ['en', 'fr'])) {
                $locale = $request->header('Accept-Language');
            } else {
                $locale = 'en';
            }
            App::setLocale($locale);
        }

        return $next($request);
    }
}
