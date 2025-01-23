<div class="p-2 bg-white dark:bg-gray-800 rounded-xl shadow">
    <div class="flex items-center justify-between px-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ __('Department assignements statistics') }}
        </h3>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('By assigned department') }}
            </span>
            <button 
                wire:click="toggleStatsView"
                type="button"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $showAlternativeStats ? 'bg-primary-600' : 'bg-gray-200' }}"
                role="switch"
                aria-checked="{{ $showAlternativeStats ? 'true' : 'false' }}"
            >
                <span 
                    aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $showAlternativeStats ? 'translate-x-5' : 'translate-x-0' }}"
                ></span>
            </button>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('By assigned professor') }}
            </span>
        </div>
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
                        ({{ __('Ratio') }} : {{ round($stat['ratio'] ?? 0, 2) }})
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
