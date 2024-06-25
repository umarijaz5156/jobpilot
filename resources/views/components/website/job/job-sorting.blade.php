@props(['popularTags'])

<div class="row">
    <div class="col-lg-12">
        <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-2 tw-mb-6 tw-mx-1.5 sm:tw-mx-0">
            <p class="tw-text-[#767F8C] tw-text-sm tw-mb-0">{{ __('popular_tag') }}:</p>
            <ul class="tw-popular-search tw-flex-wrap">
                @if (request('tag') && is_string(request('tag')))
                    <input type="hidden" value="{{ request('tag') }}" name="tag">
                @else
                    <input type="hidden" value="" name="tag">
                @endif
                @foreach ($popularTags as $tag)
                    <li onclick="tagFilter('{{ $tag->name }}')" class="tw-text-bold {{ request('tag') == $tag->name ? 'tw-bg-blue-50 tw-text-blue-500 tw-font-semibold':'' }}">
                        <a href="#">{{ $tag->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@section('frontend_scripts')
    <script>
        $('#sort_by').on('change', function() {
            $('#job_search_form').submit();
        })
    </script>
    <script>
        $('#radius').on('change', function() {
            $('#job_search_form').submit();
        })

        function tagFilter(tagid) {
            $('input[name=tag]').val(tagid)
            $('#job_search_form').submit();
        }
    </script>
@endsection
