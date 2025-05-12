{{-- INPT Logo Component --}}
<div>
    {!! str_replace('<svg', '<svg width="130" height="63.8" ', file_get_contents(public_path('svg/logo-colors.svg'))) !!}
</div>