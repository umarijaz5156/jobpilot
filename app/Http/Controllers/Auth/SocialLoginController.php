<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\Admin\NewUserRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect($provider)
    {
        if ($provider != 'google' && $provider != 'facebook' && $provider != 'github' && $provider != 'twitter' && $provider != 'linkedin-openid') {
            abort(404);
        }

        $this->forgetSocialSessions();
        session(['social_user' => request('user')]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if ($provider != 'google' && $provider != 'facebook' && $provider != 'github' && $provider != 'twitter' && $provider != 'linkedin-openid') {
            abort(404);
        }

        $this->forgetSocialSessions();

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login');
        }

        $socialiteUserId = $socialiteUser->getId();
        $socialiteUserName = $socialiteUser->getName();
        $socialiteUseremail = $socialiteUser->getEmail();
        $socialiteUserimage = $socialiteUser->getAvatar();

        $user = User::where([
            'provider' => $provider,
            'provider_id' => $socialiteUserId,
        ])->first();

        if (! $user) {
            session(['provider' => $provider]);
            session(['socialiteUserId' => $socialiteUserId]);
            session(['socialiteUserName' => $socialiteUserName]);
            session(['socialiteUseremail' => $socialiteUseremail]);
            session(['socialiteUserimage' => $socialiteUserimage]);

            session()->flash('warning', __('we_couldnot_find_any_accounts_please_continue_to_register_as_a_candidate_or_employer'));

            return redirect()->route('login', ['social_register' => true]);
        }

        Auth::guard('user')->login($user);

        $this->forgetSocialSessions();

        return redirect()->route('user.dashboard');
    }

    public function register(Request $request)
    {
        $provider = session('provider');
        $socialiteUserId = session('socialiteUserId');
        $socialiteUserName = session('socialiteUserName');
        $socialiteUseremail = session('socialiteUseremail');
        $socialiteUserimage = session('socialiteUserimage');

        // Checking email exists or not
        $is_exists_email = User::where('email', $socialiteUseremail)->exists();
        $email = $is_exists_email ? $socialiteUseremail.'_'.uniqid() : $socialiteUseremail;

        // Create user account
        $user = User::create([
            'name' => $socialiteUserName,
            'email' => $email,
            'username' => Str::slug($socialiteUserName).'_'.Str::random(5),
            'image' => $socialiteUserimage,
            'provider' => $provider,
            'provider_id' => $socialiteUserId,
            'role' => $request->user,
            'email_verified_at' => now(),
        ]);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegisteredNotification($admin, $user));
        }

        Auth::guard('user')->login($user);

        $this->forgetSocialSessions();

        return redirect()->route('user.dashboard');
    }

    protected function forgetSocialSessions()
    {
        session()->forget('provider');
        session()->forget('socialiteUserId');
        session()->forget('socialiteUserName');
        session()->forget('socialiteUseremail');
        session()->forget('socialiteUserimage');
        session()->forget('socialiteUserimage');
    }
}
