@extends('frontend.layouts.app')

@section('title')
    {{ __('applied_jobs') }}
@endsection

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.candidate.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="dashboard-right-header rt-mb-32 tw-mt-4 lg:tw-mt-0">
                            <div class="left-text m-0">
                                <h3 class="f-size-18 lh-1 m-0">
                                    {{ __('applied_jobs') }}
                                    <span class="text-gray-400">({{ $appliedJobs->total() }})</span>
                                </h3>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <div>
                            <div class="accordion ll-accordion" id="accordionExample">
                                {{-- <table>
                                    <thead>
                                        <tr>
                                            <th>{{ __('job') }}</th>
                                            <th>{{ __('date_applied') }}</th>
                                            <th>{{ __('status') }}</th>
                                            <th>{{ __('action') }}</th>
                                        </tr>
                                    </thead>
                                </table> --}}
                                <div>
                                    @if ($appliedJobs->count() > 0)
                                        @foreach ($appliedJobs as $job)
                                            <div class="accordion-item tw-mt-5">
                                                <h2 class="accordion-header" id="heading{{ $job->id }}">
                                                    <div class="accordion-button tw-flex tw-gap-2 tw-flex-wrap tw-justify-between tw-p-5">
                                                        <div class="lg:tw-w-[45%] tw-w-full">
                                                            <div class="rt-single-icon-box tw-flex-col lg:tw-flex-row tw-items-start lg:tw-items-center tw-gap-5">
                                                                <div class="tw-w-[56px] tw-h-[56px]">
                                                                    <img class="tw-w-[56px] tw-h-[56px] tw-rounded-md"
                                                                        src="{{ asset($job->company->logo_url) }}"
                                                                        alt="logo" draggable="false">
                                                                </div>
                                                                <div class="iconbox-content">
                                                                    <div class="tw-flex tw-flex-col tw-gap-3">
                                                                        <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-2 tw-items-start lg:tw-items-center">
                                                                            <a class="tw-text-[#18191C] tw-text-base tw-font-medium"
                                                                                href="{{ route('website.job.details', $job->slug) }}">
                                                                                {{ $job->company->user->name }}
                                                                            </a>
                                                                            <span
                                                                                class="tw-py-1 tw-inline-flex tw-px-3 tw-text-sm rounded-pill bg-primary-50 text-primary-500">
                                                                                {{ $job->job_type ? $job->job_type->name : '' }}
                                                                            </span>

                                                                        </div>
                                                                        <div class="tw-text-sm tw-text-[#5E6670] tw-flex tw-flex-col sm:tw-flex-row tw-gap-4 sm:tw-items-center text-gray-600">
                                                                            <span
                                                                                class="info-tools tw-flex tw-items-center tw-gap-1.5">
                                                                                <svg width="18" height="18"
                                                                                    viewBox="0 0 18 18" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path
                                                                                        d="M15.75 7.5C15.75 12.75 9 17.25 9 17.25C9 17.25 2.25 12.75 2.25 7.5C2.25 5.70979 2.96116 3.9929 4.22703 2.72703C5.4929 1.46116 7.20979 0.75 9 0.75C10.7902 0.75 12.5071 1.46116 13.773 2.72703C15.0388 3.9929 15.75 5.70979 15.75 7.5Z"
                                                                                        stroke="#939AAD" stroke-width="1.5"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                    </path>
                                                                                    <path
                                                                                        d="M9 9.75C10.2426 9.75 11.25 8.74264 11.25 7.5C11.25 6.25736 10.2426 5.25 9 5.25C7.75736 5.25 6.75 6.25736 6.75 7.5C6.75 8.74264 7.75736 9.75 9 9.75Z"
                                                                                        stroke="#939AAD" stroke-width="1.5"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round">
                                                                                    </path>
                                                                                </svg>
                                                                                {{ $job->country }}
                                                                            </span>
                                                                            <span
                                                                                class="info-tools tw-flex tw-items-center tw-gap-1.5">
                                                                                <svg width="22" height="22"
                                                                                    viewBox="0 0 22 22" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M11 2.0625V19.9375"
                                                                                        stroke="#C5C9D6" stroke-width="1.5"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path
                                                                                        d="M15.8125 7.5625C15.8125 7.11108 15.7236 6.66408 15.5508 6.24703C15.3781 5.82997 15.1249 5.45102 14.8057 5.13182C14.4865 4.81262 14.1075 4.55941 13.6905 4.38666C13.2734 4.21391 12.8264 4.125 12.375 4.125H9.28125C8.36957 4.125 7.49523 4.48716 6.85057 5.13182C6.20591 5.77648 5.84375 6.65082 5.84375 7.5625C5.84375 8.47418 6.20591 9.34852 6.85057 9.99318C7.49523 10.6378 8.36957 11 9.28125 11H13.0625C13.9742 11 14.8485 11.3622 15.4932 12.0068C16.1378 12.6515 16.5 13.5258 16.5 14.4375C16.5 15.3492 16.1378 16.2235 15.4932 16.8682C14.8485 17.5128 13.9742 17.875 13.0625 17.875H8.9375C8.02582 17.875 7.15148 17.5128 6.50682 16.8682C5.86216 16.2235 5.5 15.3492 5.5 14.4375"
                                                                                        stroke="#C5C9D6" stroke-width="1.5"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                </svg>
                                                                                @if ($job->salary_mode == 'range')
                                                                                    {{ currencyAmountShort($job->min_salary) }}
                                                                                    -
                                                                                    {{ currencyAmountShort($job->max_salary) }}
                                                                                    {{ currentCurrencyCode() }}
                                                                                @else
                                                                                    {{ $job->custom_salary }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tw-whitespace-nowrap tw-text-sm tw-text-[#5E6670]">
                                                            {{ date('M d, Y s:m', strtotime($job->pivot->created_at)) }}
                                                        </div>
                                                        <div
                                                            class="text-{{ $job->deadline_active ? 'success' : 'danger' }}-500">
                                                            @if ($job->deadline_active)
                                                                <div class="tw-flex tw-gap-1.5 tw-text-sm tw-items-center">
                                                                    <x-svg.active-check-icon />
                                                                    {{ __('active') }}
                                                                </div>
                                                            @else
                                                                <div class="tw-flex tw-gap-1.5 tw-items-center">
                                                                    <x-svg.expire-cross-icon />
                                                                    {{ __('expired') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <a target="_blank" href="{{ route('website.job.details', $job->slug) }}">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M21 9L21 3M21 3H15M21 3L13 11M10 5H7.8C6.11984 5 5.27976 5 4.63803 5.32698C4.07354 5.6146 3.6146 6.07354 3.32698 6.63803C3 7.27976 3 8.11984 3 9.8V16.2C3 17.8802 3 18.7202 3.32698 19.362C3.6146 19.9265 4.07354 20.3854 4.63803 20.673C5.27976 21 6.11984 21 7.8 21H14.2C15.8802 21 16.7202 21 17.362 20.673C17.9265 20.3854 18.3854 19.9265 18.673 19.362C19 18.7202 19 17.8802 19 16.2V14"
                                                                    stroke="#14181A" stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                        <div>
                                                            <div class="db-job-btn-wrap d-flex justify-content-end">
                                                                <button type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse{{ $job->id }}"
                                                                    aria-expanded="true"
                                                                    aria-controls="collapse{{ $job->id }}"
                                                                    class="btn bg-gray-50 text-primary-500 rt-mr-8">
                                                                    <span class="button-text">
                                                                        {{ __('view_details') }}
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </h2>
                                                <div id="collapse{{ $job->id }}" class="accordion-collapse collapse"
                                                    aria-labelledby="heading{{ $job->id }}"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body tw-w-full !tw-p-6">
                                                        <div class="tw-grid lg:tw-grid-cols-3 tw-gap-6">
                                                            <div class="lg:tw-col-span-2">
                                                                <h2 class="tw-text-[#18191C] tw-font-medium tw-text-lg tw-mt-0 tw-mb-3">{{ __('cover_letter') }}</h2>
                                                                <div class="tw-flex tw-flex-col tw-text-base tw-text-[#5E6670] tw-gap-3">
                                                                    {!! $job->cover_letter !!}
                                                                </div>
                                                            </div>
                                                            <div class="lg:tw-col-span-1">
                                                                <h3 class="tw-mt-0 tw-text-sm tw-text-[#707A7D] tw-mb-2">{{ __('candidate_hiring_stage') }}:</h3>
                                                                <span class="badge tw-mb-6 rounded-pill bg-primary-50 text-primary-500">
                                                                    {{ $job->application_status }}
                                                                </span>
                                                                @if ($job->cv_file)
                                                                    <div class="tw-rounded-lg tw-p-4" style="border: 1.5px solid rgba(206, 224, 245, 0.7);">
                                                                        <p class="tw-mb-4 tw-text-base tw-text-[#18191C]">{{ __('resume') }}</p>
                                                                        <a href="{{ asset($job->cv_file) }}" download="{{ basename($job->cv_file) }}" class="tw-flex tw-justify-between tw-items-center">
                                                                            <div class="tw-flex tw-items-center tw-gap-3">
                                                                                <svg width="48" height="48"
                                                                                    viewBox="0 0 48 48" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path
                                                                                        d="M28.0002 4H12.0002C10.9394 4 9.92196 4.42143 9.17182 5.17157C8.42167 5.92172 8.00024 6.93913 8.00024 8V40C8.00024 41.0609 8.42167 42.0783 9.17182 42.8284C9.92196 43.5786 10.9394 44 12.0002 44H36.0002C37.0611 44 38.0785 43.5786 38.8287 42.8284C39.5788 42.0783 40.0002 41.0609 40.0002 40V16L28.0002 4Z"
                                                                                        stroke="#E4E5E8" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path d="M31.9998 34H15.9998"
                                                                                        stroke="#E4E5E8" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path d="M31.9998 26H15.9998"
                                                                                        stroke="#E4E5E8" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path d="M19.9998 18.002H17.9998H15.9998"
                                                                                        stroke="#E4E5E8" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path d="M27.9998 4V16H39.9998"
                                                                                        stroke="#E4E5E8" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                </svg>
                                                                                <div>
                                                                                    <h4 class="tw-text-xs tw-text-[#767F8C] tw-mt-0 tw-mb-1">{{ $job->cv_name }}</h4>
                                                                                    <p class="tw-text-sm tw-text-[#18191C] tw-mb-0">PDF</p>
                                                                                </div>
                                                                            </div>
                                                                            <span class="tw-cursor-pointer">
                                                                                <svg width="24" height="24"
                                                                                    viewBox="0 0 24 24" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <path
                                                                                        d="M8.0625 10.3145L12 14.2509L15.9375 10.3145"
                                                                                        stroke="#0066CC" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path d="M12 3.75V14.2472"
                                                                                        stroke="#0066CC" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                    <path
                                                                                        d="M20.25 14.25V19.5C20.25 19.6989 20.171 19.8897 20.0303 20.0303C19.8897 20.171 19.6989 20.25 19.5 20.25H4.5C4.30109 20.25 4.11032 20.171 3.96967 20.0303C3.82902 19.8897 3.75 19.6989 3.75 19.5V14.25"
                                                                                        stroke="#0066CC" stroke-width="2"
                                                                                        stroke-linecap="round"
                                                                                        stroke-linejoin="round" />
                                                                                </svg>
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="rt-pt-30">
                            @if ($appliedJobs->total() > $appliedJobs->count())
                                <nav>
                                    {{ $appliedJobs->links('vendor.pagination.frontend') }}
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection
