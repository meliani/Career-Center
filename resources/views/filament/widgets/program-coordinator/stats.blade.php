<div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">
    @foreach ($stats as $stat)
        <div class="relative group">
            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg {{ match($stat['color']) {
                        'primary' => 'bg-primary-50 text-primary-500 dark:bg-primary-500/10 dark:text-primary-400',
                        'warning' => 'bg-warning-50 text-warning-500 dark:bg-warning-500/10 dark:text-warning-400',
                        'success' => 'bg-success-50 text-success-500 dark:bg-success-500/10 dark:text-success-400',
                    } }}">
                        <x-dynamic-component :component="$stat['icon']" class="w-6 h-6" />
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white animate-pulse">
                            {{ $stat['value'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
