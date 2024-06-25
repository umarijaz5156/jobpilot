<div class="modal fade" id="resumeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog tw-max-w-[536px]">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <h5 class="tw-text-lg tw-text-[#18191C] tw-font-semibold tw-mb-[18px]" id="cvModalLabel">
                        {{ __('add_cv_resume') }}</h5>
                    <div class="from-group py-2">
                        <x-forms.label name="cv_resume_name" :required="true"
                            class="tw-mb-2 tw-text-sm tw-text-[#18191C]" />
                        <input type="text" placeholder="{{ __('cv_resume_name') }}" name="resume_name"
                            id="">
                        <span id="resume_name" class="tw-text-sm tw-text-red-500"></span>
                    </div>
                    <div class="form-group tw-mb-6">
                        <x-forms.label name="upload_cv_resume" class="tw-mb-2 tw-text-sm tw-text-[#18191C]" />
                        <div class="cv-image-upload-wrap">
                            <input name="resume_file" onchange="resumeManageReadURL(this, 'add');" id="resume_add_input"
                                class="resume-file-upload-input" type="file" accept="application/pdf"
                                id="resume_add_input" />
                            <div class="drag-text">
                                <x-svg.upload-icon />
                                <h3>{{ __('browse_file') }}</h3>
                                <p>{{ __('available_format') }} - PDF<br>
                                    {{ __('maximum_file_size') }} - 5 MB</p>
                            </div>
                        </div>
                        <div class="resume-file-upload-content none">
                            <div class="wrap">
                                <x-svg.file-icon2 />
                                <h3 class="resume_selected_file_name">file</h3>
                                <p>
                                    <span><span class="resume_selected_file_size">2.3</span> MB</span> <br>
                                    <span class="resume_selected_file_type">.pdf</span>
                                </p>
                                <div class="image-title-wrap">
                                    <button type="button" class="cv-remove-image">
                                        <x-svg.trash-icon />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <span id="resume_file" class="tw-text-sm tw-text-red-500"></span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <button onclick="resumeModalClose()" type="button"
                            class="bg-priamry-50 btn btn-primary-50">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="button-content-wrapper ">
                                <span class="button-icon align-icon-right"><i class="ph-arrow-right"></i></span>
                                <span class="button-text">
                                    {{ __('add_cv_resume') }}
                                </span>
                            </span>
                        </button>
                    </div>
                    <button type="button" onclick="resumeModalClose()"
                        class="tw-rounded-full tw-flex tw-items-center tw-justify-center tw-p-3 tw-absolute -tw-top-[25px] -tw-right-[25px] tw-bg-white tw-border-2 tw-border-[#E7F0FA]">
                        <x-svg.modal-cross-icon />
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('child_js')
    <script>
        $('#resumeModal form').submit(function(event) {
            event.preventDefault();

            var files = $('input[name=resume_file]')[0].files;
            var resume_name = $('input[name=resume_name]').val();

            var fd = new FormData();
            fd.append('resume_name', resume_name);
            fd.append('resume_file', files[0]);
            fd.append('type', 'ajax');
            fd.append('_token', '<?php echo csrf_token(); ?>');

            $.ajax({
                url: "{{ route('candidate.resume.store.ajax') }}",
                type: 'post',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.success, 'Success');
                        $('input[name=resume_file]').val('');
                        $('input[name=resume_name]').val('');
                        cvRemoveImage();
                        resumeModalClose();
                    }
                },
                error: function(response) {
                    for (const [key, value] of Object.entries(response.responseJSON.errors)) {
                        $('#' + key).text(value);
                        $(`input[name="${key}"]`).addClass('tw-border-red-500');

                        setTimeout(() => {
                            $('#' + key).text('');
                            $(`input[name="${key}"]`).removeClass('tw-border-red-500');
                        }, 4000);
                    }
                }
            });

        });

        function cvRemoveImage() {
            $('.resume-file-upload-input').replaceWith($('.resume-file-upload-input').clone());
            $('.resume-file-upload-content').hide();
            $('.cv-image-upload-wrap').show();
            $('.resume-file-upload-input').val('');
        }

        $('.cv-remove-image').on('click', function() {
            cvRemoveImage();
        })

        function resumeManageReadURL(input, type) {
            if (type == 'add') {
                var fileName = document.querySelector('#resume_add_input').files[0].name;
                var fileSize = document.querySelector('#resume_add_input').files[0].size / 1024 / 1024;
                var fileType = document.querySelector('#resume_add_input').files[0].type;
            } else {
                var fileName = document.querySelector('#resume_edit_input').files[0].name;
                var fileSize = document.querySelector('#resume_edit_input').files[0].size / 1024 / 1024;
                var fileType = document.querySelector('#resume_edit_input').files[0].type;
            }
            $('.resume_selected_file_name').html(fileName);
            $('.resume_selected_file_size').html(fileSize.toFixed(4));
            $('.resume_selected_file_type').html(fileType);
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    if (input.className === 'profile-file-upload-input') {
                        $('.profile-image-upload-wrap').hide();
                        $('.profile-file-upload-image').attr('src', e.target.result);
                        $('.profile-file-upload-content').show();
                        // $('.image-title').html(input.files[0].name);
                    }
                    if (input.className === 'banner-file-upload-input') {
                        $('.banner-image-upload-wrap').hide();
                        $('.banner-file-upload-image').attr('src', e.target.result);
                        $('.banner-file-upload-content').show();
                        // $('.image-title').html(input.files[0].name);
                    }
                    if (input.className === 'resume-file-upload-input') {
                        $('.cv-image-upload-wrap').hide();
                        $('.resume-file-upload-content.none').show();
                    }
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $('.profile-remove-image').on('click', function() {
                    // console.log(this.className)
                    $('.profile-file-upload-input').replaceWith($('.profile-file-upload-input').clone());
                    $('.profile-file-upload-content').hide();
                    $('.profile-file-upload-image').attr('src', '');
                    $('.profile-image-upload-wrap').show();
                })
                $('.banner-remove-image').on('click', function() {
                    // console.log(this.className)
                    $('.banner-file-upload-input').replaceWith($('.banner-file-upload-input').clone());
                    $('.banner-file-upload-content').hide();
                    $('.banner-file-upload-image').attr('src', '');
                    $('.banner-image-upload-wrap').show();
                })
            }
        }
    </script>
@endsection
