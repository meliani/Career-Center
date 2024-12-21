<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="font-medium text-gray-500">{{__('From')}}:</span>
            <span class="ml-2">{{ $record->sender_name ?: $record->sender_email }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-500">{{__('To')}}:</span>
            <span class="ml-2">{{ $record->recipient_name ?: $record->recipient_email }}</span>
        </div>
        <div class="col-span-2">
            <span class="font-medium text-gray-500">{{__('Subject')}}:</span>
            <span class="ml-2">{{ $record->subject }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-500">{{__('Sent')}}:</span>
            <span class="ml-2">{{ $record->created_at->format('M j, Y g:i A') }}</span>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full gap-x-1 {{ $record->opens > 0 ? 'bg-success-100 text-success-700' : 'bg-gray-100 text-gray-700' }}">
                <x-heroicon-m-eye class="w-4 h-4" />
                {{ $record->opens }} {{__('opens')}}
            </span>
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full gap-x-1 ml-2 {{ $record->clicks > 0 ? 'bg-info-100 text-info-700' : 'bg-gray-100 text-gray-700' }}">
                <x-heroicon-m-cursor-arrow-rays class="w-4 h-4" />
                {{ $record->clicks }} {{__('clicks')}}
            </span>
        </div>
    </div>

    <div class="border rounded-lg">
        <div class="p-4 max-w-none bg-white rounded-lg">
            {!! $record->content !!}
        </div>
    </div>
</div>
