@component('mail::message')
# Hello {{ $user->user->name }}

Please find attached your job report.


This report includes all job listings submitted by your council.

Thank you for using our portal!

Best regards,<br>
{{ config('app.name') }}
@endcomponent
