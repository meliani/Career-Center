@if($getRecord()->is_mobility)
    <div class='flex flex-col gap-2'>
        <div class="flex flex-wrap gap-2">
            {{-- Exchange Student Badge --}}
            <span class='inline-flex items-center justify-center gap-1 rounded-full bg-warning-100 py-0.5 text-warning-700 text-sm font-medium'>
                @svg('heroicon-m-globe-alt', 'w-4 h-4')
                {{ __('Exchange Student') }}
            </span>

            {{-- Exchange Type Badge --}}
            @if($getRecord()->exchange_type)
                <span class='inline-flex items-center justify-center gap-1 rounded-full py-0.5 text-sm font-medium
                    {{ $getRecord()->exchange_type->value === 'Inbound' 
                        ? 'bg-green-100 text-green-700' 
                        : 'bg-blue-100 text-blue-700' }}'>
                    @if($getRecord()->exchange_type->value === 'Inbound')
                        @svg('heroicon-m-arrow-down-circle', 'w-4 h-4')
                    @else
                        @svg('heroicon-m-arrow-up-circle', 'w-4 h-4')
                    @endif
                    {{ $getRecord()->exchange_type->getLabel() }}
                </span>
            @endif
        </div>

        {{-- Partner Institution Info --}}
        @if($getRecord()->exchangePartner)
            <div class='flex flex-col text-sm'>
                <div class="flex items-center gap-1 font-medium text-gray-900">
                    @svg('heroicon-m-building-office', 'w-4 h-4 text-gray-500')
                    {{ $getRecord()->exchangePartner->name }}
                </div>
                @if($getRecord()->exchangePartner->city && $getRecord()->exchangePartner->country)
                    <div class="flex items-center gap-1 text-gray-500">
                        @svg('heroicon-m-map-pin', 'w-4 h-4')
                        {{ $getRecord()->exchangePartner->city }}, {{ $getRecord()->exchangePartner->country }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endif
