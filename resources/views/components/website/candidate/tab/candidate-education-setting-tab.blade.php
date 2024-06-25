@props(['educations'])
<div class="tw-flex rt-mb-32 lg:tw-mt-0 tw-items-center tw-justify-between">
    <h3 class="f-size-18 lh-1 m-0">{{ __('educations') }}</h3>
    <button id="addEducation" type="button" class="btn btn-primary ">
        {{ __('add_education') }}
    </button>
</div>
<div class="db-job-card-table -tw-mx-2">
    <table class="tw-px-2">
        <thead>
            <tr>
                <th class="!tw-text-base !tw-font-medium">{{ __('education_level') }}</th>
                <th class="!tw-text-base !tw-font-medium">{{ __('degree') }}</th>
                <th class="!tw-text-base !tw-font-medium">{{ __('year') }}</th>
                <th class="!tw-text-base !tw-font-medium tw-text-right">{{ __('action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($educations as $education)
                <tr>
                    <td>{{ $education->level }}</td>
                    <td>{{ $education->degree }}</td>
                    <td>{{ $education->year }}</td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-icon" id="dropdownMenuButton5"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
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
                                aria-labelledby="dropdownMenuButton5">
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item"
                                        onclick="educationDetail({{ json_encode($education) }})">
                                        <x-svg.edit-icon />
                                        {{ __('edit') }}
                                    </a>
                                </li>
                                <li>
                                    <form method="POST"
                                        action="{{ route('candidate.educations.destroy', $education->id) }}">
                                        @csrf
                                        @method('Delete')
                                        <button type="submit" class="dropdown-item"
                                            onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');">
                                            <x-svg.trash-icon />
                                            {{ __('delete') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        <x-svg.not-found-icon />
                        <p class="mt-4">{{ __('no_data_found') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('frontend_links')
    <link rel="stylesheet" href="{{ asset('frontend') }}/assets/css/bootstrap-datepicker.min.css">
    <style>
        #addEducationModal .modal-dialog,
        #editEducationModal .modal-dialog {
            z-index: 999999 !important;
            max-width: 950px !important;
            padding: 20px;
        }
    </style>
@endpush

@push('frontend_scripts')
    <script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
    @if (app()->getLocale() == 'ar')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ar.min.js
                    "></script>
    @endif
    <script>
        $('#addEducation').on('click', function() {
            $('#addEducationModal').modal('show');
        });

        //  $(".year_picker").attr("autocomplete", "off");

        //init datepicker
        // $('.year_picker').off('focus').datepicker({
        //     format: "yyyy",
        //     viewMode: "years",
        //     minViewMode: "years",
        //     isRTL: "{{ app()->getLocale() == 'ar' ? true : false }}",
        //     language: "{{ app()->getLocale() }}",
        // }).on('click',
        //     function() {
        //         $(this).datepicker('show');
        //     }
        // );

        $('.year_picker').datepicker({
            format: 'yyyy',
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        function closeAddEducationModal() {
            $('#addEducationModal').find('form')[0].reset();
            $('#addEducationModal').modal('hide')
        }

        function closeEditEducationModal() {
            $('#editEducationModal').find('form')[0].reset();
            $('#editEducationModal').modal('hide')
        }

        function educationDetail(education, start, end) {
            $('#education-modal-id').val(education.id);
            $('#education-modal-level').val(education.level);
            $('#education-modal-degree').val(education.degree);
            $('#education-modal-year').val(education.year);
            $('#education-notes').val(education.notes);

            $('#editEducationModal').modal('show');
        }
    </script>
@endpush
