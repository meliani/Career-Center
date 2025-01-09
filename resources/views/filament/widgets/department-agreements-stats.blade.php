<div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow">
    <div class="flex items-center justify-center">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Department assignements statistics') }}
        </h3>
    </div>
    <div class="flex items-center justify-center gap-4 overflow-x-auto py-2 px-4">
        <div class="flex items-center gap-4 min-w-fit">
            @foreach ($stats as $stat)
                <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-900/50 px-3 py-2 rounded-lg">
                    <x-filament::badge :color="$stat['color']">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component :component="$stat['icon']" class="w-4 h-4" />
                            <span>{{ $stat['count'] }} {{ __('projects') }}</span>
                        </div>
                    </x-filament::badge>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        {{ $stat['name'] }}
                    </span>
                    <span class="text-xs text-gray-500" x-data x-tooltip.raw="{{ __('Rate of supervision = :projects projects / :professors professors', ['projects' => $stat['count'], 'professors' => $stat['professors_count']]) }}">
                        ({{ __('Ratio') }} : {{ round($stat['ratio'], 2) }})
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
