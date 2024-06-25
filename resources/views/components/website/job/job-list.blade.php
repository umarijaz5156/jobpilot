<li class="d-block fade-in-bottom  rt-mb-24">
    <div class="card iconxl-size jobcardStyle1 flex-wrap {{ $job->highlight ? 'gradient-bg' : '' }}">
        <div class="card-body">
            <div class="rt-single-icon-box icb-clmn-lg ">
                <a href="{{ route('website.job.details', $job->slug) }}" class="icon-thumb">
                    @if ($job->company)
                    <img class="tw-rounded-lg tw-w-[56px] tw-h-[56px]" src="{{ $job->company->logo_url }}" alt="logo"
                        draggable="false">
                    @else
                    <svg style="width: 50px ; height: 50px ; color: black" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                    </svg>
                    @endif
                </a>
                <a href="{{ route('website.job.details', $job->slug) }}" class="iconbox-content">
                    <div class="post-info2">

                        <div class="post-main-title">
                            {{ $job->title }}
                            <span class="badge rounded-pill bg-primary-50 text-primary-500">
                                {{ $job->job_type ? $job->job_type->name : '' }}
                            </span>
                        </div>
                        <div class="body-font-4 text-gray-600 pt-2">
                            <span class="info-tools">
                                <x-svg.location-icon stroke="#C5C9D6" />

                                {{ $job->country }}
                            </span>
                            <span class="info-tools">
                                <x-svg.doller-icon />

                                @if ($job->salary_mode == 'range')
                                {{ currencyAmountShort($job->min_salary) }} - {{ currencyAmountShort($job->max_salary)
                                }} {{ currentCurrencyCode() }}
                                @else
                                {{ $job->custom_salary }}
                                @endif
                            </span>
                            <span class="info-tools">
                                <x-svg.calender-icon stroke="#C5C9D6" />

                                @if ($job->deadline_active)
                                <span>{{ $job->days_remaining }} {{ __('remaining') }}</span>
                                @else
                                <span class="text-danger">{{ __('expired') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
                <div class="iconbox-extra align-self-center">
                    <div>
                        @auth
                        @if (auth()->user()->role == 'candidate')
                        <a href="{{ route('website.job.bookmark', $job->slug) }}" title="Bookmark"
                            class="text-primary-500 hoverbg-primary-50 plain-button icon-button">
                            @if ($job->bookmarked)
                            <x-svg.bookmark-icon />
                            @else
                            <x-svg.unmark-icon />
                            @endif
                        </a>
                        @else
                        <button title="Bookmark" aria-label="Bookmark" type="button"
                            class="text-primary-500 hoverbg-primary-50 plain-button icon-button no_permission">
                            <x-svg.unmark-icon />
                        </button>
                        @endif
                        @else
                        <button title="Bookmark" aria-label="Bookmark" type="button"
                            class="text-primary-500 hoverbg-primary-50 plain-button icon-button login_required">
                            <x-svg.unmark-icon />
                        </button>
                        @endauth
                    </div>
                    @if ($job->can_apply)
                    <div>
                        @if ($job->deadline_active)
                        @auth
                        @if (auth()->user()->role == 'candidate')
                        @if (!$job->applied)
                        <button type="button" onclick="applyJobb({{ $job->id }}, '{{ $job->title }}')"
                            class="btn btn-primary2-50" title="{{ __('apply_now') }}">
                            <span class="button-content-wrapper">
                                <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                                <span class="button-text">{{ __('apply_now') }}</span>
                            </span>
                        </button>
                        @else
                        <button type="button" class="btn btn-success" title="{{ __('already_applied') }}">
                            <span class="button-content-wrapper ">
                                <span class="button-text">
                                    {{ __('already_applied') }}
                                </span>
                            </span>
                        </button>
                        @endif
                        @else
                        <button type="button" class="btn btn-primary2-50 no_permission" title="{{ __('apply_now') }}">
                            <span class="button-content-wrapper ">
                                <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                                <span class="button-text">{{ __('apply_now') }}</span>
                            </span>
                        </button>
                        @endif
                        @else
                        <button title="{{ __('apply_now') }}" type="button" class="btn btn-primary2-50 login_required">
                            <span class="button-content-wrapper ">
                                <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                                <span class="button-text">{{ __('apply_now') }}</span>
                            </span>
                        </button>
                        @endauth
                        @endif
                    </div>
                    @else
                    <a href="{{ $job->apply_on == 'custom_url' ? $job->apply_url : 'mailto:' .  $job->apply_email }}"
                        title="{{ __('apply_now') }}" target="_blank" class="btn btn-primary2-50">
                        <span class="button-content-wrapper ">
                            <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                            <span class="button-text">{{ __('apply_now') }}</span>
                        </span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>