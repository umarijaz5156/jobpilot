@extends('backend.layouts.app')
@section('title')
    {{ __('Document Lists') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">{{ __('Submitted Documents') }}

                        </h3>

                    </div>

                    <div id="example1_wrapper" class="">

                            <div class="col-md-12">
                                @if(!$company->getFirstMedia('document'))
                                    <h4  class="mt-5 mb-5 ml-2">Document Not Given</h4>
                                @else

                                        <div class="form-group col-8" style="margin-left: auto ; margin-right: auto">
                                            <x-forms.label class="mt-4"  name="Image of your NID/Driving Lisence/Passport " :required="false"  />
                                            <input name="document" type="file"
                                                   data-show-errors="true" data-width="100%"

                                                   data-default-file="{{ $company->getFirstMedia('document') ? $company->getFirstMedia('document')->getFullUrl() : ""  }}"
                                                   {{ $company->getFirstMedia('document') ? "disabled='disabled'"  : '' }}
                                                   class="dropify">
                                            <form class="my-4" action="{{route('company.verify.documents.download',$company)}}" method="POST">
                                                @csrf
                                                <input type="hidden"  name="file_type" value="document">
                                                <button class="btn btn-primary" type="submit">Download</button>
                                            </form>
                                            <label for="document_verify" style="display: flex">
                                                Verified
                                                <input id="document_verify"
                                                       {{ $company->document_verified_at ? 'checked' : ''  }}

                                                       type="checkbox" style="width: 24px ; height: 24px ; margin-left: 6px" >
                                            </label>
                                        </div>



                                @endif
                            </div>

                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

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
    <script src="{{ asset('backend') }}/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="{{ asset('backend') }}/plugins/dropify/js/dropify.min.js"></script>

    <script>
        $('.dropify').dropify();
    </script>
    <script>
        $('#document_verify').on('change', function() {
            var status = $(this).prop('checked') == true ? 1 : 0;

            var companyId = {{$company->id}};

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '{{ route('admin.document.verify.change',$company) }}',

                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });

            {{--if (status == 1) {--}}
            {{--    $(`#verification_status_${id}`).text("{{ __('verified') }}")--}}
            {{--}else{--}}
            {{--    $(`#verification_status_${id}`).text("{{ __('unverified') }}")--}}
            {{--}--}}
        });

    </script>
@endsection
