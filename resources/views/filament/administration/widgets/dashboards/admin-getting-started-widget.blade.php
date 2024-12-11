<x-filament-widgets::widget>
    <x-filament::card class="transition-transform transform hover:scale-105 duration-300">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl shadow">
                        <x-heroicon-o-chart-bar class="w-6 h-6 text-primary-600" />
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ __('System Overview') }}
                    </h2>
                </div>
                <select
                    wire:model.live="selectedTrendPeriod"
                    class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                >
                    <option value="" disabled>{{ __('Select time period') }}</option>
                    @foreach($trendPeriods as $value => $label)
                        <option value="{{ $value }}">{{ __('Compare:') }} {{ __($label) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-2 gap-4">
                @foreach($statistics as $stat)
                    @if(auth()->user()->can('viewAny', $stat['model_class']))
                        <div class="group relative">
                            @if($stat['route'] ?? false)
                                <a href="{{ $stat['route'] }}" class="block">
                            @endif
                            <div class="bg-white p-4 rounded-lg shadow-sm border group-hover:bg-{{ $stat['color'] }}-50 group-hover:border-{{ $stat['color'] }}-200 transition-all duration-300 ease-in-out relative">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-2">
                                        <div class="text-sm text-gray-500 group-hover:text-{{ $stat['color'] }}-600">{{ $stat['label'] }}</div>
                                        <div class="flex items-center space-x-2">
                                            <div class="text-3xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</div>
                                            @if(isset($stat['trend']))
                                                @if($stat['trend'] > 0)
                                                    <div class="flex items-center text-success-500" title="{{ __('Increase from previous period') }}">
                                                        <x-heroicon-s-arrow-trending-up class="w-5 h-5" />
                                                        <span class="text-xs ml-1">+{{ $stat['trend'] }}%</span>
                                                    </div>
                                                @elseif($stat['trend'] < 0)
                                                    <div class="flex items-center text-danger-500" title="{{ __('Decrease from previous period') }}">
                                                        <x-heroicon-s-arrow-trending-down class="w-5 h-5" />
                                                        <span class="text-xs ml-1">{{ $stat['trend'] }}%</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center text-gray-500" title="{{ __('No change from previous period') }}">
                                                        <x-heroicon-s-minus class="w-5 h-5" />
                                                        <span class="text-xs ml-1">0%</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-2 rounded-lg bg-{{ $stat['color'] }}-100/50 group-hover:bg-{{ $stat['color'] }}-100">
                                        @switch($stat['key'] ?? '')
                                            @case('new_offers')
                                                <x-heroicon-o-briefcase class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                                @break
                                            @case('pending_offers')
                                                <x-heroicon-o-clock class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                                @break
                                            @case('applications')
                                                <x-heroicon-o-document-text class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                                @break
                                            @case('active_users')
                                                <x-heroicon-o-users class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                                @break
                                            @case('agreements')
                                                <x-heroicon-o-document-check class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                                @break
                                            @default
                                                <x-heroicon-o-chart-bar class="w-5 h-5 text-{{ $stat['color'] }}-500" />
                                        @endswitch
                                    </div>
                                </div>
                                @if(isset($stat['description']))
                                    <p class="mt-2 text-xs text-gray-500 group-hover:text-{{ $stat['color'] }}-600">{{ $stat['description'] }}</p>
                                @endif
                                @if($stat['route'] ?? false)
                                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 text-{{ $stat['color'] }}-500" />
                                    </div>
                                @endif
                            </div>
                            @if($stat['route'] ?? false)
                                </a>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Recent Activities -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Activities') }}</h3>
                @forelse($recentActivities as $activity)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                            <span class="text-sm text-gray-700">{{ $activity['title'] }}</span>
                        </div>
                        <x-filament::badge color="primary">
                            {{ $activity['time'] }}
                        </x-filament::badge>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 text-center py-4">
                        {{ __('No recent activities') }}
                    </div>
                @endforelse
            </div>

        </div>
    </x-filament::card>
</x-filament-widgets::widget>
