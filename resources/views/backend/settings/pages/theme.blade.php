@extends('backend.settings.setting-layout')
@section('title')
    {{ __('theme_settings') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('theme_settings') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('home') }}</a></li>
                <li class="breadcrumb-item">{{ __('settings') }}</li>
                <li class="breadcrumb-item active">{{ __('theme_settings') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
    <div class="alert alert-warning mb-3">
        <h5>{{ __('heads_up_customize_the_way_you_like') }}</h5>
        <hr class="my-2">
        {{ __('add_your_brand_theme_color_it_will_be_reflected_on_your_website_and_admin_panel_add_your') }} <a
            href="{{ route('settings.general') }}">{{ __('logo_and_favicon_here') }}</a>.
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title line-height-36">{{ __('website_theme_style') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">{{ __('primary_color') }}</div>
                        <div class="card-body">
                            <div class="frontend-primary-color"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">{{ __('secondary_color') }}</div>
                        <div class="card-body">
                            <div class="frontend-secondary-color"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (userCan('setting.update'))
            <div class="card-footer text-center">
                <button onclick="$('#color_picker_form').submit()" type="submit"
                    class="btn btn-primary w-250">{{ __('update') }}</button>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('admin_theme_style') }}</h3>
                </div>
                <div class="px-4 pt-3 pb-4">
                    <form id="color_picker_form" action="{{ route('settings.theme.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input id="sidebar_color_id" type="hidden" name="sidebar_color"
                            value="{{ $setting->sidebar_color }}">
                        <input id="nav_color_id" type="hidden" name="nav_color" value="{{ $setting->nav_color }}">
                        <input id="sidebar_txt_color_id" type="hidden" name="sidebar_txt_color"
                            value="{{ $setting->sidebar_txt_color }}">
                        <input id="nav_txt_color_id" type="hidden" name="nav_txt_color"
                            value="{{ $setting->nav_txt_color }}">
                        <input id="main_color_id" type="hidden" name="main_color" value="{{ $setting->main_color }}">
                        <input id="accent_color_id" type="hidden" name="accent_color"
                            value="{{ $setting->accent_color }}">
                        <input id="frontend_primary_id" type="hidden" name="frontend_primary_color"
                            value="{{ $setting->frontend_primary_color }}">
                        <input id="frontend_secondary_id" type="hidden" name="frontend_secondary_color"
                            value="{{ $setting->frontend_secondary_color }}">
                    </form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('left_sidebar_background_color') }}</div>
                                <div class="card-body">
                                    <div class="sidebar-bg-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('left_sidebar_text_color') }}</div>
                                <div class="card-body">
                                    <div class="sidebar-text-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('top_nav_background_color') }}</div>
                                <div class="card-body">
                                    <div class="navbar-bg-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('top_nav_text_color') }}</div>
                                <div class="card-body">
                                    <div class="navbar-text-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('main_color') }}</div>
                                <div class="card-body">
                                    <div class="main-color-picker"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">{{ __('accent_color') }}</div>
                                <div class="card-body">
                                    <div class="accent-color-picker"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (userCan('setting.update'))
                    <div class="card-footer text-center">
                        <button onclick="$('#color_picker_form').submit()" type="submit"
                            class="btn btn-primary w-250">{{ __('update') }}</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title line-height-36">{{ __('layout_setting') }} </h3>
                </div>
                <div class="px-4">
                    <div class="row pt-3 pb-4">
                        <form action="{{ route('settings.layout.update') }}" method="post" id="layout_form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="default_layout" id="layout_mode">
                        </form>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ __('left_navigation_layout') }}</h5>
                                </div>
                                <img src="{{ asset('backend/image/setting/left-sidebarlayout.png') }}"
                                    class="card-img-top img-fluid w-250 h-auto" alt="top nav">

                                @if (userCan('setting.update'))
                                    <div class="card-body">
                                        @if ($setting->default_layout)
                                            <a href="javascript:void(0)" onclick="layoutChange(0)"
                                                class="btn btn-danger">{{ __('inactivate') }}</a>
                                        @else
                                            <a href="javascript:void(0)" onclick="layoutChange(1)"
                                                class="btn btn-primary">{{ __('activate') }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0">{{ __('top_navigation_layout') }}</h5>
                                </div>
                                <img src="{{ asset('backend/image/setting/top-sidebarlayout.png') }}"
                                    class="card-img-top img-fluid w-250 h-auto" alt="top nav">
                                @if (userCan('setting.update'))
                                    <div class="card-body">
                                        @if ($setting->default_layout)
                                            <a href="javascript:void(0)" onclick="layoutChange(0)"
                                                class="btn btn-primary">{{ __('activate') }}</a>
                                        @else
                                            <a href="javascript:void(0)" onclick="layoutChange(1)"
                                                class="btn btn-danger">{{ __('inactivate') }}</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- landing page start --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title line-height-36">{{ __('Choose Landing Page') }}</h3>
        </div>
        <div class="card-body">
            <form class="form-horizontal" action="{{ route('settings.landingPage.update') }}" method="POST"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-sm-2">
                        <label class="image-container">
                            <input type="radio" name="landing_page" id="1" value="1"
                                {{ $setting->landing_page == 1 ? 'checked' : '' }}>
                            <img class="full-image" src="{{ asset('backend/image/landing-page-01.png') }}"
                                alt="">
                            <span class="checked-badge"></span>
                        </label>
                    </div>
                    <div class="col-sm-2">
                        <label class="image-container">
                            <input type="radio" name="landing_page" id="2" value="2"
                                {{ $setting->landing_page == 2 ? 'checked' : '' }}>
                            <img class="full-image" src="{{ asset('backend/image/landing-page-02.png') }}"
                                alt="">
                        </label>
                    </div>
                    <div class="col-sm-2">
                        <label class="image-container">
                            <input type="radio" name="landing_page" id="3" value="3"
                                {{ $setting->landing_page == 3 ? 'checked' : '' }}>
                            <img class="full-image" src="{{ asset('backend/image/landing-page-03.png') }}"
                                alt="">
                        </label>
                    </div>
                </div>
                <div class="row mt-3 mx-auto">
                    @if (userCan('setting.update'))
                        <button type="submit" class="btn btn-primary">
                            {{ __('update') }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- landing page end --}}

