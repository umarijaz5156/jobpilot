@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('jobs');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection

@section('main')
    <form action="{{ route('website.job') }}" method="GET" id="job_search_form">
        {{-- job filtering --}}
        <x-website.job.job-filtering :countries="$countries" :categories="$categories" :job-roles="$job_roles" :min-salary="$min_salary"
            :currentCurrency="$current_currency" :max-salary="$max_salary" :experiences="$experiences" :educations="$educations" :job-types="$job_types" :total-jobs="$jobs->total()" />

        <div class="job-filter-overlay"></div>
        
        <div class="joblist-content">
            <div class="container">
                @if ($popularTags && count($popularTags))
                    <x-website.job.job-sorting :popular-tags="$popularTags" />
                @endif

                {{-- <div class="tw-flex tw-gap-2 tw-flex-wrap tw-mb-5">
                    @if (request('keyword'))
                        <span
                            class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('search') }}:
                            {{ request('keyword') }}
                            <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer" onclick="keywordClose()">
                                <x-svg.tw-close-icon />
                            </span>
                        </span>
                    @endif
                    @if (Route::current()->parameter('category'))
                        <span
                            class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('category') }}:
                            {{ $categories->where('slug', Route::current()->parameter('category'))->first()->name ?? '-' }}
                            <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer" onclick="categoryClose()">
                                <x-svg.tw-close-icon />
                            </span>
                        </span>
                    @endif
                    @if (request('job_type'))
                        <span
                            class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ request('job_type') }}<span
                                class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer" onclick="jobTypeClose()">
                                <x-svg.tw-close-icon />
                            </span>
                        </span>
                    @endif
                    @if (is_string(request('price_min')) || is_string(request('price_max')))
                        <span
                            class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('salary') }}
                            {{ request('price_min') }} - {{ request('price_max') }}
                            <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer" onclick="jobSalaryClose()">
                                <x-svg.tw-close-icon />
                            </span>
                        </span>
                    @endif
                </div> --}}
                <div class="tw-flex tw-gap-2 tw-items-center tw-py-4 tw-mb-6"
                    style="border-top: 1px solid #E4E5E8; border-bottom: 1px solid #E4E5E8;">
                  
                    @if (request('keyword') || Route::current()->parameter('category') || request('job_type') || is_string(request('price_min')) || is_string(request('price_max')) || request('is_remote') )
                        <h2 class="tw-text-sm tw-text-[#767F8C] tw-mb-0">{{ __('active_filter') }}:</h2>
                    @endif
                    <div class="tw-flex tw-gap-2 tw-flex-wrap">

                        @if (request('keyword'))
                            <span
                                class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('search') }}:
                                {{ request('keyword') }}
                                <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer"
                                    onclick="keywordClose()">
                                    <x-svg.tw-close-icon />
                                </span>
                            </span>
                        @endif
                        @if (Route::current()->parameter('category'))
                            <span
                                class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('category') }}:
                                {{ $categories->where('slug', Route::current()->parameter('category'))->first()->name ?? '-' }}
                                <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer"
                                    onclick="categoryClose()">
                                    <x-svg.tw-close-icon />
                                </span>
                            </span>
                        @endif
                        @if (request('job_type'))
                            <span
                                class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ request('job_type') }}<span
                                    class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer" onclick="jobTypeClose()">
                                    <x-svg.tw-close-icon />
                                </span>
                            </span>
                        @endif
                        @if (is_string(request('price_min')) || is_string(request('price_max')))
                            <span
                                class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('salary') }}
                                {{ request('price_min') }} - {{ request('price_max') }}
                                <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer"
                                    onclick="jobSalaryClose()">
                                    <x-svg.tw-close-icon />
                                </span>
                            </span>
                        @endif
                        @if (request('is_remote'))
                            <span
                                class="tw-py-1 tw-pl-3 tw-pr-[28px] tw-bg-[#E7F0FA] tw-text-sm tw-text-[#474C54] tw-relative tw-rounded-[30px]">{{ __('remote_job') }}
                                <span class="tw-absolute tw-right-[5px] tw-top-[3px] cursor-pointer"
                                    onclick="remotelyClose()">
                                    <x-svg.tw-close-icon />
                                </span>
                            </span>
                        @endif
                    </div>
                </div>
                 <!-- google adsense area -->
                    @if (advertisement_status('job_page_ad'))
                        @if (advertisementCode('job_page_fat_ad_after_filter_section'))
                            <div class="container my-4">
                                {!! advertisementCode('job_page_fat_ad_after_filter_section') !!}
                            </div>
                        @endif
                    @endif
                <!-- google adsense area end -->
                @if ($featured_jobs && count($featured_jobs))
                    <div>
                        <h5>{{ __('featured_jobs') }}</h5>
                        <div class="testimonail_active feature-job !-tw-mx-3 ll-feature-job slick-bullet deafult_style_dot">
                            @foreach ($featured_jobs as $job)
                                <x-website.job.job-card :job="$job" :featured="false" />
                            @endforeach
                        </div>
                    </div>
                @endif
                <!-- google adsense area -->
                @if (advertisement_status('job_page_ad'))
                    @if (advertisementCode('job_page_fat_ad_after_featured_section'))
                        <div class="container my-4">
                            {!! advertisementCode('job_page_fat_ad_after_featured_section') !!}
                            </div>
                        @endif
                @endif
                 <!-- google adsense area end -->
                <div class="row mt-5">
                    <h5>{{ __('latest_jobs') }}</h5>

                    @php
                        $mix_jobs = $all_jobs && count($all_jobs) ? $all_jobs : $jobs;
                        $jobId = 0;
                    @endphp

                    @forelse ($mix_jobs as $job)
                        <div class="col-xl-4 col-md-6 fade-in-bottom rt-mb-24 cat-1 cat-3">
                            <x-website.job.job-card :job="$job" />
                        </div>
                        @if (isset($job->id))
                            @php $jobId =  $job->id; @endphp
                        @endif
                    @empty
                        <div class="col-12" id="loading-spinner">
                            <div class="card text-center">
                                <x-not-found message="{{ __('no_data_found') }}" />
                            </div>
                        </div>
                    @endforelse
                    <div id="mix-job" class="row"></div>

                    @if (!$mix_jobs->isEmpty())
                        <button id="load-more-button" data-page="1" data-id="{{ $jobId }}"
                            class="newsButton btn btn-primary px-4 py-2 m-auto">{{ __('load_more') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </form>
    <!-- google adsense area -->
    @if (advertisementCode('home_page_center'))
    <div class="container my-4">
        {!! advertisementCode('home_page_center') !!}
    </div>
    @endif
<!-- google adsense area end -->
    <div class="rt-spacer-100 rt-spacer-md-50"></div>
        {{-- <!-- google adsense area -->
        @if (advertisementCode('home_page_center'))
            <div class="container my-4">
                {!! advertisementCode('home_page_center') !!}
            </div>
        @endif
        <!-- google adsense area end --> --}}
    {{-- Subscribe Newsletter --}}
    <x-website.subscribe-newsletter />

    {{-- Apply job Modal --}}
    <div class="modal fade" id="cvModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-transparent">
                    <h5 class="modal-title" id="cvModalLabel">{{ __('apply_job') }}: <span
                            id="apply_job_title">{{ __('job_title') }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('website.job.apply') }}">
                    @csrf
                    <div class="modal-body mt-3">
                        <input type="hidden" id="apply_job_id" name="id">
                        <div class="from-group">
                            <x-forms.label name="choose_resume" :required="true" />
                            <select class="rt-selectactive form-control w-100-p" name="resume_id" required>
                                <option value="">{{ __('select_one') }}</option>
                                @foreach ($resumes as $resume)
                                    <option {{ old('resume_id') == $resume->id ? 'selected' : '' }}
                                        value="{{ $resume->id }}">{{ $resume->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <x-forms.label name="cover_letter" :required="true" />
                            <textarea id="default" class="form-control @error('cover_letter') is-invalid @enderror" name="cover_letter"
                                rows="7" required></textarea>
                            @error('cover_letter')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer border-transparent">
                        <button type="button" class="bg-priamry-50 btn btn-outline-primary" data-bs-dismiss="modal"
                            aria-label="Close">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="button-content-wrapper ">
                                <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                                <span class="button-text">
                                    {{ __('apply_now') }}
                                </span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection


@section('css')
    <style>
        .feature-job .slick-slide {
            margin-left: 0px !important;
            margin: 0px 12px !important;
        }

        .feature-job.testimonail_active .prev-arrow {
            left: -60px;
        }

        .feature-job.testimonail_active .next-arrow {
            right: -60px;
        }

        .feature-job .slick-dots {
            display: none !important;
        }
    </style>
@endsection

@section('script')
    <script>
        function loadMoreJobs() {
            let currentUrl = window.location.href;
            let urlWithoutQueryString = currentUrl.split('?')[0];
            let queryString = window.location.search;

            let id = parseInt(document.getElementById('load-more-button').getAttribute('data-id'));
            let page = parseInt(document.getElementById('load-more-button').getAttribute('data-page'));

            // Extract existing "keyword" and "location" parameters from the query string
            let searchParams = new URLSearchParams(queryString);
            let existingKeyword = searchParams.get('keyword');
            let existingLocation = searchParams.get('location');

            // Convert null values to empty strings if they are null
            existingKeyword = existingKeyword === null ? '' : existingKeyword;
            existingLocation = existingLocation === null ? '' : existingLocation;

            // Construct the updated query string with all parameters
            let updatedQueryString = `?page=${page}&id=${id}&keyword=${existingKeyword}&location=${existingLocation}`;

            let newUrl = `${urlWithoutQueryString.replace('/jobs', '/loadmore')}${updatedQueryString}`;

            $('#load-more-button').prop('disabled', true).text('Loading...');
            axios.get(newUrl).then((response) => {
                $('#mix-job').append(response.data);
                $('#load-more-button').prop('disabled', false).text('Load More');
                let newId = parseInt(document.getElementById('get-id-page').getAttribute('data-id'));
                document.getElementById('load-more-button').setAttribute('data-id', newId);
                if (newId == 0) {
                    document.getElementById('load-more-button').setAttribute('data-page', page + 1);
                }
                $('#get-id-page').remove();
            }).catch((error) => {
                $('#load-more-button').prop('disabled', true).text('No jobs found').removeClass('btn-primary')
                    .addClass('btn-secondary');
            })
        }

        $(document).ready(function() {
            $('#load-more-button').click(function(e) {
                e.preventDefault();
                loadMoreJobs();
            });
        });
    </script>

    <script>
        function handleBookmarkClick() {
            // console.log(1);
            // const apiUrl = `/api/bookmark/${slug}`;

            // axios.post(apiUrl)
            //     .then(response => {
            //         const bookmarkButton = document.getElementById('bookmark');

            //         if (response.data.bookmarked) {
            //             // Update the icon to show that it's bookmarked
            //             bookmarkButton.innerHTML =
            //                 '<x-svg.bookmark-icon width="24" height="24" fill="#0A65CC" stroke="#0A65CC" />';
            //         } else {
            //             // Update the icon to show that it's not bookmarked
            //             bookmarkButton.innerHTML = '<x-svg.unmark-icon />';
            //         }
            //     })
            //     .catch(error => {
            //         // Handle any errors here
            //         console.error(error);
            //     });
        }
    </script>
@endsection
