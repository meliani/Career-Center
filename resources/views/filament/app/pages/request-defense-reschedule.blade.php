<x-filament::page>
    <div class="space-y-6">
        @if($existingRequest && $existingRequest->status !== \App\Enums\RescheduleRequestStatus::Rejected)
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    @svg('heroicon-o-information-circle', 'w-6 h-6 text-primary-500')
                    <p class="text-sm text-primary-700">
                        {{ __('You have an active reschedule request. You can view its status below.') }}
                    </p>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    @svg('heroicon-o-information-circle', 'w-6 h-6 text-blue-500')
                    <p class="text-sm text-blue-700">
                        {{ __('Please provide a valid reason for rescheduling your defense and select an available timeslot. Only timeslots where all your professors are available will be shown.') }}
                    </p>
                </div>
            </div>
        @endif

        {{ $this->form }}
        
        @if (!$existingRequest || $existingRequest->status === \App\Enums\RescheduleRequestStatus::Rejected)
            <div class="flex justify-end space-x-3">
                <x-filament::button 
                    color="secondary"
                    tag="a"
                    href="{{ route('filament.app.pages.welcome-dashboard') }}"
                >
                    {{ __('Cancel') }}
                </x-filament::button>
                
                <x-filament::button 
                    color="primary"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    type="button"
                >
                    <span wire:loading.remove>{{ __('Submit Request') }}</span>
                    <span wire:loading>{{ __('Submitting...') }}</span>
                </x-filament::button>
            </div>
        @else
            <div class="flex justify-end space-x-3">
                <x-filament::button 
                    color="secondary"
                    tag="a"
                    href="{{ route('filament.app.pages.welcome-dashboard') }}"
                >
                    {{ __('Back to Dashboard') }}
                </x-filament::button>
            </div>
        @endif
    </div>
</x-filament::page>
