<div class="col-lg-3">
    <div class="d-sidebar">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
            <h3 class="tw-mb-3">@lang('employer_dashboard')</h3>
            <span class="close-sidebar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_2321_30321)">
                        <path d="M19.0713 4.92896L4.92915 19.0711" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.92871 4.92896L19.0708 19.0711" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                        <clipPath id="clip0_2321_30321">
                            <rect width="24" height="24" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
            </span>
        </div>
        <ul class="sidebar-menu menu-active-classes">
            <li>
                <a href="{{ route('company.dashboard') }}" class="{{ linkActive('company.dashboard') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-stack"></i>
                        </span>
                        <span class="button-text">
                            {{ __('overview') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('website.employe.details', auth()->user()->username) }}"
                    class="{{ linkActive('company.verify.documents.index') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-user-circle"></i>
                        </span>
                        <span class="button-text">
                            {{ __('my_profile') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.myjob') }}" class="{{ linkActive('company.myjob') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-suitcase-simple"></i>
                        </span>
                        <span class="button-text">
                            {{ __('my_jobs') }}
                        </span>
                    </span>
                </a>
            </li>
            @if (!$setting->edited_job_auto_approved)
                <li>
                    <a href="{{ route('company.pending.edited.jobs') }}"
                        class="{{ linkActive('company.pending.edited.jobs') }}">
                        <span class="button-content-wrapper tw-items-center">
                            <span class="button-icon align-icon-left tw-flex tw-items-center">
                                <i class="ph-hourglass"></i>
                            </span>
                            <span class="button-text">
                                {{ __('pending_edited_jobs') }}
                            </span>
                        </span>
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('company.job.create') }}" class="{{ linkActive('company.job.create') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-plus-circle"></i>
                        </span>
                        <span class="button-text">
                            {{ __('post_a_job') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.bookmark') }}"
                    class="{{ request()->routeIs('company.bookmark') || request()->routeIs('company.bookmark.category.index') ? 'active' : '' }} ">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-bookmark-simple"></i>
                        </span>
                        <span class="button-text">
                            {{ __('saved_candidate') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.questions.manage') }}"
                    class="{{ linkActive('company.questions.manage') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-question"></i>
                        </span>
                        <span class="button-text">
                            {{ __('custom_questions') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.plan') }}" class="{{ linkActive('company.plan') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-notebook"></i>
                        </span>
                        <span class="button-text">
                            {{ __('plans_billing') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.messages') }}" class="{{ linkActive('company.messages') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-chat"></i>
                        </span>
                        <span class="button-text">
                            {{ __('Message') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.verify.documents.index') }}" class="{{ linkActive('company.verify.documents.index') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <x-svg.user-check-icon width="24" height="24" stroke="currentColor" />
                        </span>
                        <span class="button-text">
                            {{ __('verify_account') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('company.setting') }}" class="{{ linkActive('company.setting') }}">
                    <span class="button-content-wrapper tw-items-center">
                        <span class="button-icon align-icon-left tw-flex tw-items-center">
                            <i class="ph-gear"></i>
                        </span>
                        <span class="button-text">
                            {{ __('settings') }}
                        </span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    <span class="button-content-wrapper ">
                        <span class="button-icon align-icon-left">
                            <i class="ph-sign-out"></i>
                        </span>
                        <span class="button-text">
                            {{ __('log_out') }}
                        </span>
                    </span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .custom-dropdown-toggle::after {
        border: none;
        content: "";
        font-family: "flaticon";
        font-size: 14px;
        vertical-align: middle;
        margin-left: auto;
    }

    .active.custom-dropdown-toggle::after {
        transform: rotate(180deg) !important;
    }
</style>
