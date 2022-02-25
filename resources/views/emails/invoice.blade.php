@component('mail::message')
Hello there ,

You have received an invoice of {{$currency}}{{$amount}} reference {{$refcode}}

Please use the button below to pay the invoice

Note from the sender

{{$description}}

@component('mail::button', ['url' => ''])
Pay invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
