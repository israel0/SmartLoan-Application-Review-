@component('mail::message')
# Introduction
Hello {{ $name }}

This is your email address: {{ $email }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
