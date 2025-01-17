<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Jobs Added</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h1 style="color: #333;">Hello {{ $user->name }},</h1>

    <h2>New Jobs Added for {{ $user->name }}</h2>
    <p>We have successfully added the following jobs to our system:</p>
    <ul>
        @foreach ($detailedJobs as $job)
            <li>
                <strong>{{ $job->title }}</strong> -
                <a href="{{ route('website.job.details', $job->slug) }}" target="_blank" style="color: #007bff;">View Job</a>
            </li>
        @endforeach
    </ul>

    <p>
        You can view all jobs for this company here:
        <a href="{{ route('website.employe.details', $user->username) }}" target="_blank" style="color: #007bff;">
            {{ $user->name }} Page
        </a>.
    </p>

    <p>Best regards,</p>
    <p><strong>Council Direct Team</strong></p>
</body>
</html>
