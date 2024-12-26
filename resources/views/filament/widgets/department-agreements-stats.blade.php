<div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow">
    <div class="flex items-center justify-center gap-4 overflow-x-auto py-2 px-4">
        <div class="flex items-center gap-4 min-w-fit">
            @foreach ($stats as $stat)
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded-lg">
                    <div @class([
                        'flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-semibold tracking-tight',
                        'bg-info-100 text-info-700 dark:bg-info-500/10 dark:text-info-400' => $stat['color'] === 'info',
                        'bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $stat['color'] === 'success',
                        'bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' => $stat['color'] === 'warning',
                        'bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' => $stat['color'] === 'danger',
                    ])>
                        <x-dynamic-component :component="$stat['icon']" class="w-4 h-4" />
                        <span>{{ $stat['count'] }} {{ __('projects') }}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $stat['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
