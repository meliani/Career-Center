<div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ __('Last updated') }}: {{ now()->format('H:i:s') }}
    </p>
    <div class="text-sm text-gray-600 dark:text-gray-400 animate-pulse">
        {{ __('Auto-refreshing every 30 seconds') }}
    </div>
</div>
