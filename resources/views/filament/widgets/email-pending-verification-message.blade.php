<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card class="p-0">
            <div class="p-1">
                <h2 class="text-base font-semibold text-gray-800">
                    {{ __('You should verify your INPT email address') }}
                </h2>
                <p class="text-gray-600">
                    {{-- you will be verified by administration --}}
                    {{ __('Please check your email inbox for a verification email') }}
                </p>
            </div>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>