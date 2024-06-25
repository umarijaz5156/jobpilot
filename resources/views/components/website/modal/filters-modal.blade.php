@props(['jobTypes', 'categories', 'maxSalary', 'currentCurrency'])

<div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div
        class="modal-dialog  modal-wrapper md:tw-max-w-[352px] tw-mx-0 tw-w-[90%] tw-my-0 tw-absolute tw-top-0 tw-bootom-0 tw-left-0">
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
                    <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-2 tw-font-medium">{{ __('job_type') }}</h2>
                    @foreach ($jobTypes as $type)
                        <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                            <input {{ $type->name == request('job_type') ? 'checked' : '' }}
                                data-id="{{ Route::current()->parameter('category') }}" aria-data-id="category"
                                type="radio" id="{{ $type->name }}_{{ $type->id }}" class="tw-scale-125"
                                name="job_type" value="{{ $type->name }}">
                            <label for="{{ $type->name }}_{{ $type->id }}"
                                class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $type->name }}</label>
                        </div>
                    @endforeach
                </div>
                <hr class="tw-bg-[#E4E5E8] tw-m-0">
                <div class="tw-p-5">
                    <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-8 tw-font-medium">{{ __('salary') }}</h2>
                    <div>
                        <input type="hidden" name="price_min" id="price_min"
                            data-id="{{ Route::current()->parameter('category') }}" aria-data-id="category"
                            value="{{ is_string(request('price_min')) ? request('price_min') : '' }}">
                        <input type="hidden" name="price_max" id="price_max"
                            data-id="{{ Route::current()->parameter('category') }}" aria-data-id="category"
                            value="{{ is_string(request('price_max')) ? request('price_max') : '' }}">
                        <div id="priceCollapse" class="accordion-collapse collapse show mt-2" aria-labelledby="priceTag"
                            data-bs-parent="#accordionGroup">
                            <div class="accordion-body list-sidebar__accordion-body">
                                <div class="price-range-slider">
                                    <div id="priceRangeSlider"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                        <p class="tw-text-sm tw-text-[#767F8C] tw-mb-0">Min:
                            {{ $currentCurrency->symbol }}<span>0</span></p>
                        <p class="tw-text-sm tw-text-[#767F8C] tw-mb-0">Max:
                            {{ $currentCurrency->symbol }}<span>{{ round($maxSalary, 0) }}</span></p>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input {{ request('price_min') == 10 && request('price_max') == 100 ? 'checked' : '' }}
                            onclick="changeSalary(10, 100)" type="radio" id="10" class="tw-scale-125"
                            name="salleryRange">
                        <label for="10"
                            class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $currentCurrency->symbol }}10 -
                            {{ $currentCurrency->symbol }}100</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input {{ request('price_min') == 100 && request('price_max') == 1000 ? 'checked' : '' }}
                            onclick="changeSalary(100, 1000)" type="radio" id="100" class="tw-scale-125"
                            name="salleryRange">
                        <label for="100"
                            class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $currentCurrency->symbol }}100
                            -
                            {{ $currentCurrency->symbol }}1,000</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input {{ request('price_min') == 1000 && request('price_max') == 10000 ? 'checked' : '' }}
                            onclick="changeSalary(1000, 10000)" type="radio" id="1000" class="tw-scale-125"
                            name="salleryRange">
                        <label for="1000"
                            class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $currentCurrency->symbol }}1,000 -
                            {{ $currentCurrency->symbol }}10,000</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input {{ request('price_min') == 10000 && request('price_max') == 100000 ? 'checked' : '' }}
                            onclick="changeSalary(10000, 100000)" type="radio" id="10000" class="tw-scale-125"
                            name="salleryRange">
                        <label for="10000"
                            class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $currentCurrency->symbol }}10,000 -
                            {{ $currentCurrency->symbol }}100,000</label>
                    </div>
                    <div class="tw-flex tw-gap-2 tw-items-center tw-py-2">
                        <input {{ request('price_min') >= 1000000 && !request('price_max') ? 'checked' : '' }}
                            onclick="changeSalary(1000000)" type="radio" id="100000Up" class="tw-scale-125"
                            name="salleryRange">
                        <label for="100000Up"
                            class="tw-text-sm tw-text-[#18191C] tw-mt-[2px]">{{ $currentCurrency->symbol }}100,000
                            Up</label>
                    </div>
                </div>
                <hr class="tw-bg-[#E4E5E8] tw-m-0">
                <div class="tw-px-5 tw-pt-5">
                    <h2 class="tw-text-sm tw-text-[#0A65CC] tw-mb-2 tw-font-medium">{{ __('category') }}</h2>
                    <label class="tw-block ll-filter-category__item" for="allcategory">
                        <input {{ Route::current()->parameter('category') ? '' : 'checked' }} type="radio"
                            id="allcategory" class="category-radio tw-hidden" aria-data-id="category" value="">
                        <div
                            class="tw-text-sm tw-text-[#18191C] tw-font-medium hover:tw-text-[#0A65CC] tw-flex tw-cursor-pointer hover:tw-bg-[#E7F0FA] tw-px-3 tw-py-2 tw-mt-[2px]">
                            {{ __('all_category') }}</div>
                    </label>
                    @foreach ($categories as $category)
                        <label class="tw-block ll-filter-category__item"
                            for="{{ $category->name }}_{{ $category->id }}">
                            <input {{ $category->slug == Route::current()->parameter('category') ? 'checked' : '' }}
                                type="radio" aria-data-id="category" data-id="{{ $category->slug }}"
                                id="{{ $category->name }}_{{ $category->id }}" class="category-radio tw-hidden"
                                value="{{ $category->slug }}">
                            <div
                                class="tw-text-sm tw-text-[#18191C] tw-font-medium hover:tw-text-[#0A65CC] tw-flex tw-cursor-pointer hover:tw-bg-[#E7F0FA] tw-px-3 tw-py-2 tw-mt-[2px]">
                                {{ $category->name }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div
                class="tw-absolute tw-bottom-0 tw-left-0 tw-right-0 tw-p-5 tw-bg-white tw-z-50 tw-flex sm:tw-flex-row tw-flex-col tw-gap-3 sm:tw-justify-between sm:tw-items-center tw-mt-3">
                <div class="tw-flex tw-items-center tw-w-full ">

                    <label for="remote" class="!tw-flex tw-items-center tw-cursor-pointer">
                        <!-- toggle -->
                        <div class="tw-relative remote-toggle">
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
                    <button type="submit" class="btn btn-primary tw-inline-block">{{ __('apply_filter') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('frontend_scripts')
    <script>
        function keywordClose() {
            $('input[name="keyword"]').val('');
            $('#job_search_form').submit();
        }

        function remotelyClose() {
            $('input[name="is_remote"]').val('');
            $('#job_search_form').submit();
        }
        function categoryClose() {
            console.log("categoryClose function called");
            $('input[aria-data-id="category"]').prop('checked', false);
            $('#job_search_form').submit();
        }


        function jobTypeClose() {
            $('input[name="job_type"]').val('');
            $('#job_search_form').submit();
        }

        function jobSalaryClose() {
            $('input[name="price_min"]').val('');
            $('input[name="price_max"]').val('');
            $('#job_search_form').submit();
        }

        function changeSalary(minsalary, maxsalary) {
            if (minsalary && maxsalary) {
                $('#price_min').val(minsalary)
                $('#price_max').val(maxsalary)
            } else if (minsalary && !maxsalary) {
                $('#price_min').val(minsalary)
                $('#price_max').val('')
            }
        }

        function changeFilter() {
            const slider = document.getElementById('priceRangeSlider')
            const value = slider.noUiSlider.get(true);
            document.getElementById('price_min').value = value[0]
            document.getElementById('price_max').value = value[1]
            const form = $('#job_search_form')
            const data = form.serializeArray();
            // $('#job_search_form').submit()
        }

        function setDefaultPriceRangeValue() {
            const slider = document.getElementById('priceRangeSlider')
            slider.noUiSlider.set([{{ request('price_min') }}, {{ request('price_max') }}]);
        }

        $(document).ready(function() {
            const slider = document.getElementById('priceRangeSlider')
            let maxRange = Number.parseInt("{{ $maxSalary ?? 500 }}")
            let minPrice = 0;
            let maxPrice = maxRange;
            @if (request()->has('price_min') && request()->has('price_max'))
                minPrice = Number.parseInt("{{ request('price_min', 0) }}")
                maxPrice = Number.parseInt("{{ request('price_max', $maxSalary) }}")
            @endif
            noUiSlider.create(slider, {
                start: [minPrice, maxPrice],
                connect: true,
                range: {
                    min: [0],
                    max: [maxRange],
                },
                format: wNumb({
                    decimals: 0,
                    thousand: ',',
                    suffix: ' ({{ $currentCurrency->symbol }})',
                }),
                tooltips: true,
                orientation: 'horizontal',
            });

            slider.noUiSlider.on('change', function() {
                changeFilter();
            });

        });
    </script>
    <script>
        const checkboxes = document.querySelectorAll('.category-radio');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', (event) => {
                checkboxes.forEach(cb => {
                    if (cb !== event.target) {
                        cb.checked = false; // Uncheck other checkboxes
                    }
                });
            });
        });
    </script>
@endpush
