<div class="flex gap-2">
    @if($getRecord()->cv)
        <a href="{{ $getRecord()->cv }}" target="_blank" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg bg-success-500/10 text-success-600 px-2 py-1 text-sm">
            <x-heroicon-m-document class="w-4 h-4" />
            CV
        </a>
    @endif

    @if($getRecord()->lm)
        <a href="{{ $getRecord()->lm }}" target="_blank" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg bg-primary-500/10 text-primary-600 px-2 py-1 text-sm">
            <x-heroicon-m-document class="w-4 h-4" />
            CL
        </a>
    @endif

    @if($getRecord()->photo)
        <a href="{{ $getRecord()->photo }}" target="_blank" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg bg-info-500/10 text-info-600 px-2 py-1 text-sm">
            <x-heroicon-m-photo class="w-4 h-4" />
            Photo
        </a>
    @endif
</div>
