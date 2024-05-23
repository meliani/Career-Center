<x-filament-breezy::grid-section md=2 title="{{__('Know you better')}}"
    description="{{__('Please fill in the form below to help us know you better.')}}">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">

            {{ $this->form }}

            <div class="text-right">
                <x-filament::button type="submit" form="submit" class="align-right">
                    {{__('Update')}}
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>