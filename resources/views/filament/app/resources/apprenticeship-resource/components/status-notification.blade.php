<div class="p-4 border rounded-lg shadow-sm mb-5 bg-gradient-to-r from-{{ $color }}-50 to-white">
    <div class="flex items-center">
        <div class="mr-4">
            <x-dynamic-component :component="$icon" class="w-8 h-8 text-{{ $color }}-500" />
        </div>
        <div>
            <h3 class="text-lg font-medium text-{{ $color }}-700">{{ $title }}</h3>
            <p class="text-{{ $color }}-600">{{ $message }}</p>
        </div>
    </div>
</div>
