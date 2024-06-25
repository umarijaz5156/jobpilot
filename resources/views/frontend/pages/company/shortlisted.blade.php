@extends('frontend.layouts.app')

@section('title', __('applications'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div class="dashboard-right">
                        <div class="row d-flex justify-content-between p-2">
                            <div class="col-sm-12 col-md-6">
                                <h3 class="f-size-18 lh-1 mb-2 p-2">
                                    {{ __('short_listed') }}
                                    <span class="text-gray-400">({{ $applications->total() }})</span>
                                </h3>
                            </div>
                            <div class="col-sm-12 col-md-6 d-flex justify-content-between">

                                <div class="sidebar-open-nav ml-3">
                                    <i class="ph-list"></i>
                                </div>
                            </div>
                        </div>
                        <div class="db-job-card-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>{{ strtoupper(__('candidate')) }}</th>
                                        <th>{{ strtoupper(__('location')) }}</th>
                                        <th>{{ strtoupper(__('experience')) }}</th>
                                        <th>{{ strtoupper(__('action')) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($applications->count() > 0)
                                        @foreach ($applications as $application)
                                            <tr>
                                                <td>
                                                    <div class="iconbox-content">
                                                        <div class="post-info2 d-flex align-items-center">
                                                            <div class="candidate-img me-2">
                                                                <img src="{{ asset($application->candidate->photo) }}"
                                                                    alt="logo" width="48px" height="48px">
                                                            </div>
                                                            <div class="post-main-title">
                                                                <a href="{{ route('website.candidate.details', $application->candidate->user->username) }}"
                                                                    class="text-gray-900 f-size-16  ft-wt-5">
                                                                    {{ $application->candidate->user->name }}
                                                                </a>
                                                                <div class="body-font-4 text-gray-600 pt-2">
                                                                    <span class="info-tools rt-mr-8">
                                                                        {{ $application->candidate->profession ? ucfirst($application->candidate->profession->name) : '' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <svg width="20" height="20" viewBox="0 0 20 20"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M10 10.625C11.3807 10.625 12.5 9.50571 12.5 8.125C12.5 6.74429 11.3807 5.625 10 5.625C8.61929 5.625 7.5 6.74429 7.5 8.125C7.5 9.50571 8.61929 10.625 10 10.625Z"
                                                                stroke="#767F8C" stroke-width="1.3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M16.25 8.125C16.25 13.75 10 18.125 10 18.125C10 18.125 3.75 13.75 3.75 8.125C3.75 6.4674 4.40848 4.87769 5.58058 3.70558C6.75269 2.53348 8.3424 1.875 10 1.875C11.6576 1.875 13.2473 2.53348 14.4194 3.70558C15.5915 4.87769 16.25 6.4674 16.25 8.125V8.125Z"
                                                                stroke="#767F8C" stroke-width="1.3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        {{ $application->candidate->exact_location ? $application->candidate->exact_location: $application->candidate->full_address }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <svg class="f-size-20 rt-mr-4" width="20" height="20"
                                                            viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2.5 13.75L10 18.125L17.5 13.75" stroke="#5E6670"
                                                                stroke-width="1.3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path d="M2.5 10L10 14.375L17.5 10" stroke="#5E6670"
                                                                stroke-width="1.3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path d="M2.5 6.25L10 10.625L17.5 6.25L10 1.875L2.5 6.25Z"
                                                                stroke="#5E6670" stroke-width="1.3" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                        {{ $application->candidate->experience->name }}
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="db-job-btn-wrap d-flex justify-content-end">
                                                        <form
                                                            action="{{ route('company.shortlist.applied.job', $application->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="text-primary-500 btn-star hoverbg-primary-50 plain-button icon-button me-1">
                                                                <svg width="48" height="48" viewBox="0 0 48 48"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M24.4135 29.8812L29.1419 32.8769C29.7463 33.2598 30.4967 32.6903 30.3173 31.9847L28.9512 26.6108C28.9127 26.4611 28.9173 26.3036 28.9643 26.1564C29.0114 26.0092 29.0991 25.8783 29.2172 25.7787L33.4573 22.2496C34.0144 21.7859 33.7269 20.8613 33.0111 20.8148L27.4738 20.4554C27.3247 20.4448 27.1816 20.392 27.0613 20.3032C26.941 20.2144 26.8484 20.0932 26.7943 19.9538L24.7292 14.7532C24.673 14.6053 24.5732 14.4779 24.443 14.388C24.3127 14.2981 24.1582 14.25 24 14.25C23.8418 14.25 23.6873 14.2981 23.557 14.388C23.4268 14.4779 23.327 14.6053 23.2708 14.7532L21.2057 19.9538C21.1516 20.0932 21.059 20.2144 20.9387 20.3032C20.8184 20.392 20.6753 20.4448 20.5262 20.4554L14.9889 20.8148C14.2732 20.8613 13.9856 21.7859 14.5427 22.2496L18.7828 25.7787C18.9009 25.8783 18.9886 26.0092 19.0357 26.1564C19.0827 26.3036 19.0873 26.4611 19.0488 26.6108L17.7819 31.5945C17.5667 32.4412 18.4672 33.1246 19.1924 32.6651L23.5865 29.8812C23.71 29.8025 23.8535 29.7607 24 29.7607C24.1465 29.7607 24.29 29.8025 24.4135 29.8812V29.8812Z"
                                                                        fill="var(--primary-500)"
                                                                        stroke="var(--primary-500)"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <a id="candidate_profile_view"
                                                            href="{{ route('website.candidate.details', $application->candidate->user->username) }}"
                                                            class="btn btn-profile bg-gray-50 text-primary-500 rt-mr-8"
                                                            data-username="{{ $application->candidate->user->username }}">
                                                            <span class="button-text">
                                                                {{ __('view_profile') }}
                                                            </span>
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 12H19" stroke="var(--primary-500)"
                                                                    stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                                <path d="M12 5L19 12L12 19" stroke="var(--primary-500)"
                                                                    stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </a>
                                                        <button type="button" class="btn btn-icon"
                                                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M12 13.125C12.6213 13.125 13.125 12.6213 13.125 12C13.125 11.3787 12.6213 10.875 12 10.875C11.3787 10.875 10.875 11.3787 10.875 12C10.875 12.6213 11.3787 13.125 12 13.125Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 6.65039C12.6213 6.65039 13.125 6.14671 13.125 5.52539C13.125 4.90407 12.6213 4.40039 12 4.40039C11.3787 4.40039 10.875 4.90407 10.875 5.52539C10.875 6.14671 11.3787 6.65039 12 6.65039Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                                <path
                                                                    d="M12 19.6094C12.6213 19.6094 13.125 19.1057 13.125 18.4844C13.125 17.8631 12.6213 17.3594 12 17.3594C11.3787 17.3594 10.875 17.8631 10.875 18.4844C10.875 19.1057 11.3787 19.6094 12 19.6094Z"
                                                                    fill="#767F8C" stroke="#767F8C" />
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end company-dashboard-dropdown"
                                                            aria-labelledby="dropdownMenuButton1">
                                                            <li>
                                                                <a onclick="sendEmailCandidate('{{ $application->candidate->user->username }}')"
                                                                    class="dropdown-item" href="javascript:void(0)">
                                                                    <x-svg.mail-icon />
                                                                    {{ __('send_email') }}
                                                                </a>
                                                            </li>
                                                            @if ($application->candidate->cv && $application->candidate->cv_visibility)
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('website.candidate.download.cv', $application->candidate->id) }}">
                                                                        <x-svg.download-icon />
                                                                        {{ __('download_cv') }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <x-website.not-found />
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="rt-pt-12">
                            @if ($applications->total() > $applications->count())
                                <nav>
                                    {{ $applications->links('vendor.pagination.frontend') }}
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }} | {{ __('all_rights_reserved') }}
        </div>
    </div>

    <!-- ===================================== -->
    <div class="modal fade cadidate-modal" id="aemploye-profile" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog  modal-wrapper">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="text-center">
                        {{ __('save_to') }}
                    </h5>
                    <div class="row mb-5 border-top">
                        <div class="col-md-12" id="categoryList">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <x-website.modal.candidate-profile-modal />
@endsection

@section('frontend_links')
    <style>
        #aemploye-profile .modal-wrapper {
            width: 30% !important;
        }

        #aemploye-profile .modal-body {
            overflow-x: hidden !important;
            overflow-y: scroll !important;
        }
    </style>
@endsection

@section('script')
    <script type="text/javascript">
        $('.delete').on('click', function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `{{ __('are_you_sure_want_to_delete_this_item') }}`,
                    text: "{{ __('if_you_delete_this') }}, {{ __('it_will_be_gone_forever') }}",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });

        function JobForm() {
            $('#jobForm').submit();
        }

        $('#candidate_profile_view').on('click', function(e) {
            e.preventDefault();
            var username = $('#candidate_profile_view').data('username');
            showCandidateProfileModal(username);
        });

        function showCandidateProfileModal(username) {
            $.ajax({
                url: "{{ route('website.candidate.application.profile.details') }}",
                type: "GET",
                data: {
                    username: username
                },
                success: function(response) {
                    if (!response.success) {
                        if (response.redirect_url) {
                            return Swal.fire({
                                title: 'Oops...',
                                text: response.message,
                                icon: 'error',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: "{{ __('upgrade_plan') }}"
                            }).then((result) => {
                                if (result.value) {
                                    window.location.href = response.redirect_url;
                                }
                            })
                        } else {
                            return Swal.fire('Error', response.message, 'error');
                        }
                    }

                    let data = response.data;
                    let social = data.social_info

                    data.name ? $('.candidate-profile-info h2').html(data.name) : '';
                    data.candidate.photo ? $('#candidate_image').attr("src", data.candidate.photo) : '';
                    data.candidate.profession ? $('.candidate-profile-info h4').html(capitalizeFirstLetter(data
                        .candidate.profession.name)) : '';
                    data.candidate.bio ? $('.biography p').html(data.candidate.bio) : '';

                    if (data.candidate.status == 'available') {
                       $('.candidate-profile-info h6').removeClass('d-none');
                    }

                    // Social info
                    if (social.facebook || social.twitter || social.linkedin || social.youtube || social
                        .instagram) {
                        $('#candidate_social_profile_modal').show()
                        social.facebook ? $('.social-media ul li:nth-child(1)').attr("href", social.facebook) :
                            '';
                        social.twitter ? $('.social-media ul li:nth-child(2)').attr("href", social.twitter) :
                            '';
                        social.linkedin ? $('.social-media ul li:nth-child(3)').attr("href", social.linkedin) :
                            '';
                        social.instagram ? $('.social-media ul li:nth-child(4)').attr("href", social
                                .instagram) :
                            '';
                        social.youtube ? $('.social-media ul li:nth-child(5)').attr("href", social.youtube) :
                            '';

                    } else {
                        $('#candidate_social_profile_modal').hide()
                    }

                    // other info
                    data.candidate.birth_date ? $('#candidate_birth_date').html(data.candidate.birth_date) : '';
                    data.contact_info && data.contact_info.country ? $('#candidate_nationality').html(data.contact_info.country
                        .name) : ''
                    data.candidate.marital_status ? $('#candidate_marital_status').html(capitalizeFirstLetter(
                        data.candidate.marital_status)) : ''
                    data.candidate.gender ? $('#candidate_gender').html(capitalizeFirstLetter(data.candidate
                        .gender)) : ''
                    data.candidate.experience ? $('#candidate_experience').html(data.candidate.experience
                        .name) : ''
                    data.candidate.education ? $('#candidate_education').html(capitalizeFirstLetter(data
                        .candidate.education.name)) : ''

                    data.candidate.website ? $('#candidate_website').html(data.candidate.website) : ''
                    data.contact_info && data.contact_info.country ? $('#candidate_location').html(data.contact_info.country
                        .name) : ''
                    data.contact_info && data.contact_info.address ? $('#candidate_address').html(data.contact_info.address) : ''
                    data.contact_info && data.contact_info.phone ? $('#candidate_phone').html(data.contact_info.phone) : ''
                    data.contact_info && data.contact_info.secondary_phone ? $('#candidate_seconday_phone').html(data.contact_info
                        .secondary_phone) : ''
                    data.candidate.email ? $('#contact_info_email').html(data.candidate.email) : ''
                    if (data.candidate.cv_url) {
                        data.candidate.cv_url ? $('#candidate_cv').attr('href', data.candidate.cv_url) : ''
                    } else {
                        $('#download_cv').hide();
                    }

                    $('#candidate-profile-modal').modal('show');
                    toastr.success(response.profile_view_limit, 'Success')
                },
                error: function(error) {
                    Swal.fire('Error', 'Something Went Wrong!', 'error');
                }
            });
        }

        function capitalizeFirstLetter(string) {
            return string[0].toUpperCase() + string.slice(1);
        }

        function sendEmailCandidate(username) {
            $('#candidate_username').val(username)
            $('#send_message_modal').modal('show');
        }
    </script>
@endsection
