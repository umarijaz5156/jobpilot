

@extends('backend.layouts.app')
@section('title')
    {{ __('Feature Companies') }}
@endsection
@section('content')
  


<div class="container">
    <h1>Select Jobs</h1>
    <form method="POST" action="{{ route('company.updateFeatured') }}">
        @csrf
        <div class="form-group">
            <label for="Companies">Companies</label>
            <select id="Companies" name="featured_Companies[]" class="form-control" multiple="multiple" style="width: 100%;">
                @foreach ($all_companies as $company)
                    <option value="{{ $company->id }}" {{ in_array($company->id, $featured_companies->pluck('id')->toArray()) ? 'selected' : '' }}>
                    
                        {{ $company->user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#Companies').select2({
            placeholder: 'Select Companies',
            allowClear: true
        });
    });
</script>


@endsection
