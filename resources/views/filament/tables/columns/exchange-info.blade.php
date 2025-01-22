@if($getRecord()->is_mobility)
    <div class='flex flex-col gap-1'>
        <span class='inline-flex items-center justify-center rounded-full bg-warning-100 px-2.5 py-0.5 text-warning-700 text-sm font-medium'>
            {{ __('Exchange Student') }}
        </span>
        <div class='text-sm'>
            <strong>{{ $getRecord()->exchangePartner?->name }}</strong><br>
            {{-- <span class='text-gray-500'>{{ $getRecord()->exchangePartner?->full_address }}</span> --}}
        </div>
    </div>
@endif
