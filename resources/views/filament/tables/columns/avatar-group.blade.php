@php
    $names = $getRecord()->assignedUsers->pluck('name')->join(', ');
@endphp

<div 
    x-data
    x-tooltip.raw="{{ $names }}"
    class="flex -space-x-2 overflow-hidden pointer-events-auto"
>
    @foreach($getRecord()->assignedUsers as $user)
        <x-filament-panels::avatar.user :user="$user"/>
    @endforeach
</div>
