@component('mail::message')
{!! $content !!}

@component('mail::button', ['url' => $url])
View Student Information
@endcomponent

@endcomponent
