@extends('frontend.layouts.app')

@section('description')
    @php
        $data = metaData('candidates');
    @endphp
    {{ $data->description }}
@endsection
@section('og:image')
    {{ asset($data->image) }}
@endsection
@section('title')
    {{ $data->title }}
@endsection

@section('main')
    <!-- Filter Component -->
    <x-website.candidate.candidate-filter :popular-tags="$popularTags" :professions="$professions" :experiences="$experiences" :educations="$educations" :skills="$skills" />

    <!--  canidates   -->
    <div class="" id="togglclass1">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane  show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                @if ($candidates->count() > 0)
                    <div class="row">
                        @foreach ($candidates as $candidate)
                            <div
                                class="@if (request('education') || request('gender') || request('experience') || request('skills')) col-md-6 fade-in-bottom  condition_class rt-mb-24 @else col-md-6 fade-in-bottom  condition_class rt-mb-24 col-xl-4 @endif">
                                <a onclick="showCandidateProfileModal('{{ $candidate->user->username }}')"
                                    href="javascript:void(0);"
                                    class="card jobcardStyle1 body-24 {{ !auth('user')->check() ? 'login_required' : '' }}">
                                    <div class="card-body">
                                        <div class="rt-single-icon-box icb-clmn-lg tw-reltaive">
                                            <div class="icon-thumb tw-relative">
                                                <div class="profile-image">
                                                    <img src="{{ $candidate->photo }}" alt="{{ __('candidate_image') }}">
                                                </div>
                                                <div class="tw-absolute tw-top-0 tw-left-1">
                                                    @if ($candidate->status == 'available')
                                                        <div class="tw-inline-flex">
                                                            <svg width="14" height="14" viewBox="0 0 14 14"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <circle cx="7" cy="7" r="6"
                                                                    fill="#2ecc71" stroke="white" stroke-width="2">
                                                                </circle>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $option = auth('user')->check() ? '' : '';
                                            @endphp
                                            <div class="iconbox-content !tw-m-0">
                                                <div class="job-mini-title">
                                                    @if (auth('user')->check())
                                                        <span>
                                                            {{ $candidate->user->name }}
                                                        </span>
                                                    @else
                                                        <span class="login_required">
                                                            {{ maskFullName($candidate->user->name) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="loacton text-gray-500 ">
                                                    {{ $candidate->profession ? $candidate->profession->name : '' }}
                                                </span>
                                                @if ($candidate->status == 'available')
                                                    <span class="body-font-4 mt-1 text-gray-900 d-block">
                                                        {{ __('i_am_available') }}
                                                    </span>
                                                @endif
                                                <div class="bottom-link mt-1">
                                                    @if (auth('user')->check())
                                                        <span class="body-font-4 text-primary-500">{{ __('view_resume') }}
                                                            <x-svg.arrow-right-icon />
                                                        </span>
                                                    @else
                                                        <span
                                                            class="body-font-4 text-primary-500 login_required">{{ __('view_resume') }}
                                                            <x-svg.arrow-right-icon />
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div

                                                class="tw-inline-flex tw-justify-center tw-items-center tw-absolute tw-top-3 tw-right-3 tw-text-[#767F8C]">
                                                @if ($candidate->already_view)
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('you_have_seen_the_cv_on') }} {{ $candidate->already_views && $candidate->already_views[0] ? $candidate->already_views[0]->view_date_time : '-' }}. After {{ $candidate->already_views && $candidate->already_views[0] ? $candidate->already_views[0]->expired_date : '-' }} {{ __('the_view_count_will_be_reset') }}"
                                                        class="cursor-pointer ml--10px" id="cv_view">
                                                        <x-svg.eye-icon fill="#767F8C" />
                                                    </div>
                                                @else
                                                    <div data-bs-toggle="tooltip" data-bs-placement="top"
                                                        class="cursor-pointer ml--10px">
                                                        <div class="d-none" id="cv_view{{ $candidate->id }}">
                                                            <x-svg.eye-icon fill="#767F8C" />
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
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
            <!-- For List -->
            <x-website.candidate.candidate-view-list :candidates="$candidates" />
        </div>
    </div>
    </div>
    </div>
    </div>

    <div class="rt-spacer-100 rt-spacer-md-50"></div>

    {{-- Subscribe Newsletter --}}
    <x-website.subscribe-newsletter />

    <!-- ===================================== -->
    <div class="modal fade cadidate-modal" id="aemploye-profile" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-wrapper modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="text-center">
                        {{ __('save_to') }}
                    </h5>
                    <div class="row border-top">
                        <div class="col-md-12" id="categoryList">
                        </div>
                        <div class="col-md-12 tw-mt-3">
                            <div class="saved-candidate">
                                <a class="btn btn-primary" target="_blank" href="{{ route('company.bookmark.category.index') }}">
                                    <span>{{ __('create_category') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-website.modal.candidate-profile-modal />

    {{-- Candidate Filter Modal --}}
    <x-website.modal.candidate-filters-modal :experiences="$experiences" :educations="$educations" :skills="$skills"/>

     <!-- Find Candidate Id From Here | Please don't remove it Start -->
    <input type="hidden" value="" id="candidate_id">
    <!-- Find Candidate Id From Here | Please don't remove it End -->

@endsection

@push('frontend_links')
<link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <style>
        @media(min-width: 768px) {
            #aemploye-profile .modal-wrapper {
                width: 30% !important;
                margin: 1.75rem auto !important;
            }
        }

        #aemploye-profile .modal-body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .benefits-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .benefits-tags input,
        .technologies input {
            position: absolute;
            opacity: 0;
        }

        .benefits-tags span {
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            color: #474C54;
            background: rgba(241, 242, 244, 0.4);
            border: 1px solid #E4E5E8;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
        }

        .benefits-tags input:checked~span,
        .benefits-tags span.active {
            color: #0A65CC;
            background: #E7F0FA;
            border: 1px solid #0A65CC;
            border-radius: 4px;
        }

        .mr--10px {
            margin-right: 10px
        }

        .ml--10px {
            margin-left: 10px
        }

        .tooltip-inner {
            max-width: 300px;
        }

        .whatsapp-button {
            background: rgb(243, 243, 243);
            border-radius: 35px;
            display: flex;
            -webkit-box-flex: 2;
            flex-grow: 2;
            -webkit-box-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            align-items: center;
            height: 40px;
            border: 1px solid rgb(223, 223, 223);
            width: 144px;
            max-width: 160px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 15px;
        }
    </style>
@endpush

@section('script')
    <script>
        // filter function
        function Filter() {
            $('#form').submit();
        }
        // sorting
        $('#sortby').on('change', function() {
            $('#form').submit();
        })
        // perpage
        $('#perpage').on('change', function() {
            $('#form').submit();
        })
        // filter close
        function FilterClose(name) {
            $('[name="' + name + '"]').val('');
            $('#form').submit();
        }
        // candidate profile modal data by ajax
        function showCandidateProfileModal(username) {
            $.ajax({
                url: "{{ route('website.candidate.profile.details') }}",
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

                    // Set message candidate
                    $('#message_candidate_id').val(data.candidate.id)
                    $('#message_candidate_name').text(data.name)
                    $('#message_candidate_position').text(data?.candidate?.profession?.name || '');
                    $('#message_candidate_image').attr('src', data.candidate.photo || '')



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
                    }

                    if (candidate.experiences.length != 0) {
                        $("#candidate_profile_experiences tr").remove();

                        $.each(candidate.experiences, function(key, value) {
                            let formatted_end = value.currently_working ? 'Currently Working' : value
                                .formatted_end
                            $('#candidate_profile_experiences').append(
                                `<tr>
                                    <td>${value.company}</td>
                                    <td>${value.department} / ${value.designation}</td>
                                    <td>${value.formatted_start} - ${formatted_end}</td>
                                </tr>
                                `
                            )
                        });
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
                    data.contact_info && data.contact_info.country ? $('#candidate_nationality').html(data
                        .contact_info.country
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
        // company bookmarked data by ajax
        function CompanyBookmark(candidate) {
            $.ajax({
                url: "{{ route('company.bookmark.category.index', ['ajax' => 1]) }}",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {

                    $('#categoryList').html('');
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            $('#categoryList').append(`
                                <div class="saved-candidate tw-my-2">
                                    <label for="exampleRadios${value.id}" class="tw-flex tw-gap-3 tw-items-center tw-cursor-pointer">
                                        <input onclick="BookmarkCanidate(${candidate},${value.id})" type="radio" name="experience" value="6" id="exampleRadios${value.id}">
                                        <span class="tw-flex">${value.name}</span>
                                    </label>
                                </div>
                            `);
                        });
                    }
                }
            });

            $('#aemploye-profile').modal('show');
        };
        // candidate bookmarked data by ajax
        function BookmarkCanidate(id, cat) {
            var url = "{{ route('company.companybookmarkcandidate', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    cat: cat,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
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

                    // location.reload();
                },
                error: function(data) {
                    location.reload();
                }
            });
        }
        // remove bookmark data by ajax
        $('#removeBookmakCandidate').on('click', function() {
            var url = "{{ route('company.companybookmarkcandidate', ':id') }}";
            url = url.replace(':id', $('#candidate_id').val());
            // alert($('#candidate_id').val())
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#removeBookmakCandidate').addClass('d-none')
                    $('#bookmakCandidate').removeClass('d-none')
                    toastr.success('Candidate removed from bookmark list', 'Success')
                },
                error: function(data) {
                    // alert('Something went wrong')
                    location.reload();
                }
            });
        });
        // bookmarked candidate
        $('#bookmakCandidate').on('click', function() {
            CompanyBookmark($('#candidate_id').val())
            $('#candidate-profile-modal').modal('hide');
        });
        // capitalize
        function capitalizeFirstLetter(string) {
            return string[0].toUpperCase() + string.slice(1);
        }
        //tooltip
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        //  skilss select2 design
        $('#skills').select2({
            theme: 'bootstrap4',
            tags: true,
            placeholder: 'Select Skill'
        });

    </script>
@endsection
