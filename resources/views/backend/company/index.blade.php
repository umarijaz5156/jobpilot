@extends('backend.layouts.app')
@section('title')
    {{ __('company_list') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title line-height-36">{{ __('company_list') }}</h3>

                           <div>
                                @if (userCan('company.create'))
                                    <a href="{{ route('company.create') }}"
                                        class="btn bg-primary"><i
                                            class="fas fa-plus mr-1"></i> {{ __('create') }}
                                    </a>
                                @endif
                                @if (request('keyword') || request('ev_status') || request('sort_by') || request('organization_type') || request('industry_type'))
                                    <a href="{{ route('company.index') }}"
                                        class="btn bg-danger"><i
                                            class="fas fa-times"></i>&nbsp; {{ __('clear') }}
                                    </a>
                                @endif
                           </div>
                        </div>
                    </div>
                    <div class="p-3 text-right">
                        <button id="scrapeJobsButton" class="btn btn-primary">
                            Start Scraping Jobs
                        </button>

                        <!-- Loading indicator, initially hidden -->
                        <div id="loadingIndicator" style="display: none;color:rgb(24, 24, 104);">Loading...</div>

                        <!-- Message to show the result -->
                        <div id="resultMessage" style="display: none; color:#28a745;"></div>

                    </div>
                    {{-- Filter  --}}
                    <form id="formSubmit"  action="{{ route('company.index') }}" method="GET" onchange="this.submit();">
                        <div class="card-body border-bottom row">
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('search') }}</label>
                                <input name="keyword" type="text" placeholder="{{ __('search') }}" class="form-control" value="{{ request('keyword') }}">
                            </div>
                            <div class="col-xl-2 col-md-6 col-12">
                                <label>{{ __('organization_type') }}</label>
                                <select name="organization_type" class="form-control select2bs4">
                                    <option value="">
                                        {{ __('all') }}
                                    </option>
                                    @foreach ($organization_types as $organization_type)
                                        <option {{ request('organization_type') == $organization_type->id ? 'selected' : '' }} value="{{ $organization_type->id }}">
                                            {{ $organization_type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-6 col-12">
                                <label>{{ __('industry_type') }}</label>
                                <select name="industry_type" class="form-control select2bs4">
                                    <option value="">
                                        {{ __('all') }}
                                    </option>
                                    @foreach ($industry_types as $industry_type)
                                        <option {{ request('industry_type') == $industry_type->id ? 'selected' : '' }} value="{{ $industry_type->id }}">
                                            {{ $industry_type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-6 col-12">
                                <label>{{ __('email_verification') }}</label>
                                <select name="ev_status" class="form-control select2bs4">
                                    <option value="">
                                        {{ __('all') }}
                                    </option>
                                    <option {{ request('ev_status') == 'true' ? 'selected' : '' }} value="true">
                                        {{ __('verified') }}
                                    </option>
                                    <option {{ request('ev_status') == 'false' ? 'selected' : '' }} value="false">
                                        {{ __('not_verified') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-xl-3 col-md-6 col-12">
                                <label>{{ __('sort_by') }}</label>
                                <select name="sort_by" class="form-control select2bs4">
                                    <option {{ !request('sort_by') || request('sort_by') == 'latest' ? 'selected' : '' }}
                                        value="latest" selected>
                                        {{ __('latest') }}
                                    </option>
                                    <option {{ request('sort_by') == 'oldest' ? 'selected' : '' }} value="oldest">
                                        {{ __('oldest') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="card-body table-responsive p-0">
                        @include('backend.layouts.partials.message')
                        <table class="ll-table table table-hover text-nowrap">
                            <thead>
                                <tr class="text-center">
                                    <th>{{ __('company') }}</th>
                                    <th>{{ __('active') }} {{ __('job') }}</th>
                                    <th>{{ __('organization') }}/{{ __('country') }}</th>
                                    <th>{{ __('establishment_date') }}</th>
                                    @if (userCan('company.update'))
                                        <th>{{ __('account') }} {{ __('status') }}</th>
                                    @endif
                                    {{-- @if (userCan('company.update'))
                                        <th>{{ __('email_verification') }}</th>
                                    @endif
                                    @if (userCan('company.update'))
                                        <th>{{ __('profile') }} {{ __('status') }}</th>
                                    @endif --}}
                                    @if (userCan('company.update') || userCan('compnay.delete'))
                                        <th width="12%">
                                            {{ __('action') }}
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                    <tr>
                                        <td>
                                            <a href='{{ route('company.show', $company->id) }}' class="company">
                                                <img src="{{ $company->logo_url }}" alt="Logo">
                                                <div>
                                                    <h4>{{ $company->user->name }}
                                                        @if($company->is_profile_verified)
                                                        <svg
                                                            style="width: 24px ; height: 24px ; color: green"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        @endif
                                                    </h4>
                                                    <p>{{ $company->user->email }}</p>
                                                </div>
                                                <div>


                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('company.show', $company->id) }}">
                                                {{ $company->active_jobs }} {{ __("active_jobs") }}
                                            </a>
                                        </td>
                                        <td>
                                            <p class="highlight">{{ $company->organization->name }}</p>
                                            <p class="highlight mb-0"><x-svg.table-country />{{ $company->country }}</p>
                                        </td>
                                        <td>
                                            <p class="highlight mb-0">{{ $company->establishment_date ? date('j F, Y', strtotime($company->establishment_date)):'-' }}</p>
                                        </td>
                                        @if (userCan('company.update'))
                                            <td tabindex="0">
                                                <a href="#" class="active-status">
                                                    <label class="switch ">
                                                        <input data-id="{{ $company->user_id }}" type="checkbox"
                                                            class="success status-switch"
                                                            {{ $company->user->status == 1 ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <p style="min-width:70px;" class="{{ $company->user->status == 1 ? 'active' : '' }}" id="status_{{ $company->user_id }}">{{ $company->user->status == 1 ? __('activated') : __('deactivated') }}</p>
                                                </a>
                                            </td>
                                        @endif
                                        {{-- @if (userCan('company.update'))
                                            <td tabindex="0">
                                                <a href="#" class="active-status">
                                                    <label class="switch ">
                                                        <input data-userid="{{ $company->user_id }}" type="checkbox"
                                                            class="success email-verification-switch"
                                                            {{ $company->user->email_verified_at ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <p style="min-width:70px" class="{{ $company->user->email_verified_at ? 'active' : '' }}" id="verification_status_{{ $company->user_id }}">{{ $company->user->email_verified_at ? __('verified') : __('unverified') }}</p>
                                                </a>
                                            </td>
                                        @endif --}}
                                        {{-- @if (userCan('company.update') || userCan('compnay.delete'))
                                            <td tabindex="0">
                                                <a href="#" class="active-status">
                                                    <label class="switch ">
                                                        <input data-companyid="{{ $company->id }}" type="checkbox"
                                                               class="success profile-verification-switch"
                                                            {{ $company->is_profile_verified ? 'checked' : '' }}>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <p style="min-width:70px" class="{{ $company->is_profile_verified ? 'active' : '' }}" id="profile_status_{{ $company->id }}">{{ $company->is_profile_verified ? __('verified') : __('unverified') }}</p>
                                                </a>
                                                <div class="mt-2">
                                                    <a href="{{route('admin.company.documents',$company)}}">View Documents</a>
                                                </div>
                                            </td>
                                        @endif --}}

                                    @if (userCan('company.update') || userCan('compnay.delete'))
                                            <td>
                                                @if (userCan('company.view'))
                                                <a href="{{ route('company.report', $company->id) }}"
                                                    class="btn ll-btn ll-border-none">
                                                    {{__('Report')}}
                                            <x-svg.table-btn-arrow />
                                                </a>

                                                    <a href="{{ route('company.show', $company->id) }}"
                                                        class="btn ll-btn ll-border-none">
                                                        {{__('view_profile')}}
                                                <x-svg.table-btn-arrow />
                                                    </a>
                                                @endif
                                                @if (userCan('company.update'))
                                                    <a href="{{ route('company.edit', $company->id) }}"
                                                        class="btn ll-p-0">
                                                        <x-svg.table-edit />
                                                    </a>
                                                @endif
                                                @if (userCan('company.delete'))
                                                    <form action="{{ route('company.destroy', $company->id) }}"
                                                        method="POST" class="d-inline">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button
                                                            onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                            class="btn ll-p-0"><x-svg.table-delete/></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <p>{{ __('no_data_found') }}...</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($companies->count())
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $companies->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
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

@section('script')
<script>
    $(document).ready(function() {
        // When the button is clicked
        $('#scrapeJobsButton').click(function() {
            // Show loading indicator
            $('#loadingIndicator').show();
            $('#resultMessage').hide();  // Hide any previous messages

            // Define routes and their respective display messages
            const scrapingRoutes = [
                { route: "{{ route('auto.centralCoast') }}", message: "Scraping Central Coast Jobs..." },
                { route: "{{ route('auto.CanterburyBankstown') }}", message: "Scraping Canterbury Bankstown Jobs..." },
                { route: "{{ route('auto.ByronShire') }}", message: "Scraping Byron Shire Jobs..." },
                { route: "{{ route('auto.BulokeShire') }}", message: "Scraping Buloke Shire Jobs..." },
                { route: "{{ route('auto.BouliaShire') }}", message: "Scraping Boulia Shire Jobs..." },
                { route: "{{ route('auto.BrokenHillCity') }}", message: "Scraping Broken Hill City Shire Jobs..." },
                { route: "{{ route('auto.BlueMountainsCity') }}", message: "Scraping Blue Mountains City Council Jobs..." },
                { route: "{{ route('auto.BarklyRegional') }}", message: "Scraping Barkly Regional Council Jobs..." },
                { route: "{{ route('auto.BananaShire') }}", message: "Scraping  Banana Shire Council Jobs..." },
                { route: "{{ route('auto.AliceSprings') }}", message: "Scraping  Alice Springs Town Counncil Jobs..." },
                { route: "{{ route('auto.CardiniaShire') }}", message: "Scraping  Cardinia Shire Council Jobs..." },
                { route: "{{ route('auto.CentralLand') }}", message: "Scraping  Central Land Council Jobs..." },
                { route: "{{ route('auto.CityBallarat') }}", message: "Scraping  City Ballarat Council Jobs..." },
                { route: "{{ route('auto.CitySalisbury') }}", message: "Scraping  City Salisbury Council Jobs..." },
                { route: "{{ route('auto.ChartersTowers') }}", message: "Scraping  Charters Towers Regional Council Jobs..." },
                { route: "{{ route('auto.GreaterBendigo') }}", message: "Scraping  City of Greater Bendigo Council Jobs..." },
                { route: "{{ route('auto.GreaterDandenong') }}", message: "Scraping  City of Greater Dandenong Council Jobs..." },
                { route: "{{ route('auto.GreaterGeraldton') }}", message: "Scraping  City of Greater Geraldton Council Jobs..." },
                { route: "{{ route('auto.CityHobart') }}", message: "Scraping  City of Hobart Council Jobs..." },
                { route: "{{ route('auto.CityPortPhillip') }}", message: "Scraping  City of Port Phillip Council Jobs..." },
                { route: "{{ route('auto.ClarenceValley') }}", message: "Scraping  Clarence Valley Council Jobs..." },
                { route: "{{ route('auto.CookShire') }}", message: "Scraping  Cook Shire Council Jobs..." },
                { route: "{{ route('auto.CumberlandCity') }}", message: "Scraping  Cumberland City Council Jobs..." },
                { route: "{{ route('auto.FairfieldCity') }}", message: "Scraping  Fairfield City Council Jobs..." },
                { route: "{{ route('auto.FlindersShire') }}", message: "Scraping  Flinders Shire Council Jobs..." },
                { route: "{{ route('auto.GlenInnesSevern') }}", message: "Scraping  Glen Innes Severn Council Jobs..." },
                { route: "{{ route('auto.GoulburnMulwaree') }}", message: "Scraping  Goulburn Mulwaree Council Jobs..." },
                { route: "{{ route('auto.GriffithCity') }}", message: "Scraping  Griffith City Council Jobs..." },
                { route: "{{ route('auto.GympieRegional') }}", message: "Scraping  Gympie Regional Council Jobs..." },
                { route: "{{ route('auto.HinchinbrookShire') }}", message: "Scraping  Hinchinbrook Shire Council Jobs..." },
                { route: "{{ route('auto.HornsbyShire') }}", message: "Scraping  Hornsby Shire Council Jobs..." },
                { route: "{{ route('auto.LeetonShire') }}", message: "Scraping  Leeton Shire Council Jobs..." },
                { route: "{{ route('auto.LivingstoneShire') }}", message: "Scraping  Livingstone Shire Council Jobs..." },
                { route: "{{ route('auto.LoddonShire') }}", message: "Scraping  Loddon Shire Council Jobs..." },
                { route: "{{ route('auto.MansfieldShire') }}", message: "Scraping  Mansfield Council Jobs..." },
                { route: "{{ route('auto.MountAlexanderShire') }}", message: "Scraping  MountAlexander Shire Council Jobs..." },
                { route: "{{ route('auto.MurrayRiver') }}", message: "Scraping  Murray River Council Jobs..." },
                { route: "{{ route('auto.MurrindindiShire') }}", message: "Scraping  Murrindindi Shire Council Jobs..." },
                { route: "{{ route('auto.MuswellbrookShire') }}", message: "Scraping  Muswellbrook Shire Council Jobs..." },
                { route: "{{ route('auto.NorthernBeaches') }}", message: "Scraping  Northern Beaches Council Jobs..." },
                { route: "{{ route('auto.ParkesShire') }}", message: "Scraping  Parkes Shire Council Jobs..." },
                { route: "{{ route('auto.ParooShire') }}", message: "Scraping  Paroo Shire Council Jobs..." },
                { route: "{{ route('auto.RichmondValley') }}", message: "Scraping  Richmond Valley Council Jobs..." },
                { route: "{{ route('auto.RuralCityWangaratta') }}", message: "Scraping  Rural City Wangaratta Council Jobs..." },
                { route: "{{ route('auto.RoperGulfRegional') }}", message: "Scraping  Roper Gulf Regional Council Jobs..." },
                { route: "{{ route('auto.ShireAugustaMargaretRiver') }}", message: "Scraping  Shire Augusta Margaret River Council Jobs..." },
                { route: "{{ route('auto.ShireEastPilbara') }}", message: "Scraping  Shire East Pilbara Council Jobs..." },
                { route: "{{ route('auto.ShireNgaanyatjarraku') }}", message: "Scraping  Shire Ngaanyatjarraku Council Jobs..." },
                { route: "{{ route('auto.SomersetRegional') }}", message: "Scraping  Somerset Regional Council Jobs..." },
                { route: "{{ route('auto.SouthernDownsRegional') }}", message: "Scraping  Southern Downs Regional Council Jobs..." },





            ];

            let errorCouncils = []; // Array to store councils that encounter errors


            // Function to start scraping tasks
            function scrapeJobs(index) {
                if (index < scrapingRoutes.length) {
                    // Show message for the current task
                    $('#resultMessage').text(scrapingRoutes[index].message);
                    $('#resultMessage').show();

                    // AJAX request for the current route
                    $.ajax({
                        url: scrapingRoutes[index].route,  // The current scraping route
                        method: 'GET',
                        success: function(response) {
                            // Show completion message for current job scraping
                            $('#resultMessage').text(response.message + ' completed!');

                            // Recursively trigger the next scraping task after a delay
                            setTimeout(function() {
                                scrapeJobs(index + 1);  // Move to the next route
                            }, 3000);  // Optional delay before starting the next scraping
                        },
                        error: function(xhr, status, error) {
                            // Log the council name with an error
                            errorCouncils.push(scrapingRoutes[index].message);

                            // Continue to the next scraping task
                            setTimeout(function() {
                                scrapeJobs(index + 1);  // Move to the next route
                            }, 3000);  // Optional delay
                        }
                    });
                } else {
                    // All scraping tasks are completed
                    $('#loadingIndicator').hide();

                    // Display summary message
                    if (errorCouncils.length > 0) {
                        $('#resultMessage').html(
                            'Scraping completed with errors. The following councils encountered issues:<br>' +
                            errorCouncils.join('<br>')
                        );
                    } else {
                        $('#resultMessage').text('All scraping tasks completed successfully!');
                    }
                }
            }

            // Start scraping from the first route
            scrapeJobs(0);
        });
    });
</script>



    <script>
        $('.status-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('company.status.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            if (status == 1) {
                $(`#status_${id}`).text("{{ __('activated') }}")
            }else{
                $(`#status_${id}`).text("{{ __('deactivated') }}")
            }
        });
        $('.email-verification-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('userid');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('company.verify.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            if (status == 1) {
                $(`#verification_status_${id}`).text("{{ __('verified') }}")
            }else{
                $(`#verification_status_${id}`).text("{{ __('unverified') }}")
            }
        });

        $('.profile-verification-switch').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('companyid');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('company.profile.verify.change') }}',
                data: {
                    'status': status,
                    'id': id
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            if (status == 1) {
                $(`profile_status_${id}`).text("{{ __('verified') }}")
            }else{
                $(`profile_status_${id}`).text("{{ __('unverified') }}")
            }
        });
    </script>
@endsection
