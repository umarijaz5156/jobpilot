<aside id="sidebar" class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ $setting->favicon_image_url }}" alt="{{ __('logo') }}" class="elevation-3">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-nav-wrapper">
            <!-- Sidebar Menu -->
            <nav class="sidebar-main-nav mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <x-admin.sidebar-list :linkActive="Route::is('admin.dashboard') ? true : false" route="admin.dashboard" parameter="" path="admin.dashboard"
                        plus_icon="" icon="fas fa-tachometer-alt">
                        {{ __('dashboard') }}
                    </x-admin.sidebar-list>
                    <li class="nav-header text-uppercase">{{ __('website_settings') }}</li>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.system') ? true : false" route="settings.system" parameter="" path="settings.system"
                        plus_icon="" icon="fas fa-hashtag">
                        {{ __('preferences') }}
                    </x-admin.sidebar-list>

                    @if (userCan('menu-setting.index'))
                        <x-admin.sidebar-list :linkActive="Route::is('menu-settings.*') ? true : false" route="menu-settings.index" path="menu-settings.index"
                            plus_icon="" icon="fas fa-bars">
                            {{ __('menu_settings') }}
                        </x-admin.sidebar-list>
                    @endif

                    <x-admin.sidebar-list :linkActive="Route::is('settings.social.login') ? true : false" route="settings.social.login" path="settings.social.login"
                        plus_icon="" icon="fab fa-facebook">
                        {{ __('social_login') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.cookies') ? true : false" route="settings.cookies" path="settings.cookies"
                        plus_icon="" icon="fas fa-cookie-bite">
                        {{ __('cookies_alert') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.seo.*') ? true : false" route="settings.seo.index" parameter="" path="settings.seo.index"
                        plus_icon="" icon="fas fa-award">
                        {{ __('seo') }} {{ __('settings') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.pages.*') ? true : false" route="settings.pages.index" parameter=""
                    path="settings.pages.index"
                        plus_icon="" 
                    icon="fas fa-desktop">
                    {{ __('pages') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.custom') ? true : false" route="settings.custom" path="settings.custom"
                        plus_icon="" parameter="" icon="fas fa-tools">
                        {{ __('custom_css_and_js') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.websitesetting') ? true : false" route="settings.websitesetting" parameter=""
                        path="settings.websitesetting" plus_icon="" icon="fas fa-tasks">
                        {{ __('cms') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.affiliate.index') ? true : false" route="settings.affiliate.index" path="settings.affiliate.index"
                        plus_icon="" icon="fab fa-affiliatetheme">
                        {{ __('affiliate') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.email-templates.list') ? true : false" route="settings.email-templates.list" path="settings.email-templates.list"
                        plus_icon="" icon="fas fa-mail-bulk">
                        {{ __('email_templates') }}
                    </x-admin.sidebar-list>

                    {{-- System Settings  --}}
                    <li class="nav-header text-uppercase">{{ __('system_settings') }}</li>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.general') ? true : false" route="settings.general" path="settings.general"
                        plus_icon="" parameter="" icon="fas fa-cog">
                        {{ __('general') }}
                    </x-admin.sidebar-list>
                    @if (auth()->user()->can('setting.view') ||
                            auth()->user()->can('setting.update'))
                        <x-admin.sidebar-list :linkActive="Route::is('languages.*') ? true : false" route="languages.index" parameter=""
                            path="languages.index" plus_icon="" icon="fas fa-globe-asia">
                            {{ __('language') }}
                        </x-admin.sidebar-list>
                    @endif
                    <x-admin.sidebar-dropdown :linkActive="Route::is('settings.payment') || Route::is('settings.payment.manual') ? true : false" :subLinkActive="Route::is('settings.payment') || Route::is('settings.payment.manual') ? true : false" icon="fas fa-globe">
                        @slot('title')
                            {{ __('location') }}
                        @endslot
                        @if (userCan('country.view'))
                            <ul class="nav nav-treeview">
                                <x-admin.sidebar-list :linkActive="Route::is('location.country.country') ? true : false" route="location.country.country" path="location.country.country"
                                    plus_icon="" icon="fas fa-circle">
                                    {{ __('country') }}
                                </x-admin.sidebar-list>
                            </ul>
                            <ul class="nav nav-treeview">
                                <x-admin.sidebar-list :linkActive="Route::is('location.state.state') ? true : false" route="location.state.state" path="location.state.state"
                                    plus_icon="" icon="fas fa-circle">
                                    {{ __('state') }}
                                </x-admin.sidebar-list>
                            </ul>
                            <ul class="nav nav-treeview">
                                <x-admin.sidebar-list :linkActive="Route::is('location.city.city') ? true : false" route="location.city.city" path="location.city.city"
                                    plus_icon="" icon="fas fa-circle">
                                    {{ __('city') }}
                                </x-admin.sidebar-list>
                            </ul>
                        @endif
                    </x-admin.sidebar-dropdown>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.theme') ? true : false" route="settings.theme" path="settings.theme"
                        plus_icon="" icon="fas fa-swatchbook">
                        {{ __('theme') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.email') ? true : false" route="settings.email" parameter="" path="settings.email"
                        plus_icon="" icon="fas fa-envelope">
                        {{ __('smtp') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('module.currency.*') ? true : false" route="module.currency.index" path="module.currency.index"
                        plus_icon="" icon="fas fa-dollar-sign">
                        {{ __('currency') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-dropdown :linkActive="Route::is('settings.payment') || Route::is('settings.payment.manual') ? true : false" :subLinkActive="Route::is('settings.payment') || Route::is('settings.payment.manual') ? true : false" icon="fas fa-credit-card">
                        @slot('title')
                            {{ __('payment_gateway') }}
                        @endslot
                        @if (userCan('country.view'))
                            <ul class="nav nav-treeview">
                                <x-admin.sidebar-list :linkActive="Route::is('settings.payment') ? true : false" route="settings.payment" path="settings.payment"
                                    plus_icon="" icon="fas fa-circle">
                                    {{ __('auto_payment') }}
                                </x-admin.sidebar-list>
                            </ul>
                        @endif
                        @if (userCan('map.view'))
                            <ul class="nav nav-treeview">
                                <x-admin.sidebar-list :linkActive="Route::is('settings.payment.manual') ? true : false" route="settings.payment.manual"
                                    path="settings.payment.manual" plus_icon="" parameter=""
                                    icon="fas fa-circle">
                                    {{ __('manual_payment') }}
                                </x-admin.sidebar-list>
                            </ul>
                        @endif
                    </x-admin.sidebar-dropdown>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.upgrade') ? true : false" path="settings.upgrade" plus_icon=""
                        route="settings.upgrade" icon="fas fa-upload">
                        {{ __('upgrade_guide') }}
                    </x-admin.sidebar-list>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.ad_setting') ?   true : false" path="settings.ad_setting" plus_icon=""
                        route="settings.ad_setting" icon="fas fa-ad">
                        {{ __('Place listing') }}
                    </x-admin.sidebar-list>
                    <li class="nav-item">
                        <a href="/log-viewer" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Log Viewer</p>
                        </a>
                    </li>
                    <x-admin.sidebar-list :linkActive="Route::is('settings.systemInfo') ? true : false" path="settings.systemInfo" plus_icon=""
                    route="settings.systemInfo" icon="fas fa-info">
                    {{ __('systemInfo') }}
                </x-admin.sidebar-list>
                </ul>
            </nav>
            <!-- Sidebar Menu -->
            <nav class="mt-2 nav-footer pt-3 border-top border-secondary">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview"
                    role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link bg-primary text-light">
                            <i class="nav-icon fas fa-chevron-left"></i>
                            <p>{{ __('go_back') }}</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- /.sidebar -->
</aside>