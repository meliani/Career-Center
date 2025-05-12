{{-- Title box component for agreement templates --}}
<div class="border-2 border-gray-500 p-2 mx-auto max-w-sm mt-10 mb-6">
    <h1 class="text-lg font-semibold text-center mb-2">
        {{ $mainTitle }}
    </h1>
    @if(isset($subtitle))
    <h2 class="text-xs text-center mx-auto max-w-sm">
        {!! $subtitle !!}
    </h2>
    @endif
</div>
