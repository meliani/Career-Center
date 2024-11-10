<div>
    <div class="space-y-2">
        <div class="flex items-center space-x-2">
            <input type="text" readonly value="{{ $url }}" class="w-full px-3 py-2 border rounded-lg">
            <button
                onclick="navigator.clipboard.writeText('{{ $url }}').then(() => $dispatch('notify', {message: 'URL copied to clipboard!'}))"
                class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-500"
            >
                Copy
            </button>
        </div>
    </div>
</div>
