@php
    $names = $getRecord()->assignedUsers->pluck('name')->join(', ');
@endphp

<div
    class="flex"
>
<div
    x-data
    x-tooltip.raw.placement.top="{{ $names }}"
    class="flex -space-x-2 overflow-hidden pointer-events-auto relative"
>
    @foreach($getRecord()->assignedUsers as $user)
        <x-filament-panels::avatar.user :user="$user"/>
    @endforeach
</div>
</div>
