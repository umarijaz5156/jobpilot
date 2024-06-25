@props(['featured' => true])

@if (!$job->job_provider)
    <div class="tw-relative tw-h-full">
        <a href="{{ route('website.job.details', $job->slug) }}"
            class="tw-h-full card tw-card tw-block jobcardStyle1 {{ $job->highlight ? 'gradient-bg' : '' }}">
            <div class="tw-p-6 tw-h-full">
                <div class="tw-mb-5">
                    <div class="tw-mb-1.5">
                        <span class="tw-text-[#18191C] tw-text-lg tw-font-medium">
                            {{ $job->title }}
                        </span>
                    </div>
                    <div class="tw-flex tw-flex-wrap tw-gap-2 tw-items-center tw-mb-1.5">
                        @if ($job->featured && $featured)
                            <span
                                class="tw-text-[#06C] tw-text-[12px] tw-leading-[12px] tw-font-semibold tw-bg-[#E6F0FA] tw-px-2 tw-py-1 tw-rounded-[3px]">{{ __('featured') }}</span>
                        @endif
                        <span
                            class="tw-text-[#0BA02C] tw-text-[12px] tw-leading-[12px] tw-font-semibold tw-bg-[#E7F6EA] tw-px-2 tw-py-1 tw-rounded-[3px]">
                            {{ $job->job_type ? $job->job_type->name : '' }}
                        </span>
                    </div>
                    <div>
                        <span class="tw-text-sm tw-text-[#767F8C]">
                            {{ __('salary') }}:
                            @if ($job->salary_mode == 'range')
                                {{ currencyAmountShort($job->min_salary) }} -
                                {{ currencyAmountShort($job->max_salary) }} {{ currentCurrencyCode() }}
                            @else
                                {{ $job->custom_salary }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="rt-single-icon-box tw-flex-wrap tw-gap-4">
                    <span>
                        <div class="tw-w-[56px] tw-h-[56px]">
                            @if ($job->company)
                                <img class="tw-rounded-lg tw-w-[56px] tw-h-[56px]" src="{{ $job->company->logo_url }}"
                                    alt="logo" draggable="false">
                            @else
                                <svg style="width: 50px ; height: 50px ; color: black"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                </svg>
                            @endif

                        </div>
                    </span>
                    <div class="iconbox-content">
                        <div class="tw-mb-1 tw-inline-flex">
                            @if ($job->company)
                                <span
                                    class="tw-text-base tw-font-medium tw-text-[#18191C] tw-card-title">{{ $job->company->user->name }}</span>
                            @else
                                <span class="tw-text-base tw-font-medium tw-text-[#18191C] tw-card-title">
                                    {{ $job->company_name }}</span>
                            @endif
                        </div>
                        <span class="tw-flex tw-items-center tw-gap-1">
                            <i class="ph-map-pin"></i>
                            <span class="tw-location">{{ $job->country }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </a>
        <div class="tw-absolute tw-bottom-6 !tw-right-6">
            <div class="text-primary-500 hoverbg-primary-50 plain-button icon-button">
                @auth
                    @if (auth()->user()->role == 'candidate')
                        <a href="{{ route('website.job.bookmark', $job->slug) }}" class="tw-text-[#C8CCD1]">
                            @if ($job->bookmarked)
                                <x-svg.bookmark-icon width="24" height="24" fill="#0A65CC" stroke="#0A65CC" />
                            @else
                                <x-svg.unmark-icon />
                            @endif
                        </a>
                    @else
                        <button type="button"
                            class="tw-text-[#C8CCD1] hoverbg-primary-50 plain-button icon-button no_permission">
                            <x-svg.unmark-icon />
                        </button>
                    @endif
                @else
                    <button type="button"
                        class="tw-text-[#C8CCD1] hover:tw-text-[#0A65CC] hoverbg-primary-50 plain-button icon-button login_required">
                        <x-svg.unmark-icon />
                    </button>
                @endauth
            </div>
        </div>
    </div>
@endif

{{-- Careerjet Jobs --}}
@if ($job->job_provider && $job->job_provider == 'careerjet')
    <div class="tw-h-full">
        <a href="{{ $job->url ?? '#' }}" target="_blank" class="card tw-card extra-jobs jobcardStyle1">
            <div class="tw-p-6">
                <div class="tw-mb-5">
                    <div class="tw-mb-1.5">
                        <span class="tw-text-[#18191C] tw-text-lg tw-font-medium">
                            {{ $job->title ? \Illuminate\Support\Str::limit($job->title, 30) : 'No Title' }}
                        </span>
                    </div>
                    @if ($job->salary)
                        <div class="tw-flex tw-gap-2 tw-items-center">
                            <span class="tw-text-sm tw-text-[#767F8C]">
                                {{ __('salary') }}: {{ $job->salary }}
                            </span>
                        </div>
                        @else
                        <div class="tw-flex tw-gap-2 tw-items-center">
                            <span class="tw-text-sm tw-text-[#767F8C]">
                                {{ __('salary') }}: {{__('negotiable')}}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="rt-single-icon-box tw-flex-wrap tw-gap-4">
                    <div class="iconbox-content">
                        <div class="tw-mb-1 tw-inline-flex">
                            <span class="tw-text-base tw-font-medium tw-text-[#18191C] tw-card-title">
                                {{ $job->company ?? 'No Company' }}
                            </span>
                        </div>
                        <span class="tw-flex tw-items-center tw-gap-1">
                            <i class="ph-map-pin"></i>
                            <span class="tw-location">
                                {{ $job->locations ?? 'No Location' }}
                            </span>
                            <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </span>
                        </span>
                    </div>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#767F8C"
                            viewBox="0 0 256 256">
                            <path
                                d="M224,104a8,8,0,0,1-16,0V59.32l-66.33,66.34a8,8,0,0,1-11.32-11.32L196.68,48H152a8,8,0,0,1,0-16h64a8,8,0,0,1,8,8Zm-40,24a8,8,0,0,0-8,8v72H48V80h72a8,8,0,0,0,0-16H48A16,16,0,0,0,32,80V208a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V136A8,8,0,0,0,184,128Z">
                            </path>
                        </svg>
                    </span>
                </div>
            </div>
        </a>
    </div>
@endif

{{-- Indeed Jobs --}}
@if ($job->job_provider && $job->job_provider == 'indeed')
    <div class="tw-h-full">
        <a href="{{ $job->url ?? '#' }}" target="_blank" class="card tw-card extra-jobs jobcardStyle1">
            <div class="tw-p-6">
                <div class="tw-mb-5">
                    <div class="tw-mb-1.5">
                        <span class="tw-text-[#18191C] tw-text-lg tw-font-medium">
                            {{ $job->jobtitle ?? 'No Title' }}
                        </span>
                    </div>
                </div>
                <div class="rt-single-icon-box tw-flex-wrap tw-gap-4">
                    <div class="iconbox-content">
                        <div class="tw-mb-1 tw-inline-flex">
                            <span class="tw-text-base tw-font-medium tw-text-[#18191C] tw-card-title">
                                {{ $job->company ?? 'No Company' }}
                            </span>
                        </div>
                        <span class="tw-flex tw-items-center tw-gap-1">
                            <i class="ph-map-pin"></i>
                            <span class="tw-location">
                                {{ $job->formattedLocationFull ?? 'No Location' }}
                            </span>
                        </span>
                    </div>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#767F8C"
                            viewBox="0 0 256 256">
                            <path
                                d="M224,104a8,8,0,0,1-16,0V59.32l-66.33,66.34a8,8,0,0,1-11.32-11.32L196.68,48H152a8,8,0,0,1,0-16h64a8,8,0,0,1,8,8Zm-40,24a8,8,0,0,0-8,8v72H48V80h72a8,8,0,0,0,0-16H48A16,16,0,0,0,32,80V208a16,16,0,0,0,16,16H176a16,16,0,0,0,16-16V136A8,8,0,0,0,184,128Z">
                            </path>
                        </svg>
                    </span>
                </div>
            </div>
        </a>
    </div>
@endif
