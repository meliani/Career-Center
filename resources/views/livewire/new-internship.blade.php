<x-filament::card>
    <x-filament-panels::form wire:submit="create">
        {{ $this->form }}
        <div>
            <x-filament::button type="submit" size="xl">
                Submit
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament::card>