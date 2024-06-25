@php
    $user = auth()->user();
    $languages = loadLanguage();
    $current_language = currentLanguage() ? currentLanguage() : loadDefaultLanguage();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
        <i class="flag-icon {{ $current_language?->icon }}"></i>
        <span class="text-uppercase">{{ $current_language->code }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
        @foreach ($languages as $lang)
            <a class="dropdown-item {{ $current_language->code === $lang->code ? 'active' : '' }}"
                href="{{ route('changeLanguage', $lang->code) }}">
                {{ $lang->name }}
            </a>
        @endforeach
    </div>
</li>
<li class="nav-item">
    <a class="nav-link d-flex justify-content-center align-items-center" data-widget="fullscreen" href="#"
        role="button">
        <i class="fas fa-expand-arrows-alt"></i>
    </a>
</li>
<li class="nav-item">
    <form action="{{ route('settings.mode.update') }}" method="post" id="mode_form">
        @csrf
        @method('PUT')
        @if ($setting->dark_mode)
            <input type="hidden" name="dark_mode" value="0">
        @else
            <input type="hidden" name="dark_mode" value="1">
        @endif
    </form>
    <a onclick="$('#mode_form').submit()" class="nav-link d-flex justify-content-center align-items-center"
        href="#" role="button">
        @if ($setting->dark_mode)
            <i class="fas fa-sun"></i>
        @else
            <i class="fas fa-moon"></i>
        @endif
    </a>
</li>
<li class="nav-item dropdown" onclick="ReadNotification()">
    <a class="nav-link d-flex justify-content-center align-items-center" data-toggle="dropdown" href="#"
        aria-expanded="true">
        <i class="fas fa-bell"></i>
        @php
            $adminUnNotificationsCount = adminUnNotifications();
        @endphp
        @if ($adminUnNotificationsCount != 0)
            <span class="badge badge-warning navbar-badge" id="unNotifications">
                {{ $adminUnNotificationsCount }}
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notification-panel">
        <span class="dropdown-item dropdown-header text-md card-header">{{ __('notifications') }}</span>
        @php
            $adminNotifications = adminNotifications();
        @endphp
        @if ($adminNotifications->count() > 0)
            @foreach ($adminNotifications as $notification)
                <div class="dropdown-divider"></div>
                <a href="{{ $notification->data ? $notification->data['url'] : '' }}" class="dropdown-item word-break">
                    <p>
                        @if ($notification->type == 'App\Notifications\Admin\NewJobAvailableNotification')
                            <i class="fas fa-briefcase"></i>
                        @elseif ($notification->type == 'App\Notifications\Admin\NewPlanPurchaseNotification')
                            <i class="fas fa-credit-card"></i>
                        @elseif ($notification->type == 'App\Notifications\Admin\NewUserRegisteredNotification')
                            <i class="fas fa-user"></i>
                        @endif
                        &nbsp;
                        {{ $notification->data ? $notification->data['title'] : '-' }}
                    </p>
                    <span class="float-right text-muted text-sm">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </a>
            @endforeach
        @else
            <span class="d-flex justify-content-center mb-2 p-2 text-sm">
                {{ __('no_notification') }}
            </span>
        @endif
        @if ($adminNotifications->count() > 6)
            <div class="dropdown-divider"></div>
            <a href="{{ route('admin.all.notification') }}"
                class="dropdown-item dropdown-footer">{{ __('see_all_notifications') }}
            </a>
        @endif
    </div>
</li>
<li class="nav-item dropdown user-menu">
    <a href="{{ route('profile') }}" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <img src="{{ asset($user->image_url) }}" class="user-image img-circle elevation-2" alt="User Image">
        <span class="d-none d-md-inline">{{ $user->name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right rounded border-0">
        <!-- User image -->
        <li class="user-header bg-primary rounded-top">
            <img src="{{ $user->image_url }}" class="user-image img-circle elevation-2"
                alt="{{ __('user_image') }}">
            <p>
                {{ $user->name }} -
                @foreach ($user->getRoleNames() as $role)
                    (<span>{{ ucwords($role) }}</span>)
                @endforeach
                <small>{{ __('member_since') }} {{ $user->created_at->format('M d, Y') }}</small>
            </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer border-bottom d-flex">
            <a href="{{ route('profile') }}" class="btn btn-default">{{ __('profile') }}</a>
            <a href="javascript:void(0)"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                class="btn btn-default ml-auto">{{ __('log_out') }}</a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none invisible">
                @csrf
            </form>
        </li>
    </ul>
</li>
