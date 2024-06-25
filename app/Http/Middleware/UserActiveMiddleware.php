<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('user')->check() && authUser()->status) {

            return $next($request);
        } else {
            flashWarning(__('your_account_is_not_active_please_wait_until_the_account_is_activated_by_admin'));

            return redirect()->back();
        }
    }
}
