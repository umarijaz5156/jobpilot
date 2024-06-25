<div class="{{ $row ? 'row':'select-wrapper mx-0 w-100 d-flex flex-column' }}">
    <div class="{{ $row ? 'col-lg-4':'px-0 w-100' }}">
        <select id="" name="country" wire:model="selectedCountryId" class="select21 location city max-w-100">
            <option value="">Select Country</option>
            @foreach ($countries as $country)
                <option value="{{ $country['name'] }}" required>{{ $country['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="{{ $row ? 'col-lg-4':'px-0 w-100' }}">
        <select name="state" wire:model="selectedStateId" class="select21 location zone max-w-100">
            <option value="">Select State </option>
            @foreach ($states as $state)
                <option value="{{ $state['name'] }}">{{ $state['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="{{ $row ? 'col-lg-4':'px-0 w-100' }}">
        <select name="district" wire:model="selectedCityId" class="select21 location area max-w-100">
            <option value="">Select City </option>
            @foreach ($cities as $city)
                <option value="{{ $city['name'] }}">{{ $city['name'] }}</option>
            @endforeach
        </select>
    </div>
</div>
<style>
    .select-wrapper {
        gap: 16px;
    }
    .location+.bigdrop,
    .location+.select2-container {
        width: 100% !important;
    }

    @media (max-width: 1199px) {
        .location+.select2-container {
            margin: 4px 0px;
        }
    }
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px !important;
    }
    .card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
    }
</style>

@push('js')
    <script>
        $(".location.city").on('change', function(e) {
            let id = $(this).val()
            @this.set('selectedCountryId', id);
            livewire.emit('getStateByCountryId');
        })


        $(".location.zone").on('change', function(e) {
            let id = $(this).val()
            @this.set('selectedStateId', id);
            livewire.emit('getCityByStateId', id);
        })

        $(".location.area").on('change', function(e) {
            let id = $(this).val()
            @this.set('selectedCityId', id);
        })
    </script>
@endpush
