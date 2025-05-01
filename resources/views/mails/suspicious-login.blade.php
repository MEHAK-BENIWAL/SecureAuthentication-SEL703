@component('mail::message')
# 🚨 Suspicious Login Attempt Detected

There have been **{{ $attempts }} failed login attempts** to your account.

- **Email**: {{ $email }}
- **IP Address**: {{ $ip }}
- **Time**: {{ now()->toDateTimeString() }}

If this wasn't you, we recommend changing your password immediately.

<!-- @component('mail::button', ['url' => route('password.request')]) -->
<!--Reset Password-->
<!--@endcomponent-->

Stay safe,  
{{ config('app.name') }}
@endcomponent