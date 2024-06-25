@extends('frontend.layouts.app')

@section('title')
    {{ __('indeed_jobs') }}
@endsection

@section('main')
    <div class="joblist-content">
        <div class="container">
            <div class="row">
                {{-- Indeed jobs --}}
                @if (isset($indeed_jobs) && isset($indeed_jobs->results) && count($indeed_jobs->results))
                <div class="col-12 mt-5 pt-5">
                    <h4 class="tw-text-center">
                        {{ __('latest_jobs_from') }}
                        <a href="https://www.indeed.co.in"><img alt="Indeed" src="https://www.indeed.com/p/jobsearch.gif"
                            style="border:0;vertical-align:middle" class=" ezlazyloaded"
                            data-ezsrc="https://www.indeed.com/p/jobsearch.gif" height="25" width="80" ezoid="0.47928099933588997"></a>
                    </h4>

                    {{-- Filter job --}}
                    @include('frontend.pages.jobs.job-history')
                    <hr>
                    <div class="row">
                        @forelse ($indeed_jobs->results as $job)
                            <div class="col-lg-6 py-1">
                                <div class="card iconxl-size jobcardStyle1 ">
                                    <div class="card-body">
                                        <div class="rt-single-icon-box icb-clmn-lg ">
                                            <a target="_blank" href="{{ $job->url }}"
                                                class="iconbox-content">
                                                <div class="post-info2">
                                                    <div class="post-main-title">
                                                        {{ $job->jobtitle }}
                                                        <span
                                                            class="badge rounded-pill bg-primary-50 text-primary-500">
                                                            {{ $job->company }}
                                                        </span>
                                                    </div>
                                                    <div class="body-font-4 text-gray-600 pt-2">
                                                        <p>{!! $job->snippet !!}</p>
                                                        <span class="info-tools">
                                                            <x-svg.location-icon/>
                                                            {{ $job->formattedLocationFull }}
                                                        </span>
                                                        <span class="info-tools">
                                                            <x-svg.calender-icon/>
                                                            <span>{{ $job->formattedRelativeTime }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="iconbox-extra align-self-center">
                                                <div>
                                                    <a href="{{ $job->url }}" target="_blank"
                                                        class="btn btn-primary2-50">
                                                        <span class="button-content-wrapper ">
                                                            <span class="button-icon align-icon-right"><i
                                                                    class="ph-arrow-right"></i></span>
                                                            <span class="button-text">{{ __('details') }}</span>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-12">
                                <div class="card text-center">
                                    <x-not-found message="{{ __('no_data_found') }}" />
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="rt-spacer-100 rt-spacer-md-50"></div>

    {{-- Subscribe Newsletter --}}
    <x-website.subscribe-newsletter />

    <form action="{{ route('website.indeed.job') }}" id="affiliate_form">
        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
        <input type="hidden" name="category" value="{{ Route::current()->parameter('category') }}">
    </form>
@endsection

@push('frontend_scripts')
    <script>
        function keywordClose() {
            $('input[name="keyword"]').val('');
            $('#affiliate_form').submit();
        }

        function categoryClose() {
            $('input[name="category"]').val('');
            $('#affiliate_form').submit();
        }
    </script>
@endpush
