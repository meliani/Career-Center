<x-filament-widgets::widget>
    <x-filament::card class="relative overflow-hidden">
        <!-- Add animated background pattern -->
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <div class="absolute inset-0" style="background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")"></div>
        </div>

        <div class="space-y-6 relative">
            <!-- Header Section with Icon and Student Info -->
            <div class="flex flex-col space-y-4">
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

                <!-- Add Student Quick Info -->
                <div class="bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:bg-gray-100">
                    <div class="flex items-center space-x-4">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ Storage::url(auth()->user()->avatar_url) }}"
                                 class="w-16 h-16 rounded-full border-2 border-primary-500 transition-transform duration-200 hover:scale-110"
                                 alt="{{ auth()->user()->full_name }}">
                        @else
                            <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                                <x-heroicon-o-user class="w-8 h-8 text-primary-500" />
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-medium">{{ auth()->user()->full_name }}</h3>
                            <div class="text-sm text-gray-500 space-y-1">
                                <p class="flex items-center space-x-2">
                                    <x-heroicon-o-academic-cap class="w-4 h-4" />
                                    <span>{{ auth()->user()->program?->getLabel() }} - {{ auth()->user()->level?->getLabel() }}</span>
                                </p>
                                @if(auth()->user()->email_perso)
                                    <p class="flex items-center space-x-2">
                                        <x-heroicon-o-envelope class="w-4 h-4" />
                                        <span>{{ auth()->user()->email_perso }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Quick Stats Section -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:bg-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-eye class="w-5 h-5 text-primary-500" />
                            <span class="text-sm font-medium text-gray-500">{{ __('Offers Viewed') }}</span>
                        </div>
                        <span class="text-xl font-bold text-primary-600">
                            {{ auth()->user()->getViewedOffersCount() }}
                        </span>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:bg-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-paper-airplane class="w-5 h-5 text-success-500" />
                            <span class="text-sm font-medium text-gray-500">{{ __('Applications') }}</span>
                        </div>
                        <span class="text-xl font-bold text-success-600">
                            {{ auth()->user()->applications()->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Progress Section with Animation -->
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('Completion Progress') }}</span>
                    <span class="font-medium text-primary-600 transition-all duration-500">{{ round($progress) }}%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full transition-all duration-1000 ease-out"
                         style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <!-- Steps Section with Hover Effects -->
            <div class="space-y-4">
                @foreach ($steps as $index => $step)
                <div class="space-y-2">
                    <div class="group flex items-center space-x-3 p-2 rounded-lg transition-all duration-200 hover:bg-gray-50">
                        <!-- Status Circle with Animation -->
                        @if($step['status'] === 'completed')
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-success-500 transform transition-transform duration-200 group-hover:scale-110">
                                <x-heroicon-o-check class="w-5 h-5 text-white" />
                            </div>
                        @elseif($step['status'] === 'current')
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-primary-500 transform transition-transform duration-200 group-hover:scale-110 animate-pulse">
                                <span class="text-sm font-medium text-white">{{ $index + 1 }}</span>
                            </div>
                        @else
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 transform transition-transform duration-200 group-hover:scale-110">
                                <span class="text-sm font-medium text-gray-600">{{ $index + 1 }}</span>
                            </div>
                        @endif

                        <!-- Step Title with Hover Effect -->
                        <span class="text-sm transition-colors duration-200 {{
                            $step['status'] === 'completed'
                                ? 'text-gray-500 line-through'
                                : ($step['status'] === 'current'
                                    ? 'text-primary-600 font-medium'
                                    : 'text-gray-700')
                        }} group-hover:text-primary-600">
                            {{ $step['title'] }}
                        </span>
                    </div>

                    <!-- Detail Items with Hover Effects -->
                    @if(isset($step['details']))
                        <div class="ml-11 space-y-2">
                            @foreach($step['details'] as $key => $detail)
                                <div class="flex items-center space-x-2 text-xs p-1.5 rounded-md transition-all duration-200 hover:bg-gray-50">
                                    @if($detail['status'])
                                        @if($key === 'viewed')
                                            <x-heroicon-o-eye class="w-4 h-4 text-primary-500" />
                                            <span class="text-primary-600 font-medium">{{ $detail['label'] }}</span>
                                        @else
                                            <x-heroicon-o-check-circle class="w-4 h-4 text-success-500" />
                                            <span class="text-gray-500 line-through">{{ $detail['label'] }}</span>
                                        @endif
                                    @else
                                        <x-heroicon-o-exclamation-circle class="w-4 h-4 text-warning-500" />
                                        <span class="text-gray-700 font-medium">{{ $detail['label'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Action Buttons with Hover Effects -->
            <div class="flex flex-wrap gap-3">
                @foreach ($this->buttons as $button)
                    <x-filament::button
                        wire:click="{{ $button['action'] }}"
                        :icon="$button['icon']"
                        :color="$button['color']"
                        :size="$button['size'] ?? 'sm'"
                        class="transition-transform duration-200 hover:scale-105"
                    >
                        {{ __($button['label']) }}
                    </x-filament::button>
                @endforeach
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
