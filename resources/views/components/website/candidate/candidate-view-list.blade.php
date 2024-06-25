@props(['candidates'])

<div class="tab-pane " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
    @if ($candidates->count() > 0)
        @foreach ($candidates as $candidate)
            <div class="card jobcardStyle1 body-24 rt-mb-24 {{ !auth('user')->check() ? 'login_required' : '' }}">
                <div class="card-body">
                    <div class="rt-single-icon-box tw-flex-wrap tw-gap-3">
                        <div class="icon-thumb">
                            <div class="profile-image position-relative">
                                <img src="{{ asset($candidate->photo) }}" alt="{{ __('candidate_image') }}" class="object-fit-contain">

                                @if ($candidate->status == 'available')
                                    <span class="available-alert">
                                        <svg class="circle" width="14" height="14" viewBox="0 0 14 14"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="7" cy="7" r="6" fill="#2ecc71"
                                                stroke="white" stroke-width="2">
                                            </circle>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="iconbox-content !tw-m-0">
                            <div class="post-info2">
                                <div class="post-main-title">
                                    @if (auth('user')->check())
                                        <a href="javascript:void(0)" onclick="showCandidateProfileModal('{{ $candidate->user->username }}')">
                                            {{ $candidate->user->name }}
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" class="login_required">
                                            {{ maskFullName($candidate->user->name) }}
                                        </a>
                                    @endif

                                </div>
                                <span class="loacton text-gray-400 ">
                                    {{ $candidate->profession ? $candidate->profession->name : '' }}
                                </span>
                                <div class="body-font-4 text-gray-600 tw-pt-1.5 tw-flex tw-flex-wrap tw-gap-2 tw-items-center">
                                    <span class="info-tools !tw-flex tw-gap-0.5 tw-items-center">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M15.75 7.5C15.75 12.75 9 17.25 9 17.25C9 17.25 2.25 12.75 2.25 7.5C2.25 5.70979 2.96116 3.9929 4.22703 2.72703C5.4929 1.46116 7.20979 0.75 9 0.75C10.7902 0.75 12.5071 1.46116 13.773 2.72703C15.0388 3.9929 15.75 5.70979 15.75 7.5Z"
                                                stroke="#939AAD" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M9 9.75C10.2426 9.75 11.25 8.74264 11.25 7.5C11.25 6.25736 10.2426 5.25 9 5.25C7.75736 5.25 6.75 6.25736 6.75 7.5C6.75 8.74264 7.75736 9.75 9 9.75Z"
                                                stroke="#939AAD" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        {{ $candidate->country }}
                                    </span>
                                    <span class="info-tools !tw-flex tw-gap-0.5 tw-items-center">
                                        <i class="ph-suitcase-simple"></i>
                                        @if ($candidate->experience)
                                            {{ $candidate->experience ? $candidate->experience->name : '' }}
                                        @else
                                            0
                                        @endif {{ __('years_experience') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="iconbox-extra align-self-center">
                            <div>
                                @if (auth('user')->check())
                                    @if (auth()->user()->status)
                                        <!-- || -->
                                        <!-- Check auth candidate account is active -->
                                        <a onclick="showCandidateProfileModal('{{ $candidate->user->username }}')"
                                            href="javascript:void(0);" class="btn btn-primary2-50">
                                            <span class="button-content-wrapper ">
                                                <span class="button-icon align-icon-right">
                                                    <i class="ph-arrow-right"></i>
                                                </span>
                                                <span class="button-text">
                                                    {{ __('view_profile') }}
                                                </span>
                                            </span>
                                        </a>
                                    @else
                                        <!-- auth candidate account isnot active -->
                                        <a href="javascript:void(0);"
                                            onclick="toastr.warning('{{ __('your_account_is_not_active_please_wait_until_the_account_is_activated_by_admin') }}')"
                                            class="btn btn-primary2-50">
                                            <span class="button-content-wrapper ">
                                                <span class="button-icon align-icon-right">
                                                    <i class="ph-arrow-right"></i>
                                                </span>
                                                <span class="button-text">
                                                    {{ __('view_profile') }}
                                                </span>
                                            </span>
                                        </a>
                                    @endif
                                @else
                                    <a href="javascript:void(0);" class="btn btn-primary2-50 login_required">
                                        <span class="button-content-wrapper ">
                                            <span class="button-icon align-icon-right">
                                                <i class="ph-arrow-right"></i>
                                            </span>
                                            <span class="button-text">
                                                {{ __('view_profile') }}
                                            </span>
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-md-12">
            <div class="card text-center">
                <x-not-found message="{{ __('no_data_found') }}" />
            </div>
        </div>
    @endif
    @if (request('perpage') != 'all' && $candidates->total() > $candidates->count())
        <div class="rt-pt-30">
            <nav>
                {{ $candidates->links('vendor.pagination.frontend') }}
            </nav>
        </div>
    @endif
</div>
