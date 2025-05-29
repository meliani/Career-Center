<div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow">
    <div class="flex items-center justify-between px-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Department Mentoring Statistics') }}
        </h3>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Supervising + Reviewing Averages') }}
        </div>
    </div>
    
    <div class="flex items-center justify-center gap-4 overflow-x-auto py-2 px-4">
        <div class="flex items-center gap-4 min-w-fit">
            @foreach ($stats as $stat)
                <div class="flex flex-col gap-2 bg-gray-50 dark:bg-gray-900/50 px-4 py-3 rounded-lg min-w-[200px]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component :component="$stat['icon']" class="w-4 h-4" />
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                {{ $stat['name'] }}
                            </span>
                        </div>
                        <x-filament::badge :color="$stat['color']" size="xs">
                            {{ $stat['projects_count'] }} {{ __('projects') }}
                        </x-filament::badge>
                    </div>
                    
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <div class="flex justify-between mb-1">
                            <span class="font-medium">{{ $stat['professors_count'] }} {{ __('professors') }}</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span>{{ __('Avg Supervising') }}:</span>
                                <span class="font-medium">{{ $stat['avg_supervising'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>{{ __('Avg Reviewing') }}:</span>
                                <span class="font-medium">{{ $stat['avg_reviewing'] }}</span>
                            </div>
                            <div class="flex justify-between border-t pt-1">
                                <span class="font-medium">{{ __('Total Avg') }}:</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $stat['total_avg'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
