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
                        {{ __('Getting Started Guide') }}
                    </h2>
                </div>
                <x-filament::badge color="success">
                    {{ __('New') }}
                </x-filament::badge>
            </div>

            <!-- Progress Section -->
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('Completion Progress') }}</span>
                    <span class="font-medium text-primary-600">25%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full" style="width: 25%"></div>
                </div>
            </div>

            <!-- Steps Section -->
            <div class="space-y-4">
                @foreach ([
                ['title' => __('Overview of the Dashboard'), 'status' => 'completed'],
                ['title' => __('Managing Students'), 'status' => 'current'],
                ['title' => __('Handling Applications'), 'status' => 'pending'],
                ['title' => __('Reviewing Reports'), 'status' => 'pending'],
                ] as $step)
                <div class="flex items-center space-x-3">
                    @if($step['status'] === 'completed')
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-success-500">
                        <x-heroicon-o-check class="w-5 h-5 text-white" />
                    </div>
                    @elseif($step['status'] === 'current')
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-primary-500">
                        <span class="text-sm font-medium text-white">2</span>
                    </div>
                    @else
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-200">
                        <span class="text-sm font-medium text-gray-600">•</span>
                    </div>
                    @endif
                    <span
                        class="text-sm {{ $step['status'] === 'completed' ? 'text-gray-500 line-through' : 'text-gray-700' }}">
                        {{ $step['title'] }}
                    </span>
                </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3">
                <x-filament::button size="sm" color="primary">
                    {{ __('Continue Learning') }}
                </x-filament::button>
                <x-filament::button size="sm" color="gray">
                    {{ __('View All Guides') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>