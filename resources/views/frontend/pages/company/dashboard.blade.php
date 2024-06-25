@extends('frontend.layouts.app')

@section('title', __('dashboard'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="dashboard-right-header">
                            <div class="left-text">
                                <h5>{{ __('hello') }}, {{ ucfirst(auth()->user()->name) }}</h5>
                                <p class="m-0">{{ __('here_is_your_daily_activities_career_opportunities') }}
                                </p>
                            </div>
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold"
                                            class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $openJobCount }}</h6>
                                        <p>{{ __('open_job') }}</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-suitcase-simple"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold"
                                            class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $savedCandidates }}
                                        </h6>
                                        <p>{{ __('saved_candidate') }}</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-identification-card"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="single-feature-box bg-danger-50">
                                    <div class="single-feature-data">
                                        <h6 class="tw-text-[#18191C] tw-text-2xl tw-font-semibold"
                                            class="tw-text-[#18191C] tw-text-2xl tw-font-semibold">{{ $pendingJobCount }}
                                        </h6>
                                        <p>{{ __('pending_jobs') }}</p>
                                    </div>
                                    <div class="single-feature-icon">
                                        <i class="ph-suitcase-simple text-danger-500"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="tw-p-6 tw-rounded-lg ll-gray-border">
                                    <div class="row tw-space-y-3">
                                        <h5 class="tw-text-base tw-font-medium">Pricing Plan - Feature Remaining
                                        </h5>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="">
                                                <div
                                                    class="tw-text-[#18191C] tw-text-lg tw-flex tw-gap-2 tw-items-center tw-font-semibold">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.875 7.875L5.625 16.125L1.5 12.0002" stroke="#E05151"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M22.4998 7.875L14.2498 16.125L12.0586 13.9339"
                                                            stroke="#E05151" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>

                                                    <span>{{ $userplan->job_limit }}</span>
                                                </div>
                                                <p class="tw-text-sm tw-text-[#474C54] tw-mt-1 tw-mb-0">
                                                    {{ __('active_jobs') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="">
                                                <div
                                                    class="tw-text-[#18191C] tw-text-lg tw-flex tw-gap-2 tw-items-center tw-font-semibold">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12.9999 2L4.09332 12.6879C3.74451 13.1064 3.57011 13.3157 3.56744 13.4925C3.56512 13.6461 3.63359 13.7923 3.75312 13.8889C3.89061 14 4.16304 14 4.7079 14H11.9999L10.9999 22L19.9064 11.3121C20.2552 10.8936 20.4296 10.6843 20.4323 10.5075C20.4346 10.3539 20.3661 10.2077 20.2466 10.1111C20.1091 10 19.8367 10 19.2918 10H11.9999L12.9999 2Z"
                                                            stroke="#E05151" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $userplan->highlight_job_limit }}</span>
                                                </div>
                                                <p class="tw-text-sm tw-text-[#474C54] tw-mt-1 tw-mb-0">
                                                    {{ __('highlight_jobs') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="">
                                                <div
                                                    class="tw-text-[#18191C] tw-text-lg tw-flex tw-gap-2 tw-items-center tw-font-semibold">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M16 12L12 8M12 8L8 12M12 8V16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
                                                            stroke="#0A65CC" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $userplan->featured_job_limit }}</span>
                                                </div>
                                                <p class="tw-text-sm tw-text-[#474C54] tw-mt-1 tw-mb-0">
                                                    {{ __('featured_jobs') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="">
                                                <div
                                                    class="tw-text-[#18191C] tw-text-lg tw-flex tw-gap-2 tw-items-center tw-font-semibold">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M16 3.46776C17.4817 4.20411 18.5 5.73314 18.5 7.5C18.5 9.26686 17.4817 10.7959 16 11.5322M18 16.7664C19.5115 17.4503 20.8725 18.565 22 20M2 20C3.94649 17.5226 6.58918 16 9.5 16C12.4108 16 15.0535 17.5226 17 20M14 7.5C14 9.98528 11.9853 12 9.5 12C7.01472 12 5 9.98528 5 7.5C5 5.01472 7.01472 3 9.5 3C11.9853 3 14 5.01472 14 7.5Z"
                                                            stroke="#0A65CC" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $userplan->candidate_cv_view_limitation == 'limited' ? $userplan->candidate_cv_view_limit : __('unlimited') }}</span>
                                                </div>
                                                <p class="tw-text-sm tw-text-[#474C54] tw-mt-1 tw-mb-0">
                                                    {{ __('profile_view') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="tw-mt-5">
                                            <a href="{{ route('website.plan') }}"
                                                class="tw-text-base tw-capitalize tw-font-semibold">Upgrade Plan</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="recently-applied-wrap tw-mt-3 d-flex justify-content-between align-items-center rt-mb-15">
                            <h3 class="f-size-16">{{ __('recent_jobs') }}</h3>
                            <a class="view-all text-gray-500 f-size-16 d-flex align-items-center"
                                href="{{ route('company.myjob') }}">
                                {{ __('view_all') }}
                                <i class="ph-arrow-right f-size-20 rt-ml-8"></i>
                            </a>
                        </div>
                        <div class="db-job-card-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ __('job') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('applications') }}</th>
                                        <th>{{ __('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($recentJobs->count() > 0)
                                        @foreach ($recentJobs as $job)
                                            <tr>
                                                <td>
                                                    <div class="iconbox-content">
                                                        <div class="post-info2">
                                                            <div class="post-main-title">
                                                                <a href="{{ route('website.job.details', $job->slug) }}"
                                                                    class="text-gray-900 f-size-16  ft-wt-5">
                                                                    {{ Str::limit($job->title, 40, '...') }}
                                                                </a>
                                                            </div>
                                                            <div class="body-font-4 text-gray-600 pt-2">
                                                                <span class="info-tools rt-mr-8">
                                                                    {{ $job->job_type ? $job->job_type->name : '' }}
                                                                </span>
                                                                <span class="info-tools">
                                                                    {{ $job->days_remaining }}
                                                                    {{ __('remaining') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($job->status == 'active')
                                                        <div class="text-success-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-check-circle f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('active') }}
                                                        </div>
                                                    @elseif ($job->status == 'pending')
                                                        <div class="text-primary-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-hourglass f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('pending') }}
                                                        </div>
                                                    @else
                                                        <div class="text-danger-500 ft-wt-5 d-flex align-items-center">
                                                            <i class="ph-x-circle f-size-18 mt-1 rt-mr-4"></i>
                                                            {{ __('job_expire') }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ph-users f-size-20 rt-mr-4"></i>
                                                        {{ $job->applied_jobs_count }} {{ __('applications') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="db-job-btn-wrap d-flex justify-content-end">
                                                        <a href="{{ route('company.job.application', ['job' => $job->id]) }}"
                                                            class="btn bg-gray-50 text-primary-500 rt-mr-8">
                                                            <span class="button-text">
                                                                {{ __('view_applications') }}
                                                            </span>
                                                        </a>
                                                        <button type="button" class="btn btn-icon"
                                                            id="dropdownMenuButton5" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M12 13.125C12.6213 13.125 13.125 12.6213 13.125 12C13.125 11.3787 12.6213 10.875 12 10.875C11.3787 10.875 10.875 11.3787 10.875 12C10.875 12.6213 11.3787 13.125 12 13.125Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 6.65039C12.6213 6.65039 13.125 6.14671 13.125 5.52539C13.125 4.90407 12.6213 4.40039 12 4.40039C11.3787 4.40039 10.875 4.90407 10.875 5.52539C10.875 6.14671 11.3787 6.65039 12 6.65039Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 19.6094C12.6213 19.6094 13.125 19.1057 13.125 18.4844C13.125 17.8631 12.6213 17.3594 12 17.3594C11.3787 17.3594 10.875 17.8631 10.875 18.4844C10.875 19.1057 11.3787 19.6094 12 19.6094Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end company-dashboard-dropdown"
                                                            aria-labelledby="dropdownMenuButton5">
                                                            <li>
                                                                <a href="{{ route('company.promote', $job->slug) }}"
                                                                    class="dropdown-item">
                                                                    <svg width="20" height="20"
                                                                        viewBox="0 0 20 20" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                                                                            stroke="#0A65CC" stroke-width="1.5"
                                                                            stroke-miterlimit="10" />
                                                                        <path d="M6.875 10H13.125" stroke="#0A65CC"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path d="M10 6.875V13.125" stroke="#0A65CC"
                                                                            stroke-width="1.5" stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>

                                                                    {{ __('Promote Job') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('website.job.details', $job->slug) }}">
                                                                    <svg width="20" height="20"
                                                                        viewBox="0 0 20 20" fill="none"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M10 3.54102C3.75 3.54102 1.25 9.99996 1.25 9.99996C1.25 9.99996 3.75 16.4577 10 16.4577C16.25 16.4577 18.75 9.99996 18.75 9.99996C18.75 9.99996 16.25 3.54102 10 3.54102Z"
                                                                            stroke="var(--primary-500)" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                        <path
                                                                            d="M10 13.125C11.7259 13.125 13.125 11.7259 13.125 10C13.125 8.27411 11.7259 6.875 10 6.875C8.27411 6.875 6.875 8.27411 6.875 10C6.875 11.7259 8.27411 13.125 10 13.125Z"
                                                                            stroke="var(--primary-500)" stroke-width="1.5"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round" />
                                                                    </svg>
                                                                    {{ __('view_details') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('company.job.make.expire', $job->id) }}">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <svg width="20" height="20"
                                                                            viewBox="0 0 20 20" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                                                                                stroke="#5E6670" stroke-width="1.5"
                                                                                stroke-miterlimit="10"></path>
                                                                            <path d="M12.5 7.5L7.5 12.5" stroke="#5E6670"
                                                                                stroke-width="1.5" stroke-linecap="round"
                                                                                stroke-linejoin="round"></path>
                                                                            <path d="M12.5 12.5L7.5 7.5" stroke="#5E6670"
                                                                                stroke-width="1.5" stroke-linecap="round"
                                                                                stroke-linejoin="round"></path>
                                                                        </svg>
                                                                        {{ __('make_it_expire') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <x-svg.not-found-icon />
                                                <p class="mt-4">{{ __('no_data_found') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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
