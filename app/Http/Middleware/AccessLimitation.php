<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessLimitation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::guard('admin')->user()) {
        //     if (auth('admin')->user()->email !== 'developer@mail.com') {
        //         session()->flash('error', 'This action is disabled for demo');
        //         return redirect()->back();
        //     }
        // };

        // if (Auth::guard('user')->user()) {

        //     if (auth('user')->user()->email == 'company@mail.com') {
        //         session()->flash('error', 'This action is disabled for demo');
        //         return redirect()->back();
        //     }

        //     if (auth('user')->user()->email == 'candidate@mail.com') {
        //         session()->flash('error', 'This action is disabled for demo');
        //         return redirect()->back();
        //     }
        // }

        return $next($request);
    }
}
