<x-filament-widgets::widget>
    <x-filament::card>
        <div class="space-y-6">
            <!-- Header Section with Icon -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-primary-100 rounded-lg">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-primary-500" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ __('Your Internship Journey') }}
                    </h2>
                </div>
                @if($progress < 100)
                    <x-filament::badge color="warning">
                        {{ __('In Progress') }}
                    </x-filament::badge>
                @else
                    <x-filament::badge color="success">
                        {{ __('Completed') }}
                    </x-filament::badge>
                @endif
            </div>

            <!-- Progress Section -->
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('Completion Progress') }}</span>
                    <span class="font-medium text-primary-600">{{ round($progress) }}%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <!-- Steps Section -->
            <div class="space-y-4">
                @foreach ($steps as $index => $step)
                <div class="flex items-center space-x-3">
                    @if($step['status'] === 'completed')
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-success-500">
                        <x-heroicon-o-check class="w-5 h-5 text-white" />
                    </div>
                    @elseif($step['status'] === 'current')
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-primary-500">
                        <span class="text-sm font-medium text-white">{{ $index + 1 }}</span>
                    </div>
                    @else
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-200">
                        <span class="text-sm font-medium text-gray-600">{{ $index + 1 }}</span>
                    </div>
                    @endif
                    <span class="text-sm {{ $step['status'] === 'completed' ? 'text-gray-500 line-through' : ($step['status'] === 'current' ? 'text-primary-600 font-medium' : 'text-gray-700') }}">
                        {{ $step['title'] }}
                    </span>
                </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                <x-filament::button
                    wire:click="redirectToProfile"
                    icon="heroicon-o-user"
                    color="primary"
                    size="sm"
                >
                    {{ __('Update Profile') }}
                </x-filament::button>

                <x-filament::button
                    wire:click="redirectToOffers"
                    icon="heroicon-o-briefcase"
                    color="success"
                    size="sm"
                >
                    {{ __('View Offers') }}
                </x-filament::button>

                <x-filament::button
                    wire:click="redirectToAnnounceInternship"
                    icon="heroicon-o-document-text"
                    color="gray"
                    size="sm"
                >
                    {{ __('Announce Internship') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
