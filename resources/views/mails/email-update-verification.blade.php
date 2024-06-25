<x-mail::message>
# Email Verification

Hello {{ $user->name }}

We received an email update request from you.
Please verify your new  email address by clicking the button below

<x-mail::button :url="$url">
    Verify
</x-mail::button>

If you don't have an account on our website,you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
