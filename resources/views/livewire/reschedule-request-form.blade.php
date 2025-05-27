<div>
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <x-filament::icon
                icon="heroicon-o-calendar"
                class="w-5 h-5 text-gray-500"
            />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $timetable->timeslot->start_time->format('M d, Y - H:i') }} ({{ $timetable->room->name }})
            </span>
        </div>
        
        @if (!$showForm)
            <x-filament::button
                color="warning"
                size="sm"
                wire:click="toggleForm"
                icon="heroicon-m-calendar-days"
            >
                {{ __('Request Reschedule') }}
            </x-filament::button>
        @else
            <x-filament::button
                color="gray"
                size="sm"
                wire:click="toggleForm"
                icon="heroicon-m-x-mark"
            >
                {{ __('Cancel') }}
            </x-filament::button>
        @endif
    </div>
    
    @if ($showForm)
        <div class="mt-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                {{ __('Request Defense Reschedule') }}
            </h3>
            
            <form wire:submit="submit">
                {{ $this->form }}
                
                <div class="flex justify-end mt-4">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Submit Request') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    @endif
    
    @php
        $pendingRequest = \App\Models\RescheduleRequest::where('timetable_id', $timetable->id)
            ->where('student_id', auth()->user()->student->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->first();
    @endphp
    
    @if ($pendingRequest)
        <div class="mt-4 p-3 rounded-lg {{ $pendingRequest->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : ($pendingRequest->status === 'approved' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800') }}">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <x-filament::icon
                        :icon="$pendingRequest->status->getIcon()"
                        class="w-5 h-5 {{ $pendingRequest->status === 'pending' ? 'text-yellow-500' : ($pendingRequest->status === 'approved' ? 'text-green-500' : 'text-red-500') }}"
                    />
                    <span class="text-sm font-medium {{ $pendingRequest->status === 'pending' ? 'text-yellow-700 dark:text-yellow-300' : ($pendingRequest->status === 'approved' ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300') }}">
                        {{ __('Reschedule Request') }} - {{ $pendingRequest->status->getLabel() }}
                    </span>
                </div>
                <x-filament::badge :color="$pendingRequest->status->getColor()">
                    {{ $pendingRequest->status->getLabel() }}
                </x-filament::badge>
            </div>
            
            <div class="text-xs {{ $pendingRequest->status === 'pending' ? 'text-yellow-600 dark:text-yellow-400' : ($pendingRequest->status === 'approved' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') }}">
                {{ __('Requested') }}: {{ $pendingRequest->created_at->format('M d, Y H:i') }}
            </div>
            
            @if ($pendingRequest->preferred_date)
                <div class="mt-2 text-xs {{ $pendingRequest->status === 'pending' ? 'text-yellow-600 dark:text-yellow-400' : ($pendingRequest->status === 'approved' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400') }}">
                    {{ __('Preferred Date/Time') }}: {{ \Carbon\Carbon::parse($pendingRequest->preferred_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($pendingRequest->preferred_time)->format('H:i') }}
                </div>
            @endif
            
            @if ($pendingRequest->admin_notes && $pendingRequest->status !== 'pending')
                <div class="mt-2 p-2 rounded bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300">
                    <strong>{{ __('Admin Response') }}:</strong> {{ $pendingRequest->admin_notes }}
                </div>
            @endif
        </div>
    @endif
</div>
