@extends('frontend.layouts.app')
@section('description')
    @php
        $data = metaData('home');
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
    <section class="hero-section-2 tw-bg-primary-500 md:tw-py-32 tw-py-16">
        <div class="container">
            <div class="tw-flex tw-justify-center tw-items-center">
                <div class="tw-max-w-3xl tw-text-white tw-text-center">
                    <h1 class="tw-text-white">{!! __('no_1_job_portal_home_2') !!}</h1>
                    <p>{{ __('job_seekers_stats') }}</p>
                    <form action="{{ route('website.job') }}" method="GET" id="job_search_form">
                        <div class="jobsearchBox d-flex flex-column flex-md-row bg-gray-10 input-transparent rt-mb-24"
                            data-aos="fadeinup" data-aos-duration="400" data-aos-delay="50">
                            <div class="flex-grow-1 fromGroup has-icon">
                                <input id="index_search" name="keyword" type="text"
                                    placeholder="{{ __('job_title_keyword') }}" value="{{ request('keyword') }}"
                                    autocomplete="off" class="text-gray-900">
                                <div class="icon-badge">
                                    <x-svg.search-icon />
                                </div>
                                <span id="autocomplete_index_job_results"></span>
                            </div>
                            <input type="hidden" name="lat" id="lat" value="">
                            <input type="hidden" name="long" id="long" value="">
                            @php
                                $oldLocation = request('location');
                                $map = $setting->default_map;
                            @endphp

                            @if ($map == 'google-map')
                                <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                    <input type="text" id="searchInput" placeholder="{{ __('enter_location') }}"
                                        name="location" value="{{ $oldLocation }}" class="text-gray-900">
                                    <div id="google-map" class="d-none"></div>
                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                            height="24" />
                                    </div>
                                </div>
                            @else
                                <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                    <input name="long" class="leaf_lon" type="hidden">
                                    <input name="lat" class="leaf_lat" type="hidden">
                                    <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                        name="location" value="{{ $oldLocation }}" autocomplete="off"
                                        class="text-gray-900">
                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24"
                                            height="24" />
                                    </div>
                                </div>
                            @endif
                            <div class="flex-grow-0">
                                <button type="submit"
                                    class="btn btn-primary d-block d-md-inline-block ">{{ __('find_job_now') }}</button>
                            </div>
                        </div>
                    </form>
                    @if ($top_categories->count())
                        <div class="f-size-14 banner-quciks-links" data-aos="" data-aos-duration="1000"
                            data-aos-delay="500">
                            <span class="!tw-text-gray-300">{{ __('suggestion') }}: </span>

                            @foreach ($top_categories as $item)
                                @if ($item->slug)
                                <a class="!tw-text-white tw-underline"
                                    href="{{ route('website.job.category.slug', ['category' => $item->slug]) }}">
                                    {{ $item->name }} {{ !$loop->last ? ',' : '' }}
                                </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
            <div class="tw-mt-8">
                <div class="category-slider">
                    @php
                        $popular_categories = $popular_categories->toArray();
                        ksort($popular_categories);
                    @endphp
                    @foreach ($popular_categories as $key => $category)
                        @isset($category['slug'])
                            <div class="tw-py-2">
                                <a href="{{ route('website.job.category.slug', $category['slug']) }}"
                                    class="!tw-bg-white tw-transition-all tw-duration-300 hover:-tw-translate-y-[2px] tw-shadow-md tw-rounded-md tw-px-4 tw-py-2.5 tw-flex tw-gap-4 tw-items-center">
                                    <span class="tw-text-2xl">
                                        <i class="{{ $category['icon'] }}"></i>
                                    </span>
                                    <div class="">
                                        <h4 class="tw-mb-0 tw-text-lg">
                                            {{ Illuminate\Support\Str::limit($category['name'], 26) }}
                                        </h4>
                                        <p class="tw-mb-0 tw-text-sm">{{ $category['jobs_count'] }} {{ __('open_positions') }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endisset
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- working process section -->
    <section class="working-process tw-bg-white">
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-h4 ft-wt-5">
                    <span class="text-primary-500 has-title-shape">{{ config('app.name') }}
                        <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png" alt="">
                    </span>
                    <label for="">{{ __('working_process') }}</label>
                </div>
            </div>
            <div class="rt-spacer-50"></div>
            <div class="row">
                <div class="col-lg-3 col-sm-6 rt-mb-24 position-relative">
                    <div class="has-arrow first">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-user-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('explore_opportunities') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('browse_through_a_diverse_range_of_job_listings_tailored_to_your_interests_and_expertise') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow middle">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-2.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-cloud-arrow-up"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('create_your_profile') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('build_a_standout_profile_highlighting_your_skills_experience_and_qualifications') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6 position-relative">
                    <div class="has-arrow last">
                        <img src="{{ asset('frontend') }}/assets/images/all-img/arrow-1.png" alt=""
                            draggable="false">
                    </div>
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-magnifying-glass-plus"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('apply_with_ease') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('effortlessly_apply_to_jobs_that_match_your_preferences_with_just_a_few_clicks') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 rt-mb-24 col-sm-6">
                    <div class="rt-single-icon-box hover:!tw-bg-primary-50 working-progress icon-center">
                        <div class="icon-thumb rt-mb-24">
                            <div class="icon-72">
                                <i class="ph-circle-wavy-check"></i>
                            </div>
                        </div>
                        <div class="iconbox-content">
                            <div class="body-font-2 rt-mb-12">{{ __('track_your_progress') }}</div>
                            <div class="body-font-4 text-gray-400">
                                {{ __('stay_informed_on_your_applications_and_manage_your_job_seeking_journey_effectively') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rt-spacer-100 rt-spacer-md-50"></div>
    </section>
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_workingprocess_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_workingprocess_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->
    <!-- jobs card -->
    <section class="tw-bg-primary-50 md:tw-py-20 tw-py-12">
        <div class="container">
            <div class="row tw-pb-12">
                <div class="col-12">
                    <div class="tw-flex tw-gap-3 tw-items-center tw-flex-wrap">
                        <div class="flex-grow-1">
                            <h4 class="tw-mb-0">
                                {{ __('top') }}
                                <span class="text-primary-500 has-title-shape">{{ __('featured_job') }}
                                    <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                        alt="">
                                </span>
                            </h4>
                        </div>
                        <a href="{{ route('website.job') }}" class="flex-grow-0 rt-pt-md-10">
                            <button class="btn btn-outline-primary !tw-border-primary-500">
                                <span class="button-content-wrapper ">
                                    <span class="button-icon align-icon-right">
                                        <i class="ph-arrow-right"></i>
                                    </span>
                                    <span>
                                        {{ __('view_all') }}
                                    </span>
                                </span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($featured_jobs && count($featured_jobs) > 0)
                    @foreach ($featured_jobs as $job)
                        <div class="col-xl-3 col-md-4 fade-in-bottom  condition_class rt-mb-24 tw-self-stretch">
                            <a href="{{ route('website.job.details', $job->slug) }}"
                                class="tw-h-full card tw-card tw-block jobcardStyle1 tw-border-gray-200 hover:!-tw-translate-y-1 hover:tw-bg-primary-50 tw-bg-gray-50"
                                tabindex="0">
                                <div class="tw-p-6 tw-flex tw-gap-3 tw-flex-col tw-justify-between tw-h-full">
                                    <div>
                                        <div class="tw-mb-1.5">
                                            <span class="tw-text-[#18191C] tw-text-lg tw-font-medium">
                                                {{ $job->title }}
                                            </span>
                                        </div>
                                        <div class="tw-flex tw-flex-wrap tw-gap-2 tw-items-center tw-mb-1.5">
                                            <span
                                                class="tw-text-[#0BA02C] tw-text-[12px] tw-leading-[12px] tw-font-semibold tw-bg-[#E7F6EA] tw-px-2 tw-py-1 tw-rounded-[3px]">
                                                {{ $job->job_type ? $job->job_type->name : '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="tw-text-sm tw-text-[#767F8C]">
                                                @if ($job->salary_mode == 'range')
                                                    {{ currencyAmountShort($job->min_salary) }} -
                                                    {{ currencyAmountShort($job->max_salary) }}
                                                    {{ currentCurrencyCode() }}
                                                @else
                                                    {{ $job->custom_salary }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <span>
                                            <div class="tw-w-[56px] tw-h-[56px]">
                                                <img class="tw-rounded-lg tw-w-[56px] tw-h-[56px]"
                                                    src="{{ $job?->company?->logo_url }}" alt=""
                                                    draggable="false">

                                            </div>
                                        </span>
                                        <div class="iconbox-content">
                                            <div class="tw-mb-1 tw-inline-flex">
                                                <span
                                                    class="tw-text-base tw-font-medium tw-text-[#18191C]">{{ $job->company->user->name }}</span>
                                            </div>
                                            <span class="tw-flex tw-items-center tw-gap-1">
                                                <i class="ph-map-pin"></i>
                                                <span class="tw-location">
                                                    {{ $job->country }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <button
                                            class="btn hover:tw-text-white hover:tw-bg-primary-700 tw-px-2.5 tw-py-1 tw-text-white tw-bg-primary-500">{{ __('apply_now') }}</button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_featuredjob_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_featuredjob_section') !!}
            </div>
        @endif
    @endif
    <!-- top companaies -->

    @if ($top_companies && count($top_companies) > 0)
        @if (!auth('user')->check() || (auth('user')->check() && authUser()->role == 'candidate'))
            <section class="md:tw-py-20 tw-py-12">
                <div class="container">
                    <div class="row tw-pb-12">
                        <div class="col-12">
                            <div class="tw-flex tw-items-center flex-wrap">
                                <div class="flex-grow-1">
                                    <h4 class="tw-mb-0">{{ __('top') }} <span
                                            class="text-primary-500 has-title-shape">{{ __('companies') }}
                                            <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                                alt="">
                                        </span></h4>
                                </div>
                                <a href="{{ route('website.company') }}" class="flex-grow-0 rt-pt-md-10">
                                    <button class="btn btn-outline-primary">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right">
                                                <i class="ph-arrow-right"></i>
                                            </span>
                                            <span>
                                                {{ __('view_all') }}
                                            </span>
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($top_companies as $company)
                            <div class="col-xl-3 col-md-4 fade-in-bottom  condition_class rt-mb-24 tw-self-stretch">
                                <a href="{{ route('website.employe.details', $company->user->username) }}"
                                    class="card jobcardStyle1 tw-h-full hover:!-tw-translate-y-1">
                                    <div class="tw-p-6 tw-flex tw-flex-col tw-gap-1.5">
                                        <div class="tw-w-14 tw-h-14">
                                            <img class="tw-w-full tw-h-full tw-object-cover"
                                                src="{{ $company->logo_url }}" alt="" draggable="false">
                                        </div>
                                        <div>
                                            <div class="">
                                                <span
                                                    class="tw-text-[#191F33] tw-text-base tw-font-medium">{{ $company->user->name }}</span>
                                            </div>
                                            @isset($company->country)
                                                <span
                                                    class="tw-inline-flex tw-text-sm tw-gap-1 tw-items-center text-gray-400 ">
                                                    <i class="ph-map-pin"></i>
                                                    {{ $company->country }}
                                                </span>
                                            @endisset

                                        </div>
                                        <div class="tw-flex tw-flex-wrap tw-gap-1.5">
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-inline-block tw-text-xs tw-font-medium tw-text-[#474C54] tw-rounded-[52px] tw-bg-primary-50 ll-primary-border">
                                                {{ $company?->industry?->name ?? '' }}
                                            </span>
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-inline-block tw-text-xs tw-font-medium tw-text-[#474C54] tw-rounded-[52px] tw-bg-primary-50 ll-primary-border">{{ $company->jobs_count }}
                                                {{ __('open_position') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <!-- google adsense area -->
            @if (advertisement_status('home_page_ad'))
                @if (advertisementCode('home_page_fat_ad_after_client_section'))
                    <div class="container my-4">
                        {!! advertisementCode('home_page_fat_ad_after_client_section') !!}
                    </div>
                @endif
            @endif
        @endif
    @endif
    <!-- newsletter -->
    <section class="section-box tw-mb-8">
        <div class="container">
            <div class="tw-bg-primary-500 tw-p-8 tw-rounded-xl">
                <div class="row align-items-center">
                    <div class="tw-relative tw-min-h-[400px] col-xl-3 col-12 text-center d-none d-xl-block">
                        <div class="tw-flex tw-gap-3 tw-items-start tw-flex-wrap">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-bottom tw-self-center"
                                src="{{ asset('frontend/assets/images/image-01.jpeg') }}" alt="">
                            <img class="tw-w-2/5 tw-rounded tw-shadow-sm animation-float-right tw-self-center"
                                src="{{ asset('frontend/assets/images/image-02.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-top tw-self-center"
                                src="{{ asset('frontend/assets/images/image-03.jpeg') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-6 col-12 md:tw-px-10">
                        <h2 class="tw-text-white tw-font-bold tw-mb-8 text-center md:tw-text-4xl tw-text-2xl">{!! __('updates_regularly') !!}
                        </h2>
                        <div class="box-form-newsletter mt-40">
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="tw-gap-2 tw-flex sm:tw-flex-row tw-flex-col">
                                @csrf
                                <input class="input-newsletter @error('email') is-invalid @enderror" type="text"
                                    value="" name="email" placeholder="{{ __('enter_email_here') }}">
                                <button type="submit"
                                    class="tw-border-0 tw-rounded tw-min-h-[48px] tw-px-3 tw-font-medium tw-bg-orange-400 !tw-text-white">{{ __('subscribe') }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="tw-relative tw-h-full col-xl-3 col-12 text-center d-none d-xl-block">
                        <div class="tw-flex tw-gap-3 tw-items-start tw-flex-wrap">
                            <img class="tw-w-2/5 tw-rounded tw-shadow-sm animation-float-left tw-self-center"
                                src="{{ asset('frontend/assets/images/image-06.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-bottom tw-self-center"
                                src="{{ asset('frontend/assets/images/image-04.jpeg') }}" alt="">
                            <img class="tw-w-1/2 tw-rounded tw-shadow-sm animation-float-top tw-self-center"
                                src="{{ asset('frontend/assets/images/image-05.jpeg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/fontawesome-free/css/all.min.css">
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
    <style>
        span.select2-container--default .select2-selection--single {
            border: none !important;
        }

        span.select2-selection.select2-selection--single {
            outline: none;
        }

        .marginleft {
            margin-left: 10px !important;
        }

        .category-slider .slick-slide {
            margin: 0px 8px;
        }

        .category-slider .slick-dots {
            bottom: -32px;
        }

        .category-slider .slick-dots li {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0px;
        }

        .category-slider .slick-dots li button {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            width: 10px;
            height: 10px;
        }

        .category-slider .slick-dots li.slick-active button {
            background: rgba(255, 255, 255, 1);
            width: 12px;
            height: 12px;
        }

        .category-slider .slick-dots li button::before {
            display: none;
        }
        body:has(.hero-section-2) .n-header--bottom {
            box-shadow: inset 0px -1px 0px rgb(228, 229, 232, 0.3); !important;
        }
    </style>
@endsection

@section('script')
    <script>
        $('.category-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            autoplay: true,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    </script>
@endsection
