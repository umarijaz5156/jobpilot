@extends('backend.layouts.app')
@section('title')
    {{ __('job_list') }}
@endsection
@section('content')
    @php
        $userr = auth()->user();
    @endphp

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

<div class="container">
    <h1>Select Jobs</h1>
    <form method="POST" action="{{ route('jobs.updateFeatured') }}">
        @csrf
        <div class="form-group">
            <label for="jobs">Jobs</label>
            <select id="jobs" name="featured_jobs[]" class="form-control" multiple="multiple" style="width: 100%;">
                @foreach ($all_jobs as $job)
                    <option value="{{ $job->id }}" {{ in_array($job->id, $featured_jobs->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $job->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>



@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#jobs').select2({
            placeholder: 'Select jobs',
            allowClear: true
        });
    });
</script>
{{-- <script>
    $(document).ready(function() {
        // Fetch jobs from backend
        var allJobs = @json($all_jobs);
        var featuredJobs = @json($featured_jobs->pluck('id'));

        // Populate the select element with all jobs
        allJobs.forEach(function(job) {
            var isSelected = featuredJobs.includes(job.id) ? 'selected' : '';
            $('#jobs').append('<option value="' + job.id + '" ' + isSelected + '>' + job.title + '</option>');
        });

        // Initialize Select2
        $('#jobs').select2({
            placeholder: 'Select jobs',
            allowClear: true
        });
    });
</script> --}}

@endsection
