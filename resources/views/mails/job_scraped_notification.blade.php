<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Jobs Added</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        h1, h2 {
            color: #333;
        }
        .signature {
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Hi {{ $user->name }},</h1>
    <p>We hope this message finds you well.</p>

    <h2>New Jobs Added for {{ $user->name }}</h2>
    <p>The following positions have been successfully added to the <strong>Council Direct</strong> network:</p>
    <ul>
        @foreach ($detailedJobs as $job)
            <li>
                <strong>{{ $job->title }}</strong> -
                <a href="{{ route('website.job.details', $job->slug) }}" target="_blank">View Job</a>
            </li>
        @endforeach
    </ul>

    <p>You can view all jobs for this company here:
        <a href="{{ route('website.employe.details', $user->username) }}" target="_blank">
            {{ $user->name }} Page
        </a>.
    </p>

    <p>If you have any updates or changes to the advertised positions, please let us know at
        <a href="mailto:support@councildirect.com.au">support@councildirect.com.au</a>.
    </p>

    <p class="signature">
        Best regards,<br>
        <strong>Council Direct Team</strong><br>
        <i>Email:</i> <a href="mailto:support@councildirect.com.au">support@councildirect.com.au</a><br>
        <i>Website:</i> <a href="https://www.councildirect.com.au/" target="_blank">www.councildirect.com.au</a>
        <img style="margin-top: 10px;" src="{{ asset('signature.png') }}" alt="Signature">

    </p>
</body>
</html>