@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('backend/plugins/pickr') }}/classic.min.css" />
    <style>
        .image-container {
            height: 300px;
            overflow: hidden;
            position: relative;
            border: 2px solid #ced4da;
            cursor: pointer;
        }

        .image-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-left: 56px solid var(--main-color);
            border-bottom: 32px solid transparent;
            display: none;
        }

        .image-container input {
            display: none;
        }

        .image-container:has(input:checked) {
            border: 2px solid var(--main-color);
        }

        .image-container:has(input:checked)::after {
            display: block;
        }

        .full-image {
            width: 100%;
            height: auto;
            transition: transform 2s ease;
            display: block !important;
        }

        .image-container:hover .full-image {
            transform: translateY(calc(-100% + 300px));
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('backend/plugins/pickr') }}/pickr.min.js"></script>
    <script>
        // layout change function define
        function layoutChange(value) {
            $('#layout_mode').val(value)
            $('#layout_form').submit()
        }
        const colorPickers = [{
                default: '{{ $setting->sidebar_color }}',
                el: ".sidebar-bg-color-picker",
                input: '#sidebar_color_id',
                variable: '--sidebar-bg-color',
            },
            {
                default: '{{ $setting->sidebar_txt_color }}',
                el: ".sidebar-text-color-picker",
                input: '#sidebar_txt_color_id',
                variable: '--sidebar-txt-color',
            },
            {
                el: ".navbar-bg-color-picker",
                default: '{{ $setting->nav_color }}',
                variable: '--top-nav-bg-color',
                input: "#nav_color_id",
            },
            {
                el: ".navbar-text-color-picker",
                default: '{{ $setting->nav_txt_color }}',
                variable: '--top-nav-txt-color',
                input: "#nav_txt_color_id",
            },
            {
                el: ".accent-color-picker",
                default: '{{ $setting->accent_color }}',
                variable: '--accent-color',
                input: "#accent_color_id",
            },
            {
                el: ".main-color-picker",
                default: '{{ $setting->main_color }}',
                variable: '--main-color',
                input: "#main_color_id",
            },
            {
                el: ".frontend-primary-color",
                default: '{{ $setting->frontend_primary_color }}',
                variable: '--frontend-primary-color',
                input: "#frontend_primary_id",
            },
            {
                el: ".frontend-secondary-color",
                default: '{{ $setting->frontend_secondary_color }}',
                variable: '--frontend-secondary-color',
                input: "#frontend_secondary_id",
            },
        ]

        let root = document.documentElement;
        const defaultComponents = {
            preview: true,
            opacity: true,
            hue: true,

            interaction: {
                hex: true,
                rgba: true,
                cmyk: true,
                input: true,
                save: true,
                clear: true,
            }
        }
        // color picker call
        colorPickers.forEach(element => {
            const colorPicker = Pickr.create({
                el: element.el,
                theme: "classic",
                default: element.default,
                components: defaultComponents
            });

            colorPicker.on('change', (color, source, instance) => {
                setColor(color.toRGBA().toString(0), null, element.variable, element.input);
            }).on('save', (color, instance) => {
                let colorVal = color ? color.toHEXA().toString(0) : $(element.input).val();
                console.log(colorVal);
                setColor(colorVal, true, element.variable, element.input);
            });

            function setColor(color, instance, variable, input) {
                root.style.setProperty(variable, color);

                if (instance) {
                    $(input).val(color);
                    colorPicker.hide();
                }
            }
        });
    </script>
@endsection
