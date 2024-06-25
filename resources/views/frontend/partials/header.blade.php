{{-- For testing environment  --}}
@if (config('templatecookie.testing_mode'))
    @php
        $headerCountries = Modules\Location\Entities\Country::select('id', 'name', 'slug', 'icon')->active()->get();
        $headerCurrencies = Modules\Currency\Entities\Currency::all();
        $languages = loadLanguage();
        $defaultLanguage = Modules\Language\Entities\Language::where('code', config('templatecookie.default_language'))->first();
    @endphp
@endif
{{-- For testing environment --}}

<header class="header rt-fixed-top">
    <div class="n-header">
        <div class="n-header--top relative">
            @auth('user')
                @if (!authUser()->status)
                    <div class="alert alert-danger" role="alert">
                        <div class="container tw-px-0">
                            <div class="rt-ml-13">
                                {{ __('your_account_is_not_active_please_wait_until_the_account_is_activated_by_admin') }}
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
            <div class="container tw-px-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="n-header--top__left main-menu">
                        <div
                            class="mbl-top d-flex align-items-center justify-content-between container position-relative d-lg-none">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('website.home') }}" class="brand-logo">
                                    <img src="{{ $setting->dark_logo_url }}" alt="logo">
                                </a>
                            </div>

                            <div class="">
                                <div class="d-flex align-items-center ">
                                    <div class="search-icon d-lg-none tw-text-white">
                                        <svg id="mblSearchIcon" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M20.9999 21L16.6499 16.65" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <div class="mblTogglesearch bg-primary-500 rounded">
                                        <form action="{{ route('website.job') }}" method="GET" id="search-form"
                                            class="shadow px-md-5 py-md-3 p-3 !tw-bg-white rounded w-sm-75 w-100">
                                            <div class="form-item">
                                                <input name="keyword" class="search-input w-100" type="text"
                                                    placeholder="{{ __('job_title_keyword') }}"
                                                    value="{{ request('keyword') }}" id="mobile_search_input">
                                            </div>
                                        </form>
                                    </div>
                                    @auth('user')
                                        <ul
                                            class="custom-border list-unstyled d-flex align-items-center justify-content-end">
                                            @if (auth()->user()->role == 'company')
                                                <x-website.company.notifications-component />
                                            @endif

                                            @if (auth()->user()->role == 'candidate')
                                                <x-website.candidate.notifications-component />
                                            @endif

                                            @company
                                                <li class="relative">
                                                    <a href="{{ route('user.dashboard') }} " class="candidate-profile p-0">
                                                        <img src="{{ auth()->check() ? auth()->user()?->company?->logo_url : '' }}"
                                                            alt="company logo">
                                                    </a>
                                                </li>
                                            @else
                                                <li class="relative">
                                                    <a href="{{ route('user.dashboard') }} " class="candidate-profile p-0">
                                                        <img src="{{ auth()->check() ? auth()->user()?->candidate?->photo : '' }}"
                                                            alt="user logo">
                                                    </a>
                                                </li>
                                            @endcompany

                                            @if (!request()->is('email/verify'))
                                                @if (auth()->user()->role !== 'company' && auth()->user()->role !== 'candidate')
                                                    <li>
                                                        <a href="{{ route('company.job.create') }}">
                                                            <button class="btn btn-primary">
                                                                {{ __('post_job') }}
                                                            </button>
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif

                                            @if (request()->is('email/verify'))
                                                <li>
                                                    <a href="{{ route('logout') }}"
                                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <button class="btn btn-primary">
                                                            {{ __('log_out') }}
                                                        </button>
                                                    </a>
                                                </li>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            @endif
                                        </ul>
                                    @endauth

                                    @guest
                                        <ul class="list-unstyled">
                                            <li>
                                                <a href="{{ route('company.job.create') }}"
                                                    class="btn btn-primary text-white"
                                                    style="padding:12px 24px !important;">{{ __('post_job') }}
                                                </a>
                                            </li>
                                        </ul>
                                    @endguest
                                </div>
                            </div>
                        </div>
                        @if (auth('user')->check())
                            @if (authUser()->role == 'company')
                                <div class="container">
                                    <ul class="menu-active-classes">
                                        @if (isset($company_menu_lists))
                                            @foreach ($company_menu_lists as $company_menu_list)
                                                <li class="menu-item">
                                                    @php
                                                        // Check if the URL starts with "http" or "https" to identify external links
                                                        $isExternalLink = Str::startsWith($company_menu_list['url'], ['http://', 'https://']);
                                                    @endphp
                                                    <a href="{{ $company_menu_list['url'] }}"
                                                        @if ($isExternalLink) target="_blank" @endif
                                                        class="{{ urlMatch(url()->current(), url($company_menu_list['url'])) ? 'text-primary active' : '' }}">
                                                        @if ($company_menu_list['title'])
                                                            {{ $company_menu_list['title'] }}
                                                        @else
                                                            @if ($company_menu_list['en_title'])
                                                                {{ $company_menu_list['en_title'] }}
                                                            @endif
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                            @if ($custom_pages->where('show_header', 1)->count() > 0 )
                                            <li class="menu-item extra-page d-none d-lg-inline-block">
                                                <a href="javascript:void(0)" class="dropdown-toggle">
                                                    Extra Pages
                                                </a>
                                                <ul class="ll-dropdown-menu">
                                                    @foreach ($custom_pages->where('show_header', 1) as $page)
                                                        <li>
                                                            <a class="!tw-px-5 !tw-py-2"
                                                                href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                            @endif
                                            
                                            @foreach ($custom_pages->where('show_header', 1) as $page)
                                                <li class="d-lg-none">
                                                    <a class=""
                                                        href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                    <div class="tw-mb-post-job">
                                        <a href="{{ route('company.job.create') }}">
                                            <button class="btn btn-primary">
                                                {{ __('post_job') }}
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="container">
                                    <ul class="menu-active-classes ">
                                        @if (isset($candidate_menu_lists))
                                            @foreach ($candidate_menu_lists as $candidate_menu_list)
                                                <li class="menu-item">
                                                    @php
                                                        // Check if the URL starts with "http" or "https" to identify external links
                                                        $isExternalLink = Str::startsWith($candidate_menu_list['url'], ['http://', 'https://']);
                                                    @endphp
                                                    <a href="{{ $candidate_menu_list['url'] }}"
                                                        @if ($isExternalLink) target="_blank" @endif
                                                        class="{{ urlMatch(url()->current(), url($candidate_menu_list['url'])) ? 'text-primary active' : '' }}">
                                                        @if ($candidate_menu_list['title'])
                                                            {{ $candidate_menu_list['title'] }}
                                                        @else
                                                            @if ($candidate_menu_list['en_title'])
                                                                {{ $candidate_menu_list['en_title'] }}
                                                            @endif
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                            <li class="menu-item extra-page d-none d-lg-inline-block">
                                                <a href="javascript:void(0)" class="dropdown-toggle">
                                                    Extra Pages
                                                </a>
                                                <ul class="ll-dropdown-menu">
                                                    @foreach ($custom_pages->where('show_header', 1) as $page)
                                                        <li>
                                                            <a class="!tw-px-5 !tw-py-2"
                                                                href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                            @foreach ($custom_pages->where('show_header', 1) as $page)
                                                <li class="d-lg-none">
                                                    <a class=""
                                                        href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        @else
                            <div class="container">
                                <ul class="menu-active-classes">
                                    @if (isset($public_menu_lists))
                                        @foreach ($public_menu_lists as $public_menu_list)
                                            <li class="menu-item">
                                                @php
                                                    // Check if the URL starts with "http" or "https" to identify external links
                                                    $isExternalLink = Str::startsWith($public_menu_list['url'], ['http://', 'https://']);
                                                @endphp
                                                <a href="{{ $public_menu_list['url'] }}"
                                                    @if ($isExternalLink) target="_blank" @endif
                                                    class="{{ urlMatch(url()->current(), url($public_menu_list['url'])) ? 'text-primary active' : '' }}">
                                                    @if ($public_menu_list['title'])
                                                        {{ $public_menu_list['title'] }}
                                                    @else
                                                        @if ($public_menu_list['en_title'])
                                                            {{ $public_menu_list['en_title'] }}
                                                        @endif
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                        @if ($custom_pages->where('show_header', 1)->count() > 0 )
                                        <li class="menu-item extra-page d-none d-lg-inline-block">
                                            <a href="javascript:void(0)" class="dropdown-toggle">
                                                 {{__('extra_pages')}}
                                            </a>
                                            <ul class="ll-dropdown-menu">
                                                @foreach ($custom_pages->where('show_header', 1) as $page)
                                                    <li>
                                                        <a class="!tw-px-5 !tw-py-2"
                                                            href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                         @endif
                                        @foreach ($custom_pages->where('show_header', 1) as $page)
                                            <li class="d-lg-none">
                                                <a class="{{ urlMatch(url()->current(), url($public_menu_list['url'])) ? 'text-primary active' : '' }}"
                                                    href="{{ route('showCustomPage', $page->slug) }}">{{ $page->title }}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <div class="xs:tw-hidden tw-mt-6 mbl-bottom">
                            <div class="container">
                                @if ($cms_setting?->footer_phone_no)
                                    <div class="contact-info">
                                        <a class="text-gray-900" href="tel:{{ $cms_setting?->footer_phone_no }}">
                                            <x-svg.telephone2-icon />
                                            {{ $cms_setting?->footer_phone_no }}
                                        </a>
                                    </div>
                                @endif
                                @if ($setting->app_country_type === 'multiple_base')
                                    <form action="{{ route('website.job') }}" method="GET" id="search-form">
                                        <div class="tw-flex tw-w-full">
                                            @php
                                                $selected_country = session('selected_country');
                                            @endphp
                                            <div class="dropdown dropup tw-w-full">
                                                <button
                                                    class="btn tw-flex tw-justify-between tw-w-full tw-px-0 dropdown-toggle"
                                                    type="button" id="" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <div>
                                                        @if ($selected_country && selected_country())
                                                            <i class="flag-icon {{ selected_country()->icon }}"></i>
                                                            {{ selected_country()->name }}
                                                        @else
                                                            {{ __('all_country') }}
                                                        @endif
                                                    </div>
                                                </button>

                                                <ul class="dropdown-menu mx-height-400 overflow-auto tw-p-2"
                                                    aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                            href="{{ route('website.set.country') }}">
                                                            <svg width="26" height="26" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 6h16M4 10h16M4 14h16M4 18h16">
                                                                </path>
                                                            </svg>
                                                            <span class="marginleft">
                                                                {{ __('all_country') }}
                                                            </span>
                                                        </a>
                                                    </li>

                                                    @foreach ($headerCountries as $country)
                                                        <li id="lang-dropdown-item">
                                                            <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                                href="{{ route('website.set.country', ['country' => $country->id]) }}">
                                                                <i class="flag-icon {{ $country->icon }}"></i>
                                                                {{ $country->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                @if (count($headerCurrencies) && $setting->currency_switcher)
                                    @php
                                        $currency_count = count($headerCurrencies) && count($headerCurrencies) > 1;
                                        $current_currency_code = currentCurrencyCode();
                                    @endphp
                                    <div class="dropdown dropup">
                                        <button
                                            class="btn tw-flex tw-w-full tw-justify-between tw-px-0 {{ count($headerCurrencies) ? 'dropdown-toggle' : '' }}"
                                            type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            {{ $current_currency_code }}
                                        </button>
                                        @if ($currency_count)
                                            <ul class="dropdown-menu tw-p-2" aria-labelledby="dropdownMenuButton1">
                                                @foreach ($headerCurrencies as $currency)
                                                    @if ($currency->code != $current_currency_code)
                                                        <li id="lang-dropdown-item">
                                                            <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                                href="{{ route('changeCurrency', $currency->code) }}">
                                                                {{ $currency->code }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="n-header--top__right d-flex align-items-center tw-px-3">
                        @if ($cms_setting?->footer_phone_no)
                            <div class="contact-info xs:tw-inline-flex tw-hidden">
                                <a class="text-gray-900" href="tel:{{ $cms_setting?->footer_phone_no }}">
                                    <x-svg.telephone2-icon />
                                    {{ $cms_setting?->footer_phone_no }}
                                </a>
                            </div>
                        @endif
                        @if ($setting->language_changing)
                            <div class="dropdown">
                                @php
                                    $language_count = count($languages) && count($languages) > 1;
                                    $language_count2 = count($languages);
                                    $current_language = currentLanguage() ? currentLanguage() : loadDefaultLanguage();
                                @endphp
                                <button class="btn {{ $language_count ? 'dropdown-toggle' : '' }}" type="button"
                                    id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i
                                        class="flag-icon {{ isset($current_language->icon) && $current_language->icon ? $current_language->icon : '' }}"></i>
                                    {{ isset($current_language->name) && $current_language->name ? $current_language->name : '' }}
                                </button>
                                @if ($language_count)
                                    <ul class="dropdown-menu mx-height-300 overflow-auto tw-p-2"
                                        aria-labelledby="dropdownMenuButton1">
                                        @foreach ($languages as $lang)
                                            @if (isset($current_language->code) && $current_language->code != $lang->code)
                                                <li id="lang-dropdown-item">
                                                    <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                        href="{{ route('changeLanguage', $lang->code) }}">
                                                        <i
                                                            class="flag-icon {{ isset($lang->icon) && $lang->icon ? $lang->icon : '' }} tw-me-2.5"></i>
                                                        {{ $lang->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                        @if (count($headerCurrencies) && $setting->currency_switcher)
                            @php
                                $currency_count = count($headerCurrencies) && count($headerCurrencies) > 1;
                                $current_currency_code = currentCurrencyCode();
                            @endphp
                            <div class="dropdown xs:tw-inline-flex tw-hidden">
                                <button class="btn {{ count($headerCurrencies) ? 'dropdown-toggle' : '' }}"
                                    type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ $current_currency_code }}
                                </button>
                                @if ($currency_count)
                                    <ul class="dropdown-menu mx-height-300 overflow-auto tw-p-2"
                                        aria-labelledby="dropdownMenuButton1">
                                        @foreach ($headerCurrencies as $currency)
                                            @if ($currency->code != $current_currency_code)
                                                <li id="lang-dropdown-item">
                                                    <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                        href="{{ route('changeCurrency', $currency->code) }}">
                                                        {{ $currency->code }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif
                        @if ($setting->app_country_type === 'multiple_base')
                            <form action="{{ route('website.job') }}" method="GET" id="search-form"
                                class="mx-width-300 xs:tw-inline-flex tw-hidden">
                                <div class="d-flex">
                                    @php
                                        $selected_country = session('selected_country');
                                    @endphp
                                    <div class="">
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle" type="button" id=""
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                @if ($selected_country && selected_country())
                                                    <i class="flag-icon {{ selected_country()->icon }}"></i>
                                                    {{ selected_country()->name }}
                                                @else
                                                    {{ __('all_country') }}
                                                @endif
                                            </button>

                                            <ul class="dropdown-menu mx-height-300 overflow-auto tw-p-2"
                                                aria-labelledby="dropdownMenuButton1">
                                                <li>
                                                    <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                        href="{{ route('website.set.country') }}">
                                                        <svg width="26" height="26" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16">
                                                            </path>
                                                        </svg>
                                                        <span class="marginleft">
                                                            {{ __('all_country') }}
                                                        </span>
                                                    </a>
                                                </li>
                                                @foreach ($headerCountries as $country)
                                                    <li id="lang-dropdown-item">
                                                        <a class="dropdown-item hover:tw-bg-[#F1F2F4] hover:tw-rounded-[4px]"
                                                            href="{{ route('website.set.country', ['country' => $country->id]) }}">
                                                            <i class="flag-icon {{ $country->icon }}"></i>
                                                            {{ $country->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="mobile-menu">
                        <div class="menu-click tw-pe-3">
                            <button class="effect1">
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Header top -->
        <div class="n-header--bottom">
            <div class="container position-relative">
                <div class="d-flex flex-wrap  tw-gap-2 tw-items-center">
                    <div class="n-header--bottom__left d-flex align-items-center">
                        <a href="{{ route('website.home') }}" class="brand-logo">
                            <img src="{{ $setting->dark_logo_url }}" alt="logo">
                        </a>
                        <form action="{{ route('website.job') }}" method="GET" id="search-form"
                            class="mx-width-300 header-search-form d-lg-block d-none">
                            <div class="search-box">
                                <select id="headerSearch" onchange="changeSearchSelection()" class="form-select"
                                    aria-label="Default select example">
                                    <option @selected(session('header_search_role') == 'job') value="job">{{ __('jobs') }}</option>
                                    <option @selected(session('header_search_role') == 'candidate') value="candidate">{{ __('candidate') }}
                                    </option>
                                    <option @selected(session('header_search_role') == 'company') value="company">{{ __('company') }}</option>
                                </select>
                                <div class="d-flex flex-column flex-md-row align-items-center tw-ps-3">
                                    <svg class="searcbox-searchicon" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                            stroke="#0A65CC" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M21 20.9999L16.65 16.6499" stroke="#0A65CC" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <input name="keyword" class="search-input" type="text"
                                        placeholder="{{ __('job_title_keyword') }}" value="{{ request('keyword') }}"
                                        id="global_search">
                                </div>

                                <span id="autocomplete_job_results"></span>
                            </div>
                        </form>
                    </div>

                    <div class="n-header--bottom__right">
                        <div class="d-flex align-items-center ">
                            <div class="search-icon tw-ml-2 d-lg-none !tw-cursor-pointer">
                                <span>
                                    <svg id="searchIcon" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                            stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M20.9999 21L16.6499 16.65" stroke="#FFFFFF" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <div class="togglesearch">
                                <form action="{{ route('website.job') }}" method="GET" id="search-form"
                                    class="shadow px-md-5 py-md-3 p-3 !tw-bg-white rounded w-sm-75 w-100">

                                    <div class="search-box form-item position-relative">
                                        <svg class="" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                                stroke="#0A65CC" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M21 20.9999L16.65 16.6499" stroke="#0A65CC" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <input name="keyword" class="search-input w-100" type="text"
                                            placeholder="{{ __('job_title_keyword') }}"
                                            value="{{ request('keyword') }}" id="search_input">

                                    </div>
                                </form>
                            </div>
                            @auth('user')
                                <ul class="list-unstyled tw-gap-6 tw-flex tw-items-center tw-justify-between">

                                    @if (auth()->user()->role == 'company')
                                        <x-website.company.notifications-component />
                                    @endif
                                    @if (auth()->user()->role == 'candidate')
                                        <x-website.candidate.notifications-component />
                                    @endif

                                    <x-website.company.message-component />

                                    <div class="dropdown dropstart">
                                        <a href="javascript:void(0)" class="candidate-profile position-relative"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            @company
                                                <img src="{{ auth()->user()->company->logo_url }}" alt="logo">
                                            @else
                                                <img src="{{ auth()->user()->candidate->photo }}" alt="photo">
                                                @if (auth()->user()->candidate->status == 'available')
                                                    <span class="available-alert-header">
                                                        <svg class="circle" width="14" height="14"
                                                            viewBox="0 0 14 14" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="7" cy="7" r="6" fill="#2ecc71"
                                                                stroke="white" stroke-width="2">
                                                            </circle>
                                                        </svg>
                                                    </span>
                                                @endif
                                            @endcompany
                                        </a>
                                        @candidate
                                        <ul class="custom-border dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('candidate.dashboard') ? 'active' : '' }}"
                                                    href="{{ route('candidate.dashboard') }}">
                                                    <i class="ph-stack"></i>
                                                    {{ __('dashboard') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('candidate.setting') ? 'active' : '' }}"
                                                    href="{{ route('candidate.setting') }}">
                                                    <i class="ph-gear"></i>
                                                    {{ __('settings') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="ph-sign-out"></i>
                                                    {{ __('log_out') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                    @else
                                        <ul class="dropdown-menu custom-border" aria-labelledby="dropdownMenuButton1">
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('company.dashboard') ? 'active' : '' }}"
                                                    href="{{ route('company.dashboard') }}">
                                                    <i class="ph-stack"></i>
                                                    {{ __('dashboard') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('company.myjob') ? 'active' : '' }}"
                                                    href="{{ route('company.myjob') }}">
                                                    <i class="ph-suitcase-simple"></i>
                                                    {{ __('my_jobs') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('company.plan') ? 'active' : '' }}"
                                                    href="{{ route('company.plan') }}">
                                                    <i class="ph-notebook"></i>
                                                    {{ __('plans_billing') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request()->routeIs('company.setting') ? 'active' : '' }}"
                                                    href="{{ route('company.setting') }}">
                                                    <i class="ph-gear"></i>
                                                    {{ __('settings') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="ph-sign-out"></i>
                                                    {{ __('log_out') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            </li>
                                        </ul>
                                        @endcandidate
                                    </div>
                                    @if (!request()->is('email/verify'))
                                        @company
                                            <li class="tw-hidden sm:tw-block">

                                                <a href="{{ route('company.job.create') }}">
                                                    <button class="btn btn-light">
                                                        {{ __('post_job') }}
                                                    </button>
                                                </a>
                                            </li>
                                        @endcompany
                                    @endif
                                    @if (request()->is('email/verify'))
                                        <li>
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <button class="btn btn-primary">
                                                    {{ __('log_out') }}
                                                </button>
                                            </a>
                                        </li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    @endif
                                </ul>
                            @endauth
                            @guest
                                <ul class="list-unstyled tw-flex tw-flex-wrap tw-gap-3 tw-items-center tw-justify-between">
                                    <li>
                                        <a href="{{ route('login') }}"
                                            class="btn btn-outline-light">{{ __('sign_in') }}</a>
                                    </li>
                                    <li class="d-none d-sm-block">
                                        <a href="{{ route('company.job.create') }}"
                                            class="btn btn-light">{{ __('post_job') }}
                                        </a>
                                    </li>
                                </ul>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rt-mobile-menu-overlay"></div>
        <div class="sidebar-overlay"></div>
    </div>
</header>

@push('script')
    <script>
        function changeSearchSelection() {
            var job_search_url = "{{ route('website.job') }}";
            var candidate_search_url = "{{ route('website.candidate') }}";
            var company_search_url = "{{ route('website.company') }}";
            var search_selection = $("#headerSearch").val();

            if (search_selection == 'job') {
                $(".header-search-form").attr('action', job_search_url);
            } else if (search_selection == 'candidate') {
                $(".header-search-form").attr('action', candidate_search_url);
            } else if (search_selection == 'company') {
                $(".header-search-form").attr('action', company_search_url);
            }
        }
    </script>
@endpush
