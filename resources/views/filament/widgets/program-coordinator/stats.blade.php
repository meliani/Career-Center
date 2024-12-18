<div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">
    @foreach ($stats as $stat)
        <button
            wire:click="filterByStat('{{ $stat['filter'] }}')"
            wire:loading.class="opacity-50"
            type="button"
            @class([
                'relative w-full transition-all duration-300 transform hover:-translate-y-1 focus:outline-none',
                'ring-2 ring-primary-500' => $activeFilter === $stat['filter'],
            ])
        >
            @if ($activeFilter === $stat['filter'])
                <div class="absolute -top-2 -right-2 h-5 w-5 bg-primary-500 rounded-full flex items-center justify-center">
                    <div wire:loading.remove>
                        <x-heroicon-s-funnel class="h-3 w-3 text-white" />
                    </div>
                    <div wire:loading>
                        <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            @endif
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg {{ match($stat['color']) {
                        'primary' => 'bg-primary-50 text-primary-500 dark:bg-primary-500/10 dark:text-primary-400',
                        'warning' => 'bg-warning-50 text-warning-500 dark:bg-warning-500/10 dark:text-warning-400',
                        'success' => 'bg-success-50 text-success-500 dark:bg-success-500/10 dark:text-success-400',
                    } }}">
                        <x-dynamic-component :component="$stat['icon']" class="w-6 h-6" />
                    </div>

                    <div class="space-y-1 text-left">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $stat['value'] }}
                        </p>
                    </div>
                </div>
            </div>
        </button>
    @endforeach
</div>
