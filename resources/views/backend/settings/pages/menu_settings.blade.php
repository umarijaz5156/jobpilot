@extends('backend.settings.setting-layout')

@section('title')
    {{ __('menu_settings') }}
@endsection

@section('breadcrumbs')
    <div class="row mb-2 mt-4">
        <div class="col-sm-6">
            <h1 class="m-0">
                {{ __('menu_settings') }}
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('home') }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    {{ __('settings') }}
                </li>
                <li class="breadcrumb-item active">
                    {{ __('menu_settings') }}
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('website-settings')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning mb-3">
                {{ __('for_internal_links_use_your_link_as_the_starting_point') }}. <br>
                {{ __('for_external_links_begin_with_httpswwwyour_linkcom') }}.
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-md-flex justify-content-between">
                        <div class="row">
                            <h3 class="col-12 col-md-8 card-title line-height-36">
                                {{ __('menu_settings') }}
                            </h3>
                            <div class="d-flex align-items-center col-md-4">
                                <a href="{{ route('menu-settings.index', ['for' => 'public']) }}" class="a-color">
                                    <div class="filtertags close-tag pointer mr-2">
                                        <div
                                            class="single-tag {{ request('for') == 'public' || !request('for') ? 'single-tag-active' : '' }}">
                                            {{ __('everyone') }}
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('menu-settings.index', ['for' => 'employee']) }}" class="a-color">
                                    <div class="filtertags close-tag pointer mr-2">
                                        <div
                                            class="single-tag {{ request('for') == 'employee' ? 'single-tag-active' : '' }}">
                                            {{ __('employer') }}
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('menu-settings.index', ['for' => 'candidate']) }}" class="a-color">
                                    <div class="filtertags close-tag pointer mr-2">
                                        <div
                                            class="single-tag {{ request('for') == 'candidate' ? 'single-tag-active' : '' }}">
                                            {{ __('candidate') }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="mt-2 mt-md-0">
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    {{ __('title') }}
                                </th>
                                <th>
                                    {{ __('url') }}
                                </th>
                                <th width="10%">
                                    {{ __('menu_show_frontend') }}
                                </th>
                                <th width="15%">
                                    {{ __('action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @forelse ($menus as $menu)
                                <tr data-id="{{ $menu->id }}">
                                    <td class="vertical-middle">
                                        <h5>
                                            {{ $menu->title }}
                                        </h5>
                                        <div>
                                            @foreach ($menu->translations as $translation)
                                                @if (app()->getLocale() == $translation->locale)
                                                @else
                                                    <span class="d-block">
                                                        <b>
                                                            {{ getLanguageByCodeInLookUp($translation->locale,$app_languages) }}
                                                        </b>:
                                                        {{ $translation->title }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="vertical-middle">
                                        <a href="{{ $menu->url }}" target="_blank">
                                            {{ ucfirst(Str::replace('_', ' ', $menu->url)) }}
                                        </a>
                                    </td>
                                    <td class="vertical-middle" tabindex="0">
                                        @if (userCan('menu-setting.update'))
                                            <form id="statusChangeForm{{ $menu->id }}"
                                                action="{{ route('menu-setting.status.change', $menu->id) }}"
                                                method="POST">
                                                @csrf
                                                <label class="switch">
                                                    <input name="status" value="1" data-id="{{ $menu->id }}"
                                                        type="checkbox" class="success status-switch"
                                                        {{ $menu->status == 1 ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="vertical-middle">
                                        @if (userCan('menu-setting.update'))
                                            <div class="btn btn-success mt-0">
                                                <i class="fas fa-hand-rock"></i>
                                            </div>
                                        @endif
                                        @if (userCan('menu-setting.update'))
                                            <a href="{{ route('menu-settings.edit', $menu->id) }}"
                                                class="btn btn-info mt-0 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if (userCan('menu-setting.delete'))
                                            @if (!$menu->default)
                                                <form action="{{ route('menu-settings.destroy', $menu->id) }}"
                                                    class="d-inline" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button data-toggle="tooltip" data-placement="top"
                                                        title="{{ __('delete') }}"
                                                        onclick="return confirm('{{ __('are_you_sure_want_to_delete_this_item') }}');"
                                                        class="btn bg-danger"><i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <x-admin.not-found word="menu" route="" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center align-items-center {{ $menus->total() > 20 ? 'mt-3' : '' }}">
                    {{ $menus->links() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if (!empty($menu_setting))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">
                            {{ __('edit') }}
                        </h3>
                        <a href="{{ route('menu-settings.index') }}"
                            class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                            <i class="fas fa-arrow-left mr-1"></i>
                            {{ __('back') }}
                        </a>
                    </div>
                    <div class="card-body">
                        @if (userCan('menu-setting.update'))
                            <form class="form-horizontal" action="{{ route('menu-settings.update', $menu_setting->id) }}"
                                method="POST">
                                @method('PUT')
                                @csrf
                                @foreach ($app_languages as $key => $language)
                                    @php
                                        $label = __('title') . ' ' . getLanguageByCodeInLookUp($language->code,$app_languages);
                                        $title = "title_{$language->code}";
                                        $data = $menu_setting->translations->where('locale', $language->code)->first();
                                        $value = $data ? $data->title : '';
                                    @endphp
                                    <div class="form-group">
                                        <x-forms.label :name="$label" for="title" :required="true" />
                                        <input id="title" type="text" name="{{ $title }}"
                                            placeholder="{{ __('title') }}" value="{{ $value }}"
                                            class="form-control @if ($errors->has($title)) is-invalid @endif">
                                        @if ($errors->has($title))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first($title) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="form-group">
                                    <x-forms.label name="url" for="url" :required="true" />
                                    <input id="url" {{ $menu_setting->default ? 'disabled' : '' }} type="text"
                                        name="url" placeholder="{{ __('enter') }} {{ __('url') }}"
                                        value="{{ $menu_setting->url }}"
                                        class="form-control @error('url') is-invalid @enderror">
                                    @error('url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    @php
                                        $value = $menu_setting->for;
                                        $arrayValue = json_decode($value, true);
                                    @endphp
                                    <x-forms.label name="visibility_this_menu" for="eligibility" :required="true" />
                                    <div>
                                        <!-- Checkboxes for "everyone", "employer", and "candidate" -->
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="public"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button edit {{ in_array('public', $arrayValue) ? 'selected' : '' }}"
                                                data-value="public">{{ __('everyone') }}</span>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="employee"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button edit {{ in_array('employee', $arrayValue) ? 'selected' : '' }}"
                                                data-value="employee">{{ __('employer') }}</span>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="candidate"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button edit {{ in_array('candidate', $arrayValue) ? 'selected' : '' }}"
                                                data-value="candidate">{{ __('candidate') }}</span>
                                        </label>
                                    </div>
                                    @error('eligibility')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="show_in_the_menu" for="status" :required="false" />
                                    <div>
                                        <label class="switch">
                                            <input value="1" @checked($menu_setting->status) name="status"
                                                type="checkbox" class="success status-switch">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group m-auto">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus mr-1"></i>
                                        {{ __('save') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <p>{{ __('dont_have_permission') }}</p>
                        @endif
                    </div>
                </div>
            @endif
            @if (empty($menu_setting))
                @php
                    $arrayValue = [''];
                @endphp
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">
                            {{ __('create') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        @if (userCan('menu-setting.create'))
                            <form class="form-horizontal" action="{{ route('menu-settings.store') }}" method="POST">
                                @csrf
                                @foreach ($app_languages as $key => $language)
                                    @php
                                        $label = __('title') . ' ' . getLanguageByCodeInLookUp($language->code,$app_languages);
                                        $title = "title_{$language->code}";
                                    @endphp
                                    <div class="form-group">
                                        <x-forms.label :name="$label" for="title" :required="true" />
                                        <input id="title" type="text" name="{{ $title }}"
                                            placeholder="{{ __('title') }}" value="{{ old($title) }}"
                                            class="form-control @if ($errors->has($title)) is-invalid @endif">
                                        @if ($errors->has($title))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first($title) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="form-group">
                                    <x-forms.label name="url" for="url" :required="true" />
                                    <input id="url" type="text" name="url"
                                        placeholder="{{ __('enter') }} {{ __('url') }}"
                                        value="{{ old('url') }}"
                                        class="form-control @error('url') is-invalid @enderror">
                                    @error('url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <x-forms.label name="visibility_this_menu" for="eligibility" :required="true" />
                                    <div>
                                        <!-- Checkboxes for "everyone", "employer", and "candidate" -->
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="public"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button {{ in_array('public', request('eligibility', [])) ? 'selected' : '' }}"
                                                data-value="public">{{ __('everyone') }}</span>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="employee"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button {{ in_array('employee', request('eligibility', [])) ? 'selected' : '' }}"
                                                data-value="employee">{{ __('employer') }}</span>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="eligibility[]" value="candidate"
                                                class="hidden-checkbox" multiple>
                                            <span
                                                class="select-button {{ in_array('candidate', request('eligibility', [])) ? 'selected' : '' }}"
                                                data-value="candidate">{{ __('candidate') }}</span>
                                        </label>
                                    </div>
                                    @error('eligibility')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- Display selected options -->
                                <div class="form-group">
                                    <x-forms.label name="show_in_the_menu" for="status" :required="false" />
                                    <div>
                                        <label class="switch">
                                            <input value="1" name="status" type="checkbox"
                                                class="success status-switch">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group m-auto">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus mr-1"></i>
                                        {{ __('save') }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <p>{{ __('dont_have_permission') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            // Function to handle the button click to get selected options (Edit Page)
            $('#get-selected-options-edit').click(function() {
                const selectedOptions = [];
                $('input[name="eligibility[]"]:checked').each(function() {
                    selectedOptions.push($(this).val());
                });
            });

            // Function to update the display of selected options when the page loads (Edit Page)
            function updateSelectedOptionsEdit() {
                const selectedOptions = [];
                $('input[name="eligibility[]"]:checked').each(function() {
                    selectedOptions.push($(this).val());
                });
            }

            // Check which checkboxes are selected when the page loads (Edit Page)

            $('input[name="eligibility[]"]').each(function() {
                const value = $(this).val();
                if ($.inArray(value, `<?php echo json_encode($arrayValue); ?>`) !== -1) {
                    $(this).prop('checked', true);
                }
            });


            // Update the display of selected options when the page loads (Edit Page)
            updateSelectedOptionsEdit();

            // Handle checkbox clicks to update the display of selected options (Edit Page)
            $('input[name="eligibility[]"]').change(function() {
                updateSelectedOptionsEdit();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to handle the button click to get selected options
            $('#get-selected-options').click(function() {
                const selectedOptions = [];
                $('input[name="eligibility[]"]:checked').each(function() {
                    selectedOptions.push($(this).val());
                });


            });

            // Function to update the display of selected options when the page loads
            function updateSelectedOptions() {
                const selectedOptions = [];
                $('input[name="eligibility[]"]:checked').each(function() {
                    selectedOptions.push($(this).val());
                });

                // Remove the "selected" class from all buttons first
                $('.select-button').removeClass('selected');

                // Add the "selected" class to the appropriate buttons
                selectedOptions.forEach(function(option) {
                    $(`.select-button[data-value="${option}"]`).addClass('selected');
                });

            }

            // Check which checkboxes are selected when the page loads
            updateSelectedOptions();

            // Handle checkbox clicks to update the display of selected options
            $('input[name="eligibility[]"]').change(function() {
                updateSelectedOptions();
            });
        });
    </script>
    <script>
        $('.status-switch').on('change', function() {
            var id = $(this).data('id');
            $('#statusChangeForm' + id).submit();
        });
    </script>
    <script>
        $(function() {
            $("#sortable").sortable({
                items: 'tr',
                cursor: 'move',
                opacity: 0.4,
                scroll: false,
                dropOnEmpty: false,
                update: function() {
                    sendTaskOrderToServer('#sortable tr');
                },
                classes: {
                    "ui-sortable": "highlight"
                },
            });
            $("#sortable").disableSelection();

            function sendTaskOrderToServer(selector) {
                var order = [];
                $(selector).each(function(index, element) {
                    order.push({
                        id: $(this).attr('data-id'),
                        position: index + 1
                    });
                });

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('menu-setting.sort-able') }}",
                    data: {
                        order: order,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success');
                    }
                });
            }
        });
    </script>
@endsection

@section('style')
    <style>
        /* Hide the default checkbox styles */
        .hidden-checkbox {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            margin: 0;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        /* Style the select buttons */
        .select-button {
            padding: 6px 16px 6px 16px;
            margin: 5px;
            display: inline-block;
            font-size: 13px;
            font-weight: 400;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 30px;
            /* Adjust the value to control the border's roundness */
        }

        /* Style the select buttons */
        .select-buttons {
            padding: 6px 16px 6px 16px;
            margin: 5px;
            display: inline-block;
            font-size: 13px;
            font-weight: 400;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 30px;
            /* Adjust the value to control the border's roundness */
        }

        /* Style for the selected button */
        .select-button.selected {
            background-color: #0a65cc;
            color: #fff;
        }

        .select-buttons.selected {
            background-color: #0a65cc;
            color: #fff;
        }

        .vertical-middle {
            vertical-align: middle !important;
        }

        .vertical-middle a {
            text-transform: lowercase;
        }

        .-mt-6px {
            margin-top: -6px !important;
        }

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

        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 19px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            display: none;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 3px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input.success:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(15px);
            -ms-transform: translateX(15px);
            transform: translateX(15px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
