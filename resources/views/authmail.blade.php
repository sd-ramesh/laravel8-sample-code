@component('mail::message')

Verify your account.

@component('mail::button', ['url' => 'https://google.com?token='])
Confirm
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent