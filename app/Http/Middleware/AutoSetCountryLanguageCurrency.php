<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Website\WebsiteController;
use Closure;
use Illuminate\Http\Request;

class AutoSetCountryLanguageCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $session_exist = session()->get('first_visit_unique'); // get session

        // if hasn't session it means visitor first visit site
        if (! $session_exist && config('templatecookie.set_ip_based_location')) {
            (new WebsiteController)->setCurrentLocation($request); // setup function call

            session()->put('first_visit_unique', true);

            return $next($request);

        } else { // already visited
            return $next($request);
        }
    }
}
