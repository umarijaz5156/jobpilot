@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('home');
    @endphp
Council Direct is the go-to platform for finding and posting the latest community service jobs in Australia. Let's find yours today! 
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
Find Latest Community Service Jobs in Australia
@endsection

@section('main')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <section class="hero-section-3">
        <div class="container">
            <div class="tw-flex tw-justify-center tw-items-center tw-relative tw-z-50">
                <div class="tw-max-w-3xl tw-text-white tw-text-center">
                    <h1 class="tw-text-white">{!! __('Community Service Jobs in Australia') !!}</h1>
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
                            @php
                                 $country = App\Models\SearchCountry::where('name','Australia')->first();
                                 $states = App\Models\State::where('country_id',$country->id)->get();
                            @endphp

                            <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                <input name="long" class="leaf_lon" type="hidden">
                                <input name="lat" class="leaf_lat" type="hidden">
                                <select style="border: none;font-size: 16px;color: #a3a4a7 !important;" name="state_id" class="text-gray-900">
                                    <option value="" selected disabled>{{ __('Select state') }}</option>
                                    @php
                                        $orderedStates = [
                                            'NSW (New South Wales)',
                                            'VIC (Victoria)',
                                            'QLD (Queensland)',
                                            'TAS (Tasmania)',
                                            'NT (Northern Territory)',
                                            'SA (South Australia)',
                                            'ACT (Australian Capital Territory)',
                                            'NZ (New Zealand)',
                                             'WA (Western Australia)',
                                             'New Zealand'
                                        ];
                                    @endphp
                                    @foreach($orderedStates as $stateName)
                                        @if($state = $states->where('name', $stateName)->first())
                                            <option {{ request('state_id') == $state->id ? 'selected' : '' }}  value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endif
                                    @endforeach
                                </select>


                                <div class="icon-badge">
                                    <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24" height="24" />
                                </div>
                            </div>

                               {{-- <div class="flex-grow-1 fromGroup has-icon banner-select no-border">
                                <input name="long" class="leaf_lon" type="hidden">
                                <input name="lat" class="leaf_lat" type="hidden">
                                <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                       name="location" value="{{ $oldLocation }}" autocomplete="off"
                                       class="text-gray-900">

                                <div class="icon-badge">
                                    <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}" width="24" height="24" />
                                </div>

                            </div> --}}

                            @endif
                            <div class="flex-grow-0">
                                <button type="submit"
                                    class="btn btn-primary d-block d-md-inline-block ">{{ __('find_job_now') }}</button>
                            </div>
                        </div>
                    </form>
                    {{-- @if ($top_categories->count())
                        <div class="f-size-14 banner-quciks-links" data-aos="" data-aos-duration="1000"
                            data-aos-delay="500">
                            <span class="!tw-text-gray-300">{{ __('suggestion') }}: </span>
                            @foreach ($top_categories as $item)
                                @if ($item->slug)
                                    <a class="!tw-text-white tw-underline"
                                        href="{{ route('website.job.category.slug', ['category' => $item->slug]) }}">>
                                        {{ $item->name }} {{ !$loop->last ? ',' : '' }}</a>
                                @endif
                            @endforeach
                    @endif
                </div> --}}
            </div>
        </div>
    </section>
    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_thin_ad_after_counter_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_thin_ad_after_counter_section') !!}
            </div>
        @endif
    @endif
    <!-- google adsense area end -->
    <!-- category section -->
 
    <section class="bg-light rounded shadow-sm md:tw-py-20 tw-py-12">
        <div class="container ">
            {{-- <div class="text-center">
                <h1></h1>
            </div> --}}
            <div class="tw-mt-8 tw-relative tw-z-50">
                 <div class="row justify-content-center">
                    <div class="col-md-12 ">
                        <div class=" p-4 " style="font-size: 1.2rem;">

                            <p>
                                Are you interested in doing a community service job in Australia? You find them on the Council Direct website. Council Direct is a platform which advertises community service jobs leading you towards better career choices. Community Services includes forming a range of initiatives which aim to provide support for individuals and communities in need. These services are kind of voluntary works done by individuals to benefit their community.
                            </p>
                                <p>
                                    Community Service Jobs in Australia involve working in Youth Services, Social Services, and Health Care system. These programs promote the mental health of the community and provide opportunities for indigenous people. For finding community services jobs in Australia, you can visit Council Direct!
                                </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

  <!-- jobs card -->
  <section class="tw-bg-primary-50 md:tw-py-20 tw-py-12">
    <div class="container">
        <div class="row md:tw-pb-12 tw-pb-8">
            <div class="col-12">
                <div class="tw-flex tw-gap-3 tw-items-center tw-flex-wrap">
                    <div class="flex-grow-1">
                        <h2 class="tw-mb-0">
                            {{ __('top') }}
                            <span class="text-primary-500 has-title-shape">{{ __('featured_jobs') }}
                                <img src="{{ asset('frontend') }}/assets/images/all-img/title-shape.png"
                                    alt="">
                            </span>
                        </h2>
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
            @if ($electrical && count($electrical) > 0)
                @foreach ($electrical as $job)
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
                                        <div class="tw-w-[56px] tw-h-[56px]">
                                            <img class="tw-rounded-lg tw-w-[56px] tw-h-[56px]"
                                                src="{{ $job?->company?->logo_url }}" alt=""
                                                draggable="false">

                                        </div>
                                        {{-- <span
                                            class="tw-text-[#0BA02C] tw-text-[12px] tw-leading-[12px] tw-font-semibold tw-bg-[#E7F6EA] tw-px-2 tw-py-1 tw-rounded-[3px]">
                                            {{ $job->job_type ? $job->job_type->name : '' }}
                                        </span> --}}
                                    </div>
                                    {{-- <div>
                                        <span class="tw-text-sm tw-text-[#767F8C]">
                                            @if ($job->salary_mode == 'range')
                                                {{ currencyAmountShort($job->min_salary) }} -
                                                {{ currencyAmountShort($job->max_salary) }}
                                                {{ currentCurrencyCode() }}
                                            @else
                                                {{ $job->custom_salary }}
                                            @endif
                                        </span>
                                    </div> --}}
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    {{-- <span>

                                    </span> --}}
                                    <div class="iconbox-content">
                                        <div class="tw-mb-1 tw-inline-flex">
                                            <span
                                                class="tw-text-base tw-font-medium tw-text-[#18191C]">{{ $job->company->user->name ?? " "}}</span>
                                        </div>
                                        <span class="tw-flex tw-items-center tw-gap-1">
                                            <i class="ph-map-pin"></i>
                                            <span class="tw-location">{{  $job->state->name ?? '' }}</span>
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



    <section class="bg-light rounded shadow-sm md:tw-py-20 tw-py-12">
        <div class="container">
            <div class="text-center">
                <h2>Jobs in Community Services Australia</h2>
            </div>
            <div class="tw-mt-8 tw-relative tw-z-50">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="p-4" style="font-size: 1.2rem;">
                            <p>
                                You can find a number of Jobs in Community Services Australia. The common job roles include working as Community Worker, Social Worker, Family Support Worker and Community Development Officer. They focus on planning and implementing community programs in the society. They provide care and support for the elderly individuals in care facilities. 
                            </p>
                            <p>
                                They also earn a good amount of money and as the experience increases they get more career opportunities in the field. Community Service Jobs in Australia require a degree or diploma in social work, psychology or community services. Council Direct advertises the community services jobs according to different states, local bodies and regions.
                            </p>
                          
                          
                            <h3 class="pt-5">Community Service Job Opportunities</h3>
                            <p>
                                There are various community service jobs opportunities. As a Community Development Officer, they work to design and implement trying to improve community welfare. They also work as Youth Worker, in which they work for the youth and provide them emotional  or financial challenges. As Family Support Worker, they assist families and guide them in crisis management.
                            </p>
                            <p>
                                Community Services Jobs also include Disability Support Worker in which they support the disabled people and provide support to them. The most common community service job opportunity involves working as a Mental Health Support Worker. They try to help the young minds in maintaining and solving the health issues. Council Direct is a great platform which advertises community service job opportunities for the job finders through their job listings.
                            </p>
                           
                           

                            <h3 class="pt-5">What is Community Services Work?</h3>
                            <p>
                                Community Services Work refers to a field of employment focused on helping the families, individuals and communities to overcome the challenges in their daily lives. It involves working in health and social care to provide assistance to individuals to work on their emotional needs.
                            </p>
                            <p>
                                Community Services Work is not a field but a voluntary group of people working towards the betterment of the society. They help people in doing essential services such as housing, food and healthcare. The major purpose of community services work also features on providing counseling advice to the people. Community Services Jobs in Australia also includes community services work done voluntary or involuntary by young people.
                            </p>
                          

                            <h3 class="pt-5">Types of Community Services</h3>
                            <p>
                                Different types of community services are present in Community Services Jobs in Australia. Social Services is also a type of community service which provides social support to individuals. They provide emergency relief to the people who are in financial crisis. Another form of community services involves Health and Wellbeing Services. The focus on mental and physical wellbeing of the people.
                            </p>
                            <p>
                                Youth Services also comes under Community Services. They provide social and emotional navigation to the students. Disability Support Services is also a major and famous type of community services which help disabled people. Family and Child Services offer support to families on how to maintain a good family environment.
                            </p>
                        
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <section class="bg-light rounded shadow-sm md:tw-py-8 tw-py-8">
        <div class="container ">
            <div class="text-center">
                <h2>Why Choose Us?</h2>
            </div>
            <div class="tw-mt-8 tw-relative tw-z-50">
                 <div class="row justify-content-center">
                    <div class="col-md-12 ">
                        <div class=" p-4 " style="font-size: 1.2rem;">

                            <p>
                                Council Direct is a great and user-friendly platform which advertises the Environment Jobs in Australia. Council Direct specializes in listing jobs within local councils and government organizations and targets the jobs in public service. This platform is a reliable source for those seeking Environment Jobs in Australia. You can filter the jobs according to your need and apply!
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- google adsense area end -->

    <!-- google adsense area -->
    @if (advertisement_status('home_page_ad'))
        @if (advertisementCode('home_page_fat_ad_after_client_section'))
            <div class="container my-4">
                {!! advertisementCode('home_page_fat_ad_after_client_section') !!}
            </div>
        @endif
    @endif

        <!-- create profile -->
        <section class="md:tw-py-20 tw-py-12 !tw-border-t !tw-border-b !tw-border-primary-100">
            <div class="container">
                <div class="row tw-items-center">
                    <div class="col-lg-6">
                        <img class="tw-rounded-lg" src="{{ asset('home_page.webp') }}"
                            alt="jobBox">
                    </div>
                    <div class="col-lg-6">
                        <div class="lg:tw-ps-12 tw-pt-6 lg:tw-pt-0">
                            <h5 class="tw-text-primary-500 tw-mb-4">{{ __('create_profile') }}</h5>
                            <h2 class="">{{ __('create_your_personal_account_profile') }}</h2>
                            <p class="">{{ __('work_profile_description') }}</p>
                            <div class="">
                                <a href="{{ route('register') }}" class="btn btn-primary">{{ __('create_profile') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="bg-light py-5">
            <div class="container">
                <h2 class="text-center mb-4">Frequently Asked Questions</h2>
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="border-color: #0851A4; color: #0851A4;">
                                <strong>What is a community service job in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A community service job in Australia involves working for the benefit of society. Community service workers offer advice to families and children to ensure their safety within the community. These services are often voluntary, but some are paid positions.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 2 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="border-color: #0851A4; color: #0851A4;">
                                <strong>Is community service in demand in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, community service is in demand in Australia. Community workers are well-compensated, and contributing to society brings a sense of accomplishment. Community services are vital for maintaining the well-being of society.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 3 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="border-color: #0851A4; color: #0851A4;">
                                <strong>How much do community service workers get paid in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Community service workers in Australia typically earn between AUD 80,000 and AUD 90,000 annually, depending on their experience. Some community service positions may be part-time or paid on a daily or monthly basis.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 4 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="border-color: #0851A4; color: #0851A4;">
                                <strong>How do I become a community worker in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To become a community worker in Australia, you should get involved with community associations that offer relevant experience. A degree in Community Services is also recommended to qualify for community service positions.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 5 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="border-color: #0851A4; color: #0851A4;">
                                <strong>Is community service good in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, community service is highly regarded in Australia. It offers opportunities for personal growth, career development, and community contribution. It can also provide benefits to those working in community service roles.
                            </div>
                        </div>
                    </div>
                    <!-- FAQ 6 -->
                    <div class="accordion-item border" style="border-color: #0851A4;">
                        <p class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix" style="border-color: #0851A4; color: #0851A4;">
                                <strong>What is the course for community service in Australia?</strong>
                            </button>
                        </p>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                The official course for community service in Australia is the Certificate III in Community Services. This qualification provides entry-level experience to work as a community support worker, caseworker assistant, or residential care worker.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        




        <style>
            .accordion-button:focus {
                box-shadow: none;
            }
            .accordion-button:not(.collapsed) {
                color: #0851A4;
                background-color: #e0e0e0;
            }
            .accordion-button:not(.collapsed) .icon {
                content: "-";
            }
            .accordion-button.collapsed .icon {
                content: "+";
            }
            .accordion-button {
                font-size: 1.25rem; /* Increase the font size of the questions */
            }
            .accordion-body {
                font-size: 1.15rem; /* Increase the font size of the answers */
            }
            .accordion-button .icon {
                margin-left: auto;
                font-size: 1.25rem; /* Adjust the size of the icon */
            }
        </style>


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

        <section class="bg-light rounded shadow-sm md:tw-py-20 tw-py-12">
            <div class="container ">
                <div class="text-center">
                    <h2>Conclusion</h2>
                </div>
                <div class="tw-mt-8 tw-relative tw-z-50">
                     <div class="row justify-content-center">
                        <div class="col-md-12 ">
                            <div class=" p-4 " style="font-size: 1.2rem;">

                                <p>
                                    Community Services Jobs in Australia are good way to start a career. They get experience in the field and help the communities to work and excel further. Community Support is a great step towards accomplishment of community services in Australia. Council Direct priorities these job listings and advertises them on their website. You can find them by visiting the website !
                                </p>
                               
                            </div>
                        </div>
                    </div>

                </div>

            </div>
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
    <!-- google adsense area end -->
    <!-- newsletter -->
    {{-- <section class="section-box tw-mb-8">
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
                        <h2 class="tw-text-white tw-font-bold tw-mb-8 text-center md:tw-text-4xl tw-text-2xl"> {!! __('updates_regularly') !!}
                        </h2>
                        <div class="box-form-newsletter mt-40">
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="tw-gap-2 tw-flex tw-flex-col sm:tw-flex-row">
                                @csrf
                                <input class="input-newsletter" type="text" value="" name="email"
                                    placeholder="{{ __('enter_email_here') }}">
                                <button type="submit"
                                    class="tw-border-0 tw-min-h-[48px] tw-rounded tw-px-3 tw-font-medium tw-bg-orange-400 !tw-text-white">{{ __('subscribe') }}</button>
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
    </section> --}}
    @php
    $cms = App\Models\Cms::first('home_page_banner_image');
    $bannerImage = $cms->home_page_banner_image ?? 'frontend/assets/images/hero-bg-3.jpeg';
@endphp

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/fontawesome-free/css/all.min.css">
    <x-map.leaflet.autocomplete_links />
    @include('map::links')
    <style>
        .hero-section-3 {
            padding: 100px 0px;
            background-image: url('{{ asset($bannerImage) }}');
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }

        .hero-section-3::after {
            background-color: black;
            content: "";
            height: 100%;
            left: 0;
            opacity: .5;
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1;
        }

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
            box-shadow: none; !important;
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
