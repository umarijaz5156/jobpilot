@extends('backend.settings.setting-layout')

@section('title')
    {{ __('website_settings') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('affiliate_setting') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('affiliate_setting') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
    <div class="row mb-3">
        <div class="col-sm-3">
            <form action="{{ route('settings.affiliate.default') }}" method="POST">
                @csrf
                <div>
                    <label class="" for="">{{ __('default') }} {{ __('affiliate') }} {{ __('jobs') }}</label>
                </div>
                <div class="d-flex">
                    <select name="job_provider" class="form-control">
                        <option value="" hidden>{{ __('select_one') }}</option>
                        @if (config('templatecookie.default_job_provider'))
                        <option value="">{{ __('disable_all') }}</option>
                        @endif
                        <option {{ config('templatecookie.default_job_provider') == 'careerjet' ? 'selected' : '' }} value="careerjet">
                            Careerjet Jobs
                        </option>
                        <option {{ config('templatecookie.default_job_provider') == 'indeed' ? 'selected' : '' }} value="indeed">
                            Indeed Jobs
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary ml-2">{{ __('update') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header ">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">
                            {{ __('carrerjet_api') }}
                            (<small><a target="_blank" href="http://www.careerjet.com.bd/partners/?ak=90be9ef4225c017181e4804a187f1a60">
                                Become an affiliate partner
                            </a></small>)
                        </h3>
                    </div>
                </div>
                <form class="form-horizontal" action="{{ route('settings.careerjet.update') }}"
                    method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <x-forms.label name="affiliate_id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input
                                    value="{{ old('careerjet_affiliate_id', config('templatecookie.careerjet_id')) }}"
                                    name="careerjet_affiliate_id" type="text"
                                    class="form-control @error('careerjet_affiliate_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('careerjet_affiliate_id')
                                    <span class="invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5" for="careerjet_job_limit"> {{ __('job_limit') }} {{ __('per_page') }}</label>
                            <div class="col-sm-7">
                                <select name="job_limit" id="careerjet_job_limit"
                                    class="form-control mr-sm-2 @error('job_limit') is-invalid @enderror">
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_limit') == 5 ? 'selected' : '' }}
                                        value="5">
                                        5
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_limit') == 10 ? 'selected' : '' }}
                                        value="10">
                                        10
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_limit') == 15 ? 'selected' : '' }}
                                        value="15">
                                        15
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_limit') == 20 ? 'selected' : '' }}
                                        value="20">
                                        20
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_limit') == 50 ? 'selected' : '' }}
                                        value="50">
                                        50
                                    </option>
                                </select>
                                @error('job_limit')
                                    <span class="invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <x-forms.label name="default_locale" class="col-sm-5" />
                            <div class="col-sm-7">
                                <select name="default_locale" class="select2bs4 custom-select mr-sm-2 @error('default_locale') is-invalid @enderror">
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_BD' ? 'selected' : '' }}
                                        value="en_BD">
                                        en_BD ( Bangladesh )
                                    </option>

                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'cs_CZ' ? 'selected' : '' }}
                                        value="cs_CZ">
                                        cs_CZ ( Czech Republic )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'da_DK' ? 'selected' : '' }}
                                        value="da_DK">
                                        da_DK ( Denmark )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'de_AT' ? 'selected' : '' }}
                                        value="de_AT">
                                        de_AT ( Austria )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'de_CH' ? 'selected' : '' }}
                                        value="de_CH">
                                        de_CH ( Switzerland )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'de_DE' ? 'selected' : '' }}
                                        value="de_DE">
                                        de_DE ( Germany )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_AE' ? 'selected' : '' }}
                                        value="en_AE">
                                        en_AE ( United Arab Emirate )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_AU' ? 'selected' : '' }}
                                        value="en_AU">
                                        en_AU ( Australia )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_CA' ? 'selected' : '' }}
                                        value="en_CA">
                                        en_CA ( Canada )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_CN' ? 'selected' : '' }}
                                        value="en_CN">
                                        en_CN ( China )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_HK' ? 'selected' : '' }}
                                        value="en_HK">
                                        en_HK ( Hong Kong )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_IE' ? 'selected' : '' }}
                                        value="en_IE">
                                        en_IE ( Ireland )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_IN' ? 'selected' : '' }}
                                        value="en_IN">
                                        en_IN ( India )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_MY' ? 'selected' : '' }}
                                        value="en_MY">
                                        en_MY ( Malaysia )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_NZ' ? 'selected' : '' }}
                                        value="en_NZ">
                                        en_NZ ( New Zealand )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_OM' ? 'selected' : '' }}
                                        value="en_OM">
                                        en_OM ( Oman )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_PH' ? 'selected' : '' }}
                                        value="en_PH">
                                        en_PH ( Philippines )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_PK' ? 'selected' : '' }}
                                        value="en_PK">
                                        en_PK ( Pakistan )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_QA' ? 'selected' : '' }}
                                        value="en_QA">
                                        en_QA ( Qatar )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_SG' ? 'selected' : '' }}
                                        value="en_SG">
                                        en_SG ( Singapore )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_GB' ? 'selected' : '' }}
                                        value="en_GB">
                                        en_GB ( United Kingdom )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_US' ? 'selected' : '' }}
                                        value="en_US">
                                        en_US ( United States )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_ZA' ? 'selected' : '' }}
                                        value="en_ZA">
                                        en_ZA ( South Africa )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_TW' ? 'selected' : '' }}
                                        value="en_TW">
                                        en_TW ( Taiwan )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'en_VN' ? 'selected' : '' }}
                                        value="en_VN">
                                        en_VN ( Vietnam )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_AR' ? 'selected' : '' }}
                                        value="es_AR">
                                        es_AR ( Argentina )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_BO' ? 'selected' : '' }}
                                        value="es_BO">
                                        es_BO ( Bolivia )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_CL' ? 'selected' : '' }}
                                        value="es_CL">
                                        es_CL ( Chile )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_CR' ? 'selected' : '' }}
                                        value="es_CR">
                                        es_CR ( Costa Rica )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_DO' ? 'selected' : '' }}
                                        value="es_DO">
                                        es_DO ( Dominican Republic )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_EC' ? 'selected' : '' }}
                                        value="es_EC">
                                        es_EC ( Ecuador )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_ES' ? 'selected' : '' }}
                                        value="es_ES">
                                        es_ES ( Spain )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_GT' ? 'selected' : '' }}
                                        value="es_GT">
                                        es_GT ( Guatemala )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_MX' ? 'selected' : '' }}
                                        value="es_MX">
                                        es_MX ( Mexico )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_PA' ? 'selected' : '' }}
                                        value="es_PA">
                                        es_PA ( Panama )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_PE' ? 'selected' : '' }}
                                        value="es_PE">
                                        es_PE ( Peru )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_PR' ? 'selected' : '' }}
                                        value="es_PR">
                                        es_PR ( Puerto Rico )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_PY' ? 'selected' : '' }}
                                        value="es_PY">
                                        es_PY ( Paraguay )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_UY' ? 'selected' : '' }}
                                        value="es_UY">
                                        es_UY ( Uruguay )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'es_VE' ? 'selected' : '' }}
                                        value="es_VE">
                                        es_VE ( Venezuela )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fi_FI' ? 'selected' : '' }}
                                        value="fi_FI">
                                        fi_FI ( Finland )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_CA' ? 'selected' : '' }}
                                        value="fr_CA">
                                        fr_CA ( Canada )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_BE' ? 'selected' : '' }}
                                        value="fr_BE">
                                        fr_BE ( Belgium )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_CH' ? 'selected' : '' }}
                                        value="fr_CH">
                                        fr_CH ( Switzerland )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_FR' ? 'selected' : '' }}
                                        value="fr_FR">
                                        fr_FR ( France )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_LU' ? 'selected' : '' }}
                                        value="fr_LU">
                                        fr_LU ( Luxembourg )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'fr_MA' ? 'selected' : '' }}
                                        value="fr_MA">
                                        fr_MA ( Morocco )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'hu_HU' ? 'selected' : '' }}
                                        value="hu_HU">
                                        hu_HU ( Hungary )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'it_IT' ? 'selected' : '' }}
                                        value="it_IT">
                                        it_IT ( Italy )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'ja_JP' ? 'selected' : '' }}
                                        value="ja_JP">
                                        ja_JP ( Japan )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'ko_KR' ? 'selected' : '' }}
                                        value="ko_KR">
                                        ko_KR ( Korea )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'nl_BE' ? 'selected' : '' }}
                                        value="nl_BE">
                                        nl_BE ( Belgium )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'nl_NL' ? 'selected' : '' }}
                                        value="nl_NL">
                                        nl_NL ( Netherlands )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'no_NO' ? 'selected' : '' }}
                                        value="no_NO">
                                        no_NO ( Norway )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'pl_PL' ? 'selected' : '' }}
                                        value="pl_PL">
                                        pl_PL ( Poland )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'pt_PT' ? 'selected' : '' }}
                                        value="pt_PT">
                                        pt_PT ( Portugal )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'pt_BR' ? 'selected' : '' }}
                                        value="pt_BR">
                                        pt_BR ( Brazil )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'ru_RU' ? 'selected' : '' }}
                                        value="ru_RU">
                                        ru_RU ( Russia )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'ru_UA' ? 'selected' : '' }}
                                        value="ru_UA">
                                        ru_UA ( Ukraine )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'sv_SE' ? 'selected' : '' }}
                                        value="sv_SE">
                                        sv_SE ( Sweden )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'sk_SK' ? 'selected' : '' }}
                                        value="sk_SK">
                                        sk_SK ( Slovakia )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'tr_TR' ? 'selected' : '' }}
                                        value="tr_TR">
                                        tr_TR ( Turkey )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'uk_UA' ? 'selected' : '' }}
                                        value="uk_UA">
                                        uk_UA ( Ukraine )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'vi_VN' ? 'selected' : '' }}
                                        value="vi_VN">
                                        vi_VN ( Vietnam )
                                    </option>
                                    <option
                                        {{ config('templatecookie.careerjet_default_locale') == 'zh_CN' ? 'selected' : '' }}
                                        value="zh_CN">
                                        zh_CN ( China )
                                    </option>

                                </select>
                                @error('default_locale')
                                    <span class="invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i
                                            class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header ">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">
                            {{ __('indeed_api') }}
                            (<small><a target="_blank" href="https://indianaffiliateprograms.com/affiliate-programs/indeed">
                                Become an affiliate partner
                            </a></small>)
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('settings.indeed.update') }}"
                    method="POST">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <x-forms.label name="affiliate_id" class="col-sm-5" />
                            <div class="col-sm-7">
                                <input
                                    value="{{ old('indeed_affiliate_id', config('templatecookie.indeed_id')) }}"
                                    name="indeed_affiliate_id" type="text"
                                    class="form-control @error('indeed_affiliate_id') is-invalid @enderror"
                                    autocomplete="off">
                                @error('indeed_affiliate_id')
                                    <span class="invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5" for="indeed_job_limit"> {{ __('job_limit') }} {{ __('per_page') }}</label>
                            <div class="col-sm-7">
                                <select name="job_limit" class="form-control mr-sm-2 @error('job_limit') is-invalid @enderror" id="indeed_job_limit">
                                    </option>
                                    <option
                                        {{ config('templatecookie.indeed_limit') == 5 ? 'selected' : '' }}
                                        value="5">
                                        5
                                    </option>
                                    <option
                                        {{ config('templatecookie.indeed_limit') == 10 ? 'selected' : '' }}
                                        value="10">
                                        10
                                    </option>
                                    <option
                                        {{ config('templatecookie.indeed_limit') == 15 ? 'selected' : '' }}
                                        value="15">
                                        15
                                    </option>
                                    <option
                                        {{ config('templatecookie.indeed_limit') == 20 ? 'selected' : '' }}
                                        value="20">
                                        20
                                    </option>
                                    <option
                                        {{ config('templatecookie.indeed_limit') == 50 ? 'selected' : '' }}
                                        value="50">
                                        50
                                    </option>
                                </select>
                                @error('job_limit')
                                    <span class="invalid-feedback"
                                        role="alert"><span>{{ $message }}</span></span>
                                @enderror
                            </div>
                        </div>
                        @if (userCan('setting.update'))
                            <div class="form-group row">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i
                                            class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
        .select2-results__option[aria-selected=true] {
            display: none;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            color: #fff;
            border: 1px solid #fff;
            background: #007bff;
            border-radius: 30px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
        }
    </style>
@endsection
