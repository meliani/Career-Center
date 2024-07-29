<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card class="p-0">
            <div class="p-1">
                <h2 class="text-base font-semibold text-gray-800">
                    {{ __('Your account pending verification by administration') }}.
                </h2>
                <p class="text-gray-600">
                    {{ __('Right after verification, you will be able to access all features of the application') }}.
                    {{ __('Verification process may take up to 24 hours') }}.
                </p>
                <h2 class="text-base font-semibold text-gray-800">
                    {{-- fill inyour profile while --}}
                    {{ __('You can fill in your profile while waiting for verification') }}.
                </h2>
            </div>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>