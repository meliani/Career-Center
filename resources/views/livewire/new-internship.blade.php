<div>
    <div class="flex justify-center py-4 bg-gray-100 dark:bg-gray-800">
        <div x-data="{ mode: 'light' }" x-on:dark-mode-toggled.window="mode = $event.detail">
            <span x-show="mode === 'light'" class="block">
                <img src="{{ asset('/svg/logo-colors.svg') }}" alt="Logo" class="h-20 mx-auto">
            </span>

            <span x-show="mode === 'dark'" class="block">
                <img src="{{ asset('/svg/logo-white.svg') }}" alt="Logo" class="h-20 mx-auto">
            </span>
        </div>
    </div>
    <x-filament::card>
        <x-filament-panels::form wire:submit="create">
            {{ $this->form }}
            <div>
                <x-filament::button type="submit" size="xl">
                    {{ __('Publish Offer') }}
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::card>
</div>