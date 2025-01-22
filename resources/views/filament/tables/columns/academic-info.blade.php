<div class="flex flex-col gap-1">
    <div class="flex gap-2">
        <span class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-0.5 text-primary-700 text-sm font-medium">
            {{ $getRecord()->level?->getLabel() }}
        </span>
        <span class="inline-flex items-center justify-center rounded-full bg-success-100 px-2.5 py-0.5 text-success-700 text-sm font-medium">
            {{ $getRecord()->program?->getLabel() }}
        </span>
    </div>
    <span class="text-sm text-gray-500">{{ $getRecord()->year?->title }}</span>
</div>
