<script src="{{ asset('frontend/assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/OverlayScrollbars.js') }}"></script>
<script src="{{ asset('frontend/assets/js/scrollax.min.js') }}"></script>
<script src="{{ asset('backend/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/waypoints.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('backend/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('backend/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/aos.js') }}"></script>
<script src="{{ asset('frontend/assets/js/slick.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/sortable.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/ckeditor.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/axios.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/app.js') }}"></script>
<script src="{{ asset('backend/js/livewire.js') }}"></script>
@vite('resources/frontend/js/app.js')

<script>
    var auth_check = $('#auth_user').val();

    if (auth_check == 1) {
        loadUnreadMessageCount();

        function playAudio() {
            const audio = new Audio("/frontend/assets/sound.mp3");
            audio.play();
        }

        function loadUnreadMessageCount() {
            $.ajax({
                url: "{{ route('load.unread.count') }}",
                type: "GET",
                success: function(response) {
                    if (response > 0) {
                        $('.unread-message-part').removeClass('d-none');
                    } else {
                        $('.unread-message-part').addClass('d-none');
                    }
                }
            });
        }
    }

    // autocomplete
    var path = "{{ route('website.job.autocomplete') }}";

    $('.global_header_search').keyup(function(e) {
        var keyword = $(this).val();

        if (keyword != '') {
            $.ajax({
                url: path,
                type: 'GET',
                dataType: "json",
                data: {
                    search: keyword
                },
                success: function(data) {
                    $('#autocomplete_job_results').fadeIn();
                    $('#autocomplete_job_results').html(data);
                }
            });
        } else {
            $('#autocomplete_job_results').fadeOut();
        }
    });

    $('#global_search').keypress(function(e) {
        var key = e.which;

        if (key == 13) {
            $('#search-form').submit();
        }
    });

    $("#searchIcon").click(function() {
        $(".togglesearch").toggle();
        $("#search_input").focus();
    });

    $("#mblSearchIcon").click(function() {

        $(".mblTogglesearch").toggle();
        $('#mobile_search_input').focus();
    });


    $('button.effect1').on('click', function() {
        $(this).find('span').toggleClass('active');
    });

    $('.rt-mobile-menu-overlay').on('click', function() {
        $('button.effect1').find('span').removeClass('active');
    });

    //image upload scripts
    function readURL(input) {
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
                $('.profile-file-upload-input').replaceWith($('.profile-file-upload-input').clone());
                $('.profile-file-upload-content').hide();
                $('.profile-file-upload-image').attr('src', '');
                $('.profile-image-upload-wrap').show();
            })
            $('.banner-remove-image').on('click', function() {
                $('.banner-file-upload-input').replaceWith($('.banner-file-upload-input').clone());
                $('.banner-file-upload-content').hide();
                $('.banner-file-upload-image').attr('src', '');
                $('.banner-image-upload-wrap').show();
            })
        }
    }
    $('.profile-remove-image').on('click', function() {
        $('.profile-file-upload-input').replaceWith($('.profile-file-upload-input').clone());
        $('.profile-file-upload-content').hide();
        $('.profile-image-upload-wrap').show();
    })
    $('.banner-remove-image').on('click', function() {
        $('.banner-file-upload-input').replaceWith($('.banner-file-upload-input').clone());
        $('.banner-file-upload-content').hide();
        $('.banner-image-upload-wrap').show();
    })
    $('.cv-remove-image').on('click', function() {
        $('.resume-file-upload-input').replaceWith($('.resume-file-upload-input').clone());
        $('.resume-file-upload-content').hide();
        $('.cv-image-upload-wrap').show();
    })

    $('.image-upload-wrap').bind('dragover', function() {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function() {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
</script>
@stack('frontend_scripts')
@yield('frontend_scripts')

<script>
    @if (request('verified'))
        Swal.fire({
            title: "{{ __('email_verified') }}",
            text: "{{ __('your_email_has_been_verified') }}",
            icon: "success",
        })
    @endif

    @if (Session::has('success'))
        toastr.success("{{ Session::get('success') }}", 'Success!')
    @endif

    @if (Session::has('warning'))
        toastr.warning("{{ Session::get('warning') }}", 'Warning!')
    @endif

    @if (Session::has('error'))
        toastr.error("{{ Session::get('error') }}", 'Error!')
    @endif

    // toast config
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "hideMethod": "fadeOut"
    }

    $('.login_required').on('click', function(event) {
        event.preventDefault();

        Swal.fire({
            title: "{{ __('unauthenticated') }}",
            text: "{{ __('if_you_perform_this_action_you_need_to_login_your_account_first_do_you_want_to_login_now') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('yes_want_to_login') }}",
            cancelButtonText: "{{ __('cancel') }}",
        }).then((result) => {
            if (result.value) {
                window.location.href = '/login';
            }
        })
    });
    $('.no_permission').on('click', function(event) {
        event.preventDefault();
        Swal.fire({
            title: "{{ __('unauthorized_access') }}",
            text: "{!! __('you_dont_have_permission_to_perform_this_action') !!}",
            icon: "warning",
            dangerMode: true,
        })
    });

    $('[data-toggle="tooltip"]').tooltip();

    $(".notification-icon a").off("click").on('click', function(e) {
        e.stopImmediatePropagation();
        return true;
    });
</script>

<script>
    // read notification by ajax
    function ReadNotification() {
        $.ajax({
            url: "{{ route('user.notification.read') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(data) {
                $('#unNotifications').hide();
            }
        });
    }
    // read single notification by ajax
    function readSingleNotification(url, id) {
        $.ajax({
            url: "{{ route('website.markread.notification') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(data) {
                window.location.href = url;
            }
        });
    }
    // Call ckeditor
    if (document.querySelector('#image_ckeditor')) {
        ClassicEditor.create(document.querySelector('#image_ckeditor'), {
                ckfinder: {
                    uploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}"
                },
            })
            .catch(error => {
                console.error(error);
            });
    }
    // Call ckeditor
    if (document.querySelector('#image_ckeditor_2')) {
        ClassicEditor.create(document.querySelector('#image_ckeditor_2'), {
                ckfinder: {
                    uploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}"
                },
            })
            .catch(error => {
                console.error(error);
            });
    }
    // Call ckeditor
    if (document.querySelector('#editor2')) {

        ClassicEditor.create(document.querySelector('#editor2'))
            .then(editor => {
                editor.ui.view.editable.element.style.height = '500px';
            })
            .catch(error => {
                console.error(error);
            });
    }
    // Call ckeditor
    if (document.querySelector('#editor3')) {
        ClassicEditor.create(document.querySelector('#editor3'))
            .then(editor => {
                editor.ui.view.editable.element.style.height = '500px';
            })
            .catch(error => {
                console.error(error);
            });
    }
    // Call ckeditor
    if (document.querySelector('#editor4')) {
        ClassicEditor.create(document.querySelector('#editor4'))
            .then(editor => {
                editor.ui.view.editable.element.style.height = '500px';
            })
            .catch(error => {
                console.error(error);
            });
    }

    function setLocationSession(form) {
        axios.post('/set/session', form)
            .then((res) => {
                // console.log(res.data);
                // toastr.success("Location Saved", 'Success!');
            })
            .catch((e) => {
                toastr.error("Something Wrong", 'Error!');
            });
    }
    // about page testimonial
    if ($(".testimonal2-active").length > 0) {
        $(".testimonal2-active").slick({
            slidesToShow: 1,
            infinite: true,
            slidesToScroll: 1,
            dots: true,
            fade: false,
            prevArrow: $(".slickprev3"),
            nextArrow: $(".slicknext3")
        });
    }
    // tab switch style
    var style = localStorage.getItem("candidate_style") == null ? 'box' : localStorage.getItem("candidate_style");
    setStyle(style);

    function styleSwitch(style) {
        localStorage.setItem("candidate_style", style);
        setStyle(style);
    }

    function setStyle(style) {
        if (style == 'box') {
            $('#nav-home-tab').addClass('active');
            $('#nav-home').addClass('show active');
            $('#nav-profile-tab').removeClass('active');
            $('#nav-profile').removeClass('show active');
        } else {
            $('#nav-home-tab').removeClass('active');
            $('#nav-home').removeClass('show active');
            $('#nav-profile-tab').addClass('active');
            $('#nav-profile').addClass('show active');
        }
    }

    // category wise search
    $(document).ready(function() {
        const form = $("#job_search_form");
        const radioButtons = form.find("input[aria-data-id='category']");

        // Store the initial action attribute value
        const defaultAction = form.attr("action");

        // Function to update the form action based on the selected radio button
        function updateFormAction(selectedRadioValue) {
            const dataSlug = selectedRadioValue || '';
            const actionUrl = selectedRadioValue ?
                "{{ route('website.job.category.slug', ':slug') }}".replace(':slug', dataSlug) :
                defaultAction;
            form.attr("action", actionUrl);
        }

        // Initialize form action on page load
        updateFormAction("{{ Route::current()->parameter('category') }}");

        // Update selected radio value when radio button changes
        radioButtons.on("change", function() {
            const selectedRadioValue = $(this).data('id');
            updateFormAction(selectedRadioValue);
        });
    });
</script>

@yield('script')
@stack('script')
