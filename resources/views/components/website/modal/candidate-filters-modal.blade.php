<div class="modal fade" id="candidateFiltersModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <form id="form" action="{{ route('website.candidate') }}" method="GET">
        <div
            class="modal-dialog modal-wrapper tw-mx-0 md:tw-max-w-[352px] tw-w-[90%] tw-my-0 tw-absolute tw-top-0 tw-bootom-0 tw-left-0">
            <div class="modal-content tw-rounded-none tw-relative tw-min-h-screen tw-max-h-screen">
                <div class="tw-h-screen tw-overflow-x-hidden tw-overflow-y-auto tw-pb-24">
                    <div class="tw-px-5 tw-pt-5">
                        <div class="tw-flex tw-justify-between items-center">
                            <h2 class="tw-text-[#18191C] tw-text-xl tw-font-medium tw-mb-0">{{ __('filter') }}</h2>
                            <button type="button" class="tw-p-0 tw-border-0 tw-bg-transparent" data-bs-dismiss="modal"
                                aria-label="Close">
                                <x-svg.close-icon />
                            </button>
                        </div>
                    </div>
                    <div class="tw-p-5">
                        <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-2 tw-font-medium">{{ __('Skills') }}</h2>
                        <div class="">
                            {{-- <select id="skills" name="skills[]"
                        class="select2-taggable form-control  select2-hidden-accessible" multiple=""
                        data-select2-id="skills" tabindex="-1" aria-hidden="true">
                    </select> --}}
                            <select name="skills[]" class="form-control @error('skills') is-invalid @enderror"
                                id="skills" multiple>
                                @foreach ($skills as $skill)
                                    <option
                                        {{ request('skills') ? (in_array($skill->name, request('skills')) ? 'selected' : '') : '' }}
                                        value="{{ $skill->name }}">
                                        {{ $skill->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr class="tw-bg-[#E4E5E8] tw-m-0">
                    <div class="tw-p-5">
                        <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-2 tw-font-medium">{{ __('Experiences') }}</h2>
                        <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                            <input type="radio" name="experience" value="" id="allEx" class="tw-scale-125">
                            <label for="allEx" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">All</label>
                        </div>
                        @foreach ($experiences as $experience)
                            <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                <input {{ $experience->name == request('experience') ? 'checked' : '' }}
                                    data-id="{{ Route::current()->parameter('experience') }}" aria-data-id="category"
                                    type="radio" id="{{ $experience->name }}_{{ $experience->id }}"
                                    class="tw-scale-125" name="experience" value="{{ $experience->name }}">
                                <label for="{{ $experience->name }}_{{ $experience->id }}"
                                    class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $experience->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <hr class="tw-bg-[#E4E5E8] tw-m-0">
                    <div class="tw-p-5">
                        <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-2 tw-font-medium">{{ __('Education') }}</h2>
                        <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                            <input type="radio" value="" name="education" id="allEdu" class="tw-scale-125">
                            <label for="allEdu" class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">All</label>
                        </div>
                        @foreach ($educations as $education)
                            <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                                <input {{ $education->name == request('education') ? 'checked' : '' }}
                                    data-id="{{ Route::current()->parameter('education') }}" aria-data-id="category"
                                    type="radio" id="{{ $education->name }}_{{ $education->id }}"
                                    class="tw-scale-125" name="education" value="{{ $education->name }}">
                                <label for="{{ $education->name }}_{{ $education->id }}"
                                    class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $education->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div
                    class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-p-5 tw-bg-white tw-z-50 tw-flex tw-flex-wrap tw-gap-3 tw-justify-between tw-items-center tw-mt-3">
                    <div class="tw-flex tw-flex-wrap tw-items-center tw-w-full ">

                        <label for="remote" class="!tw-flex !tw-items-center tw-cursor-pointer">
                            <!-- toggle -->
                            <div class="tw-relative !tw-inline-block remote-toggle">
                                <!-- input -->
                                <input type="checkbox" id="remote" class="tw-sr-only" value="1"
                                    data-id="{{ Route::current()->parameter('category') }}" aria-data-id="category"
                                    name="is_remote" {{ request('is_remote') ? 'checked' : '' }}>
                                <!-- line -->
                                <div class="tw-block tw-bg-[#E4E5E8] tw-w-10 tw-h-[22px] tw-rounded-full"></div>
                                <!-- dot -->
                                <div
                                    class="dot tw-absolute tw-left-1 tw-top-1 tw-bg-white tw-w-3.5 tw-h-3.5 tw-rounded-full tw-transition">
                                </div>
                            </div>
                            <!-- label -->
                            <div class="tw-ml-3 tw-text-gray-700 tw-font-medium">
                                {{ __('remote_job') }}
                            </div>
                        </label>

                    </div>
                    <div>
                        <button type="submit"
                            class="btn btn-primary tw-inline-block">{{ __('apply_filter') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
