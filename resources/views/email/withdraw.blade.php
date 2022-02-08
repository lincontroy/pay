@component('mail::message')
Hello {{$name}},

Please confirm your withdrawal of {{$currency}} {{$amt}} using the code below.

Code: {{$code}}

@component('mail::button', ['url' => 'https://pay.lifegeegs.com/merchant/confirm/'.$ref])
Confirm
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
