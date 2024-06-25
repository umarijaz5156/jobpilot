@extends('frontend.layouts.app')

@section('title', __('custom_questions'))

@section('main')
    <div class="dashboard-wrapper">
        <div class="container">
            <div class="row">
                <x-website.company.sidebar />
                <div class="col-lg-9">
                    <div x-data="manageQuestions" class="dashboard-right tw-ps-0 lg:tw-ps-5">
                        <div class="d-flex justify-content-between tw-items-center tw-w-full tw-mb-6 lg:tw-mt-0 tw-mt-6">
                            <h3 class="f-size-18 lh-1 mb-0"> {{ __('custom_questions') }} </h3>
                            <form
                                action="{{route('company.questions.featureToggle')}}"
                                method="POST"
                                id="toggle-form-id">
                                @csrf
                                <label>
                                    <input name="enableQuestion"
                                    {{ auth()->user()->company->question_feature_enable ? 'checked' : ''  }}
                                    onchange="document.getElementById('toggle-form-id').submit();"
                                    class="tw-scale-150 " style="margin-right: 10px" type="checkbox" >
                                    {{ __('create_custom_questions')  }}
                                    ({{ auth()->user()->company->question_feature_enable ? 'Enable' : 'Disable' }})
                                </label>
                            </form>
                        </div>
                        <div class="tw-border tw-border-gray-200 tw-p-4 tw-rounded-lg border">
                            <form x-bind:action="{{route('company.questions.store')}}" class="post-job-item rt-mb-15" method="POST">
                                @csrf
                                <div class="tw-mb-3">
                                    <div class="tw-flex justify-content-between tw-items-center tw-mb-3">
                                        <h3 class="tw-text-base lh-1 mb-0">
                                            <span x-show="!isEditing" >{{ __('create') }}  </span>
                                            <span x-show="isEditing" >{{ __('update') }} </span>
                                            {{ __('question')  }}
                                        </h3>
                                        <a x-show="isEditing" @click.prevent="setCreateMode"  href="#">{{__('create_new_screening_question')}}</a>
                                    </div>
                                    <input x-model="editingQuestion.title" name="newQuestion" class="form-control" type="text" placeholder="{{ __('question') }}..">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="hidden" x-model="isEditing" name="isEditing" >
                                    <input type="hidden" x-model="editingQuestion.id" name="editingId" >
                                </div>
                                <div class="tw-mt-4 tw-flex tw-flex-wrap tw-items-start tw-justify-between">
                                    <div class="tw-flex tw-items-center tw-mb-2">
                                        <input x-model="editingQuestion.required" id="answerRequired" name="isRequired" class="tw-scale-150 tw-mr-2 tw-ml-1" type="checkbox">
                                        <label for="answerRequired" class="tw-select-none tw-inline-block">
                                            {{__('candidate_must_answer')}}
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary"> {{__('Save')}} {{ __('question') }} </button>
                                </div>
                            </form>
                        </div>
                        <div class="row tw-mt-12">
                            <div class="col-sm-12 col-md-12">
                                <h3 class="f-size-18 lh-1 mb-4">
                                    {{__('existing_questions')}} ({{$dataCount}})
                                 </h3>
                                <div class=" overflow-hidden">
                                    <div class="">
                                        <div class="db-job-card-table text-center">
                                            @if ($questions->count() > 0)
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th class="text-start">{{ __('title') }}</th>
                                                    <th class="">{{ strtoupper(__('required')) }}</th>
                                                    <th class="text-end">{{ __('action') }}</th>

                                                </tr>
                                                </thead>
                                                <tbody class="text-center">

                                                    @foreach ($questions as $question)
                                                        <tr>
                                                            <td>
                                                                <div class="text-start">
                                                                        <span class="ml-2 text-gray-900 f-size-16  ft-wt-4">
                                                                            {{ $question->title }}
                                                                        </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div >
                                                                    @if($question->required)
                                                                        <svg  color="green" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    @else
                                                                        <svg color="red" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>

                                                                    @endif


                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-end mr-2">
                                                                    <a
                                                                       @click.prevent="editQuestion({{$question->id}})"
                                                                       class="f-size-25 cursor-pointer text-primary p-1">
                                                                        <i class="ph-pencil-simple"></i>
                                                                    </a>

                                                                    <a onclick="DataDelete('data-delete-form{{ $question->id }}')"
                                                                       href="#"
                                                                       class="f-size-25 cursor-pointer text-danger-500 p-1">
                                                                        <i class="ph-trash-simple"></i>
                                                                    </a>
                                                                    <form class="d-none"
                                                                          id="data-delete-form{{ $question->id }}"
                                                                          action="{{ route('company.questions.delete', $question->id) }}"
                                                                          method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                @if ($questions->count())
                                                    <tr>
                                                        <td colspan="2" class="text-center p-0">
                                                            {{ $questions->links('vendor.pagination.simple-bootstrap-4') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                            @else
                                                <tr>
                                                    <td colspan="2" class="text-center">
                                                        <x-svg.not-found-icon class="tw-w-80" />
                                                        <p class="mt-4">{{ __('no_data_found') }}</p>
                                                    </td>
                                                </tr>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-footer text-center body-font-4 text-gray-500">
            <x-website.footer-copyright />
        </div>
    </div>
@endsection


@section('frontend_scripts')

    <script defer src="{{ asset('backend/js/alpine.min.js') }}"></script>

@endsection
@section('script')
    <script>
        function manageQuestions(){
            return {
                currentPageQuestions : @json($questions->items()),
                isEditing : false,
                editingQuestion : {},
                newQuestion : '',
                isRequired : false,

                editQuestion : function (id){
                   this.editingQuestion = this.currentPageQuestions.find((question)=>{
                       return question.id == id ;
                   });
                   this.isEditing = true ;

                },
                setCreateMode : function (){
                    this.isEditing = false
                    this.editingQuestion.required = false;
                    this.editingQuestion.title = '';
                    // this.editingQuestion = {};
                }

            }
        }
    </script>

    <script>
        function DataDelete(id) {
            if (confirm("{{ __('are_you_sure') }}") == true) {
                $('#' + id).submit();
            } else {
                return false;
            }
        }
    </script>
@endsection
