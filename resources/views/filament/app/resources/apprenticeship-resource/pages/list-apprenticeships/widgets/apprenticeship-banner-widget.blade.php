<div class="relative bg-gradient-to-r from-primary-500 to-primary-700 p-6 rounded-lg shadow-md text-white mb-6">
    <div class="max-w-4xl">
        <h2 class="text-2xl font-bold mb-2">{{ __('Apprenticeship Agreements') }} - {{ $this->getAcademicYear() }}</h2>
        
        @if ($this->hasExistingAgreement())
            <p class="text-white/90 mb-3">{{ __('Your apprenticeship agreement has been created for this academic year. You can monitor its progress and access your documentation below.') }}</p>
        @else
            <p class="text-white/90 mb-3">{{ __('Create your apprenticeship agreement using our step-by-step wizard. You can save a draft and complete it later if needed.') }}</p>
        @endif
        
        <div class="text-sm bg-white/20 p-3 rounded-md">
            <div class="flex items-center mb-1">
                <x-heroicon-o-light-bulb class="w-5 h-5 mr-2" />
                <span class="font-medium">{{ __('Important Tips:') }}</span>
            </div>
            <ul class="list-disc list-inside pl-3 space-y-1">
                <li>{{ __('Make sure all organization and contact information is accurate') }}</li>
                <li>{{ __('Clearly describe your tasks and responsibilities') }}</li>
                <li>{{ __('Save your agreement as a draft until you are ready to submit') }}</li>
                <li>{{ __('Once submitted, you will need to wait for approval before generating the final PDF') }}</li>
            </ul>
        </div>
    </div>
    
    <div class="absolute top-0 right-0 h-full w-1/3 overflow-hidden rounded-r-lg opacity-20 pointer-events-none">
        <div class="transform translate-x-12 translate-y-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-64 h-64">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>
    </div>
</div>
