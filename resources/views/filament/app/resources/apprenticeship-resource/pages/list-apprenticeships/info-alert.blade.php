<div class="p-4 rounded-lg bg-{{ $type }}-50 text-{{ $type }}-700 mb-6">
    <div class="flex items-center">
        @if (isset($icon))
            <x-dynamic-component :component="$icon" class="w-5 h-5 mr-3" />
        @endif
        <div>
            <p>{{ $message }}</p>
        </div>
    </div>
</div>
