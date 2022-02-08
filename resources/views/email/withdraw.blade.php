@component('mail::message')
Hello {{Auth::user()->name}},

Please confirm your withdrawal of {{Auth::user()->currency}} {{$amt}} using the code below.

Code: {{$code}}

@component('mail::button', ['url' => 'https://pay.lifegeegs.com/merchant/confirm/'.$ref])
Confirm
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
