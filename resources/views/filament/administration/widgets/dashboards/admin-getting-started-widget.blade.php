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
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">{{ __('Total platform Users') }}</div>
                    <div class="text-2xl font-bold text-primary-600">{{ $statistics['total_users'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">{{ __('New Internship Offers') }}</div>
                    <div class="text-2xl font-bold text-success-600">{{ $statistics['new_offers'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">{{ __('Applications') }}</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $statistics['applications'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border">
                    <div class="text-sm text-gray-500">{{ __('Active Users') }}</div>
                    <div class="text-2xl font-bold text-info-600">{{ $statistics['active_users'] }}</div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Recent Activities') }}</h3>
                @foreach($recentActivities as $activity)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                            <span class="text-sm text-gray-700">{{ $activity['title'] }}</span>
                        </div>
                        <x-filament::badge color="primary">
                            {{ $activity['time'] }}
                        </x-filament::badge>
                    </div>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="flex space-x-3">
                <x-filament::button size="sm" color="primary" icon="heroicon-o-plus">
                    {{ __('New Application') }}
                </x-filament::button>
                <x-filament::button size="sm" color="secondary" icon="heroicon-o-user-group">
                    {{ __('Manage Users') }}
                </x-filament::button>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
