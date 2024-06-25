@extends('frontend.layouts.app')

@section('title')
    {{ __('Verification Documents') }}
@endsection

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="dashboard-right-header">
                            <span class="sidebar-open-nav">
                                <i class="ph-list"></i>
                            </span>
                        </div>
                        <h2 class="tw-text-2xl tw-font-medium tw-text-[#18191C] tw-mb-8">
                            {{ __('Submit Documents') }}
                        </h2>
                        <form method="POST"
                              action="{{route('company.verify.documents.store')}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group col-8">
                                <x-forms.label name="Image of your NID/Driving Lisence/Passport " :required="false" />
                                <input name="document" type="file"
                                       data-show-errors="true" data-width="100%"

                                       data-default-file="{{ $company->getFirstMedia('document') ? $company->getFirstMedia('document')->getFullUrl() : ""  }}"
                                       {{ $company->document_verified_at ? "disabled='disabled'"  : '' }}
                                       class="dropify">
                            </div>
                            @error('document')
                            <div>
                                <span class="text-danger font-size-13 d-block mt-4"><strong>{{ $message }}</strong></span>
                            </div>
                            @enderror

                            @if(!$company->document_verified_at)
                            <button type="submit" class="btn btn-primary mt-4"> Upload</button>
                                @if($company->getFirstMedia('document'))
                                <div style=" color: red ; margin-top: 12px; ">


                                    <svg
                                        style="width: 32px ; height: 32px ;"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>

                                    <span>Unverified</span>

                                </div>
                                @endif
                            @else
                                <div style=" color: green ; margin-top: 12px; ">
                                    <svg
                                        style="width: 32px ; height: 32px ;"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Verified</span>

                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        input[type=file]{
            position:static;
            opacity: 1;
            width: auto;
            height: auto;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/dropify/css/dropify.min.css">


@endsection


@section('frontend_scripts')
    <script src="{{ asset('backend') }}/plugins/dropify/js/dropify.min.js"></script>

    <script>
        $('.dropify').dropify();
    </script>
    <script>

        $(document).ready(function (){
            $('#nid_front').on('change',function (){
                $('#nid_front_form').submit();
            })
            $('#nid_back').on('change',function (){
                $('#nid_front_form').submit();
            })
            $('#tin').on('change',function (){
                $('#nid_front_form').submit();
            })
        });
    </script>
@endsection

