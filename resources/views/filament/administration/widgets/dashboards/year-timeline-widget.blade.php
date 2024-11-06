<x-filament-widgets::widget>
    <x-filament::card class="relative overflow-hidden">
        {{-- <div class="absolute top-0 right-0 w-32 h-32 transform rotate-45 translate-x-16 -translate-y-16 bg-primary-50"></div> --}}

        <div class="space-y-6 relative">
            <!-- Enhanced Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl shadow-lg transform transition-transform hover:scale-110 duration-200">
                        <x-heroicon-o-calendar class="w-6 h-6 text-primary-600" />
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ __('Academic Year Timeline') }}
                    </h2>
                </div>
                <x-filament::button size="sm" color="secondary" icon="heroicon-o-calendar">
                    {{ now()->format('Y') }}
                </x-filament::button>
            </div>

            <!-- Improved Timeline -->
            <div class="relative border-l-4 border-primary-200 dark:border-primary-700 ml-4 space-y-6">
                @foreach($events as $event)
                    <div class="group relative hover:bg-gray-50 rounded-lg p-4 transition-all duration-300 -ml-2 cursor-pointer">
                        <!-- Timeline Dot -->
                        <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-6 ring-4 ring-white dark:ring-gray-900 group-hover:ring-primary-50 transition-all duration-300
                            {{ $event['date'] < now() ? 'bg-success-500' : 'bg-primary-500' }}">
                            @if($event['date'] < now())
                                <x-heroicon-o-check class="w-4 h-4 text-white" />
                            @else
                                <x-heroicon-o-calendar class="w-4 h-4 text-white" />
                            @endif
                        </span>

                        <!-- Event Content -->
                        <div class="pl-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-200">
                                    {{ $event['title'] }}
                                </h3>
                                <time class="text-sm font-medium text-primary-500 bg-primary-50 px-2 py-1 rounded-full mt-2 sm:mt-0">
                                    {{ \Carbon\Carbon::parse($event['date'])->format('F j, Y') }}
                                </time>
                            </div>

                            <div class="flex items-center space-x-2">
                                <p class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors duration-200">
                                    {{ $event['description'] }}
                                </p>
                                @if($event['date'] >= now())
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-primary-100 bg-primary-600 rounded-full">
                                        {{ \Carbon\Carbon::parse($event['date'])->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Hover Effect Border -->
                        <div class="absolute left-0 top-0 w-1 h-full bg-primary-500 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-200"></div>
                    </div>
                @endforeach
            </div>

            <!-- Timeline Footer -->
            <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                <x-filament::button size="sm" color="primary" icon="heroicon-o-plus" class="transform transition-transform hover:scale-105">
                    {{ __('Add Event') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
