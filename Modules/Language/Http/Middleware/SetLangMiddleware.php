<?php

namespace Modules\Language\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $current_lang = session('current_lang');

        if (session()->has('current_lang')) {
            app()->setLocale($current_lang->code);
        } else {
            app()->setLocale(config('templatecookie.default_language'));
        }

        return $next($request);
    }
}
