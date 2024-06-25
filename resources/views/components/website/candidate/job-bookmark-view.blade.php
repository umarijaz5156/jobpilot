@if ($jobs->count() > 0)
    @foreach ($jobs as $job)
        <div class="card jobcardStyle1 rt-mb-24">
            <div class="card-body">
                <div class="rt-single-icon-box ">
                    <div class="icon-thumb">
                        <img src="{{ asset($job->company->logo_url) }}" alt="logo" draggable="false" class="object-fit-contain">
                    </div>
                    <div class="iconbox-content !tw-m-0">
                        <div class="post-info2">
                            <div class="post-main-title">
                                <a href="{{ route('website.job.details', $job->slug) }}">{{ $job->title }}</a>
                                <span class="badge rounded-pill bg-primary-50 text-primary-500">
                                    {{ $job->job_type ? $job->job_type->name : '' }}
                                </span>
                            </div>
                            <div class="body-font-4 text-gray-600 pt-2">
                                <span class="info-tools">
                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M19.25 9.16602C19.25 15.5827 11 21.0827 11 21.0827C11 21.0827 2.75 15.5827 2.75 9.16602C2.75 6.97798 3.61919 4.87956 5.16637 3.33238C6.71354 1.78521 8.81196 0.916016 11 0.916016C13.188 0.916016 15.2865 1.78521 16.8336 3.33238C18.3808 4.87956 19.25 6.97798 19.25 9.16602Z"
                                            stroke="#C5C9D6" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M11 11.916C12.5188 11.916 13.75 10.6848 13.75 9.16602C13.75 7.64723 12.5188 6.41602 11 6.41602C9.48122 6.41602 8.25 7.64723 8.25 9.16602C8.25 10.6848 9.48122 11.916 11 11.916Z"
                                            stroke="#C5C9D6" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    {{ $job->country }}
                                </span>
                                <span class="info-tools">
                                    <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.8125 4.5625C11.8125 4.11108 11.7236 3.66408 11.5508 3.24703C11.3781 2.82997 11.1249 2.45102 10.8057 2.13182C10.4865 1.81262 10.1075 1.55941 9.69047 1.38666C9.27342 1.21391 8.82642 1.125 8.375 1.125H5.28125C4.36957 1.125 3.49523 1.48716 2.85057 2.13182C2.20591 2.77648 1.84375 3.65082 1.84375 4.5625C1.84375 5.47418 2.20591 6.34852 2.85057 6.99318C3.49523 7.63784 4.36957 8 5.28125 8H9.0625C9.97418 8 10.8485 8.36216 11.4932 9.00682C12.1378 9.65148 12.5 10.5258 12.5 11.4375C12.5 12.3492 12.1378 13.2235 11.4932 13.8682C10.8485 14.5128 9.97418 14.875 9.0625 14.875H4.9375C4.02582 14.875 3.15148 14.5128 2.50682 13.8682C1.86216 13.2235 1.5 12.3492 1.5 11.4375"
                                            stroke="#C5C9D6" stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round">
                                        </path>
                                    </svg>
                                    @if ($job->salary_mode == 'range')
                                    {{ currencyAmountShort($job->min_salary) }} - {{ currencyAmountShort($job->max_salary) }} {{ currentCurrencyCode() }}
                                    @else
                                    {{ $job->custom_salary }}
                                    @endif
                                </span>
                                <span class="info-tools">
                                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.875 3.4375H4.125C3.7453 3.4375 3.4375 3.7453 3.4375 4.125V17.875C3.4375 18.2547 3.7453 18.5625 4.125 18.5625H17.875C18.2547 18.5625 18.5625 18.2547 18.5625 17.875V4.125C18.5625 3.7453 18.2547 3.4375 17.875 3.4375Z"
                                            stroke="#C5C9D6" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M15.125 2.0625V4.8125" stroke="#C5C9D6" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M6.875 2.0625V4.8125" stroke="#C5C9D6" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M3.4375 7.5625H18.5625" stroke="#C5C9D6" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    {{ $job->days_remaining }} {{ __('remaining') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="iconbox-extra align-self-center">
                        <div>
                            <a href="{{ route('website.job.bookmark', $job->slug) }}"
                                class="text-primary-500 hoverbg-primary-50 plain-button icon-button">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z"
                                        fill="var(--primary-500)" stroke="var(--primary-500)"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                        @if ($job->can_apply)
                            <div>
                                @if (!$job->applied)
                                    <a href="{{ route('website.job.apply', $job->slug) }}"
                                        class="btn btn-primary2-50">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right"><i
                                                    class="ph-arrow-right"></i></span>
                                            <span class="button-text">
                                                {{ __('apply_now') }}
                                            </span>

                                        </span>
                                    </a>
                                @else
                                    <button type="button" class="btn btn-success">
                                        <span class="button-content-wrapper ">
                                            <span class="button-text">
                                                {{ __('already_applied') }}
                                            </span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        @else
                            @if ($job->apply_on == 'custom_url')
                                <a href="{{ $job->apply_url }}" target="_blank" class="btn btn-primary2-50">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right"><i
                                                class="ph-arrow-right"></i></span>
                                        <span class="button-text">
                                            {{ __('apply_now') }}
                                        </span>

                                    </span>
                                </a>
                            @else
                                <a href="mailto:{{ $job->apply_email }}" class="btn btn-primary2-50">
                                    <span class="button-content-wrapper ">
                                        <span class="button-icon align-icon-right"><i
                                                class="ph-arrow-right"></i></span>
                                        <span class="button-text">
                                            {{ __('apply_now') }}
                                        </span>

                                    </span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <x-not-found message="no_data_found" />
@endif
<div class="rt-spacer-50 rt-spacer-md-20"></div>
<nav>
    {{ $jobs->links('vendor.pagination.frontend') }}
</nav>
