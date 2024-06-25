@extends('frontend.layouts.app')

@section('title', __('bookmarks'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <x-website.company.sidebar />

                <div class="col-lg-9">
                    <div class="dashboard-right tw-ps-0 lg:tw-ps-3">
                        <div class="row d-flex tw-flex-wrap justify-content-between p-2">
                            <div class="col-sm-12 col-md-6">
                                <h3 class="f-size-18 lh-1 mb-2 p-2">
                                    {{ __('bookmarks') }}
                                    <span class="text-gray-400">({{ $bookmarks->total() }})</span>
                                </h3>
                            </div>
                            <div class="col-sm-12 col-md-6 d-flex tw-flex-wrap tw-gap-3 tw-justify-between">
                                <form id="categoryForm" action="{{ route('company.bookmark') }}" method="GET">
                                    <div class="header-dropdown d-flex tw-flex-wrap">
                                        <h3 class="f-size-18 lh-1 mb-2 p-2">
                                            {{ __('filter') }}
                                        </h3>
                                        <select onchange="CategoryForm()" name="category" class="rt-selectactive ">
                                            <option value="all">{{ __('all') }}</option>
                                            @foreach ($categories as $cat)
                                                <option {{ Route::current()->parameter('category') == $cat->id ? 'selected' : '' }}
                                                    value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                                <a href="{{ route('company.bookmark.category.index') }}">
                                    <button type="button" class="btn btn-outline-primary">
                                        {{ __('category') }}
                                    </button>
                                </a>
                                <div class="sidebar-open-nav !tw-mt-0">
                                    <i class="ph-list"></i>
                                </div>
                            </div>
                        </div>
                        @if ($bookmarks->count() > 0)
                            @foreach ($bookmarks as $candidate)
                                <div class="card jobcardStyle1 saved-candidate">
                                    <div class="card-body">
                                        <div class="rt-single-icon-box tw-flex-wrap">
                                            <div class="icon-thumb">
                                                <div class="profile-image position-relative">
                                                    <a href="javascript:void(0)"
                                                        onclick="showCandidateProfileModal('{{ $candidate->user->username }}')">
                                                        <img src="{{ asset($candidate->photo) }}" alt="photo">
                                                    </a>

                                                    @if ($candidate->status == 'available')
                                                        <span class="available-alert-header">
                                                            <svg class="circle" width="10" height="10"
                                                                viewBox="0 0 14 14" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <circle cx="7" cy="7" r="6"
                                                                    fill="#2ecc71" stroke="white" stroke-width="2">
                                                                </circle>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="iconbox-content">
                                                <div class="post-info2">
                                                    <div class="post-main-title">
                                                        <a href="javascript:void(0)"
                                                            onclick="showCandidateProfileModal('{{ $candidate->user->username }}')">
                                                            {{ $candidate->user->name }}
                                                        </a>

                                                    </div>
                                                    <span class="loacton text-gray-400 ">
                                                        @if ($candidate->profession)
                                                            {{ ucfirst($candidate->profession->name) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="iconbox-extra align-self-center">
                                                <div>
                                                    <form
                                                        action="{{ route('company.companybookmarkcandidate', $candidate->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-primary-500 hoverbg-primary-50 plain-button icon-button">
                                                            <x-svg.bookmark2-icon />
                                                        </button>
                                                    </form>
                                                </div>
                                                <div>
                                                    @if (auth()->user()->status)
                                                        <!-- || -->
                                                        <!-- Check auth candidate account is active -->
                                                        <a onclick="showCandidateProfileModal('{{ $candidate->user->username }}')"
                                                            class="btn btn-primary2-50" href="javascript:void(0)">
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
                                                        <a onclick="toastr.warning('{{ __('your_account_is_not_active_please_wait_until_the_account_is_activated_by_admin') }}')"
                                                            class="btn btn-primary2-50" href="javascript:void(0)">
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
                                                <div class="rt">
                                                    <button type="button" class="btn btn-icon hover:bg-gray-50"
                                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M12 13.125C12.6213 13.125 13.125 12.6213 13.125 12C13.125 11.3787 12.6213 10.875 12 10.875C11.3787 10.875 10.875 11.3787 10.875 12C10.875 12.6213 11.3787 13.125 12 13.125Z"
                                                                fill="#767F8C" stroke="#767F8C"></path>
                                                            <path
                                                                d="M12 6.65039C12.6213 6.65039 13.125 6.14671 13.125 5.52539C13.125 4.90407 12.6213 4.40039 12 4.40039C11.3787 4.40039 10.875 4.90407 10.875 5.52539C10.875 6.14671 11.3787 6.65039 12 6.65039Z"
                                                                fill="#767F8C" stroke="#767F8C"></path>
                                                            <path
                                                                d="M12 19.6094C12.6213 19.6094 13.125 19.1057 13.125 18.4844C13.125 17.8631 12.6213 17.3594 12 17.3594C11.3787 17.3594 10.875 17.8631 10.875 18.4844C10.875 19.1057 11.3787 19.6094 12 19.6094Z"
                                                                fill="#767F8C" stroke="#767F8C"></path>
                                                        </svg>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end company-dashboard-dropdown"
                                                        aria-labelledby="dropdownMenuButton1">
                                                        <li>
                                                            <a target="__blank" class="dropdown-item"
                                                                href="mailto:{{ $candidate->user->email }}">
                                                                <svg width="20" height="20" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76">
                                                                    </path>
                                                                </svg>
                                                                {{ __('send_email') }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="card jobcardStyle1 saved-candidate">
                                <div class="card-body">
                                    <div class="text-center">
                                        <x-svg.not-found-icon />
                                        <p class="mt-4">{{ __('no_data_found') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if ($bookmarks->total() > $bookmarks->count())
                        <nav>
                            {{ $bookmarks->links('vendor.pagination.frontend') }}
                        </nav>
                    @endif
                </div>
            </div>
        </div>
        <!-- ============================= -->
        <!-- Modal -->
        <x-website.modal.candidate-profile-modal />
        <!-- ============================= -->
    </div>
@endsection

@section('frontend_links')
    <style>
        #categoryForm {
            width: 60% !important;
        }
    </style>
@endsection

@section('script')
    <script>
        function CategoryForm() {
            $('#categoryForm').on('change', function() {
                $(this).submit();
            })
        }

          // candidate profile modal data by ajax
          function showCandidateProfileModal(username) {
            $.ajax({
                url: "{{ route('website.candidate.application.profile.details') }}",
                type: "GET",
                data: {
                    username: username,
                    count_view: 1
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
                    let candidate = response.data.candidate;

                    $('#candidate_id').val(data.candidate.id)
                    if (data.candidate.bookmarked) {
                        $('#removeBookmakCandidate').removeClass('d-none')
                        $('#bookmakCandidate').addClass('d-none')
                    } else {
                        $('#bookmakCandidate').removeClass('d-none')
                        $('#removeBookmakCandidate').addClass('d-none')
                    }

                    data.name ? $('.candidate-profile-info h2').html(data.name) : '';
                    data.bio ? $('.tab-pane p').html(data.bio) : '';
                    data.candidate.photo ? $('#candidate_image').attr("src", data.candidate.photo) : '';
                    data.candidate.profession ? $('.candidate-profile-info h4').html(capitalizeFirstLetter(data
                        .candidate.profession.name)) : '';
                    data.candidate.bio ? $('.biography p').html(data.candidate.bio) : '';

                    if (data.candidate.status == 'available') {
                       $('.candidate-profile-info h6').removeClass('d-none');
                    }

                    //social media Link
                    candidate.social_info[0] ? $('#facebook').attr('href', candidate.social_info[0]['url']) :
                        '';
                    candidate.social_info[1] ? $('#twitter').attr('href', candidate.social_info[1]['url']) : '';
                    candidate.social_info[2] ? $('#youtube').attr('href', candidate.social_info[2]['url']) : '';
                    candidate.social_info[3] ? $('#linkedin').attr('href', candidate.social_info[3]['url']) :
                        '';
                    candidate.social_info[4] ? $('#pinterest').attr('href', candidate.social_info[4]['url']) :
                        '';
                    candidate.social_info[5] ? $('#github').attr('href', candidate.social_info[5]['url']) : '';

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

                    // skills & languages
                    if (candidate.skills.length != 0) {
                        $("#candidate_skills span").remove();
                        console.log(123)

                        $.each(candidate.skills, function(key, value) {
                            $('#candidate_skills').append(
                                `<span class="tw-bg-[#E7F0FA] tw-rounded-[4px] tw-text-sm tw-text-[#0A65CC] tw-px-3 tw-py-1.5">
                                    ${value.name}
                                </span>`
                            )
                        });
                    }

                    if (candidate.languages.length != 0) {
                        $("#candidate_languages div").remove();

                        $.each(candidate.languages, function(key, value) {
                            $('#candidate_languages').append(
                                `<div class="tw-inline-block tw-p-3 tw-bg-[#F1F2F4] tw-rounded-[4px]">
                                    <h4 class="tw-text-sm tw-text-[#474C54] tw-font-medium tw-mb-[2px]">${value.name}</h4>
                                </div>`

                            )
                        });
                    }

                    if (candidate.educations.length != 0) {
                        $("#candidate_profile_educations tr").remove();

                        $.each(candidate.educations, function(key, value) {
                            $('#candidate_profile_educations').append(
                                `<tr>
                                    <td>${value.level}</td>
                                    <td>${value.degree}</td>
                                    <td>${value.year}</td>
                                </tr>`
                            )
                        });
                    }else{
                        $('#candidate_profile_educations').append(
                            `<tr>
                                <td>No data found</td>
                            </tr>`
                        )
                    }

                    if (candidate.experiences.length != 0) {
                        $("#candidate_profile_experiences tr").remove();

                        $.each(candidate.experiences, function(key, value) {
                            let formatted_end = value.currently_working ? 'Currently Working' : value.formatted_end

                            $('#candidate_profile_experiences').append(
                                `<tr>
                                    <td>${value.company}</td>
                                    <td>${value.department} / ${value.designation}</td>
                                    <td>${value.formatted_start} - ${formatted_end}</td>
                                </tr>
                                `
                            )
                        });
                    }else{
                        $('#candidate_profile_experiences').append(
                            `<tr>
                                <td>No data found</td>
                            </tr>`
                        )
                    }

                    // other info
                    data.candidate.bio ? $('#candidate-bio').html(data.candidate.bio) : '';

                    // experience info
                    const experienceList = document.getElementById("experience-list");

                    // Clear the experience list container before populating the data
                    $('#experience-list').empty();

                    // Loop through the experiences array in reverse order
                    for (let i = data.candidate.experiences.length - 1; i >= 0; i--) {
                        const experience = data.candidate.experiences[i];

                        if (experience.created_at) {

                            experienceHTML = `
                            <li class="tw-text-sm tw-flex tw-flex-col tw-gap-3">
                                <div class="tw-flex tw-flex-col tw-gap-1">
                                    <p class="tw-m-0 tw-text-sm tw-text-[#0066CC] tw-font-medium -tw-mt-1.5">${experience.formatted_start}</p>
                                    <h4 class="tw-m-0 tw-text-sm tw-text-[#14181A] tw-font-medium">${experience.designation}</h4>
                                    <p class="tw-m-0 tw-text-sm tw-text-[#707A7D]">${experience.company}/${experience.department}</p>
                                </div>
                                <p class="tw-m-0 tw-text-sm tw-text-[#3C4649]">${experience.responsibilities}</p>
                            </li>
                            `;
                        }

                        $('#experience-list').append(experienceHTML);
                    }

                    // education info
                    const educationlist = document.getElementById("education-list");

                    $('#education-list').empty();

                    // Loop through the education array in reverse order
                    for (let i = data.candidate.educations.length - 1; i >= 0; i--) {
                        const education = data.candidate.educations[i];

                        // Check if created_at property exists and is not empty
                        if (education.created_at) {

                            const educationHtml = `
                                <li class="tw-text-sm tw-flex tw-flex-col tw-gap-3">
                                    <div class="tw-flex tw-flex-col tw-gap-1">
                                        <p class="tw-m-0 tw-text-sm tw-text-[#0066CC] tw-font-medium -tw-mt-1.5" id="candidate-edu-year">${education.year}</p>
                                        <h4 class="tw-m-0 tw-text-sm tw-text-[#14181A] tw-font-medium">${education.degree}</h4>
                                        <p class="tw-m-0 tw-text-sm tw-text-[#707A7D]">${education.degree} / ${education.level}</p>
                                    </div>
                                    <p class="tw-m-0 tw-text-sm tw-text-[#3C4649]">${education.notes}</p>
                                </li>
                            `;

                            $('#education-list').append(educationHtml);
                        }
                    }

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

                    if (data.candidate.website) {
                        $('#candidate_website').attr('href', data.candidate.website);
                        $('#candidate_website').html(data.candidate.website);
                    }
                    $('#candidate_location').html(data.candidate.exact_location ? data.candidate
                        .exact_location : data.candidate.full_address)

                    data.contact_info && data.contact_info.phone ? $('#candidate_phone').html(data.contact_info
                        .phone) : ''
                    data.contact_info && data.contact_info.secondary_phone ? $('#candidate_seconday_phone')
                        .html(data.contact_info
                            .secondary_phone) : ''
                    data.contact_info && data.contact_info.email ? $('#contact_info_email').html(data
                        .contact_info.email) : ''

                    if (data.candidate.whatsapp_number && data.candidate.whatsapp_number.length) {
                        $("#contact_whatsapp").attr("href", 'https://wa.me/' + data.candidate.whatsapp_number)
                        $('#contact_whatsapp_part').removeClass('d-none')
                    } else {
                        $('#contact_whatsapp_part').addClass('d-none')
                    }

                    $('#candidate-profile-modal').modal('show');
                    if (response.profile_view_limit && response.profile_view_limit.length) {
                        if (!response.data.candidate.already_view) {
                            toastr.success(response.profile_view_limit, 'Success');
                        }
                    }

                    $('#cv_view' + candidate.id).removeClass("d-none");
                },
                error: function(error) {
                    console.log(error.data);
                    Swal.fire('Error', 'Something Went Wrongs!', 'error');
                }
            });
        }
        function capitalizeFirstLetter(string) {
            return string[0].toUpperCase() + string.slice(1);
        }
    </script>
@endsection
