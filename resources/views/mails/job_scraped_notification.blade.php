@component('mail::message')
# Hello {{ $user->name }}

<h1>New Jobs Added for {{ $user->name }}</h1>
<p>We have successfully added the following jobs to our system:</p>
<ul>
    @foreach ($detailedJobs as $job)
        <li>
            <strong>{{ $job->title }}</strong> -
            <a href="{{ route('website.job.details', $job->slug) }}">View Job</a>
        </li>
    @endforeach
</ul>
<p>You can view all jobs for this company here:
    <a href="{{ route('website.employe.details', $user->username) }}">
        {{ $user->name }} Page
    </a>.
</p>

Best regards,<br>
Council Direct Team
{{-- {{ config('app.name') }} --}}
@endcomponent
