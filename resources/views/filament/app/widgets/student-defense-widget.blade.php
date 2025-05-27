<x-filament-widgets::widget>
    <x-filament::card class="relative overflow-hidden">
        <!-- Add subtle background pattern -->
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
        </div>

        <div class="space-y-6 relative">            <!-- Header Section with Icon -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-primary-100 rounded-lg">
                        @svg('heroicon-o-academic-cap', 'w-6 h-6 text-primary-500')
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ __('Your Defense Schedule') }}
                    </h2>
                </div>
            </div>

            @if($upcomingDefense)
                <!-- Defense Info Card -->
                <div class="bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:bg-gray-100 border-l-4 border-primary-500">
                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-medium text-gray-900">{{ $upcomingDefense->project->title ?? __('Your Defense') }}</h3>
                            <x-filament::badge color="primary">
                                {{ __('Scheduled') }}
                            </x-filament::badge>
                        </div>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">                            <div class="flex items-center space-x-2">
                                @svg('heroicon-o-calendar', 'w-5 h-5 text-primary-500')
                                <span class="text-sm font-medium">
                                    {{ $upcomingDefense->timeslot && $upcomingDefense->timeslot->start_time ? $upcomingDefense->timeslot->start_time->format('F j, Y') : __('Date TBD') }}
                                </span>
                            </div>
                              <div class="flex items-center space-x-2">
                                @svg('heroicon-o-clock', 'w-5 h-5 text-primary-500')
                                <span class="text-sm font-medium">
                                    @if($upcomingDefense->timeslot && $upcomingDefense->timeslot->start_time && $upcomingDefense->timeslot->end_time)
                                        {{ $upcomingDefense->timeslot->start_time->format('H:i') }} - 
                                        {{ $upcomingDefense->timeslot->end_time->format('H:i') }}
                                    @else
                                        {{ __('Time TBD') }}
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @svg('heroicon-o-map-pin', 'w-5 h-5 text-primary-500')
                                <span class="text-sm font-medium">
                                    {{ $upcomingDefense->room->name ?? __('Room TBD') }}
                                </span>
                            </div>
                            @if($upcomingDefense->project && $upcomingDefense->project->reviewers->count() > 0)
                            <div class="flex items-center space-x-2">
                                @svg('heroicon-o-user-group', 'w-5 h-5 text-primary-500')
                                <span class="text-sm font-medium">
                                    @foreach($upcomingDefense->project->reviewers as $reviewer)
                                        {{ $reviewer->full_name }}@if(!$loop->last), @endif
                                    @endforeach
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        @if($rescheduleRequest)
                            <!-- Reschedule Request Status -->
                            <div class="mt-4 p-3 rounded-lg 
                                @if($rescheduleRequest->status->value === 'pending')
                                    bg-yellow-50 border border-yellow-200
                                @elseif($rescheduleRequest->status->value === 'approved')
                                    bg-green-50 border border-green-200
                                @elseif($rescheduleRequest->status->value === 'rejected')
                                    bg-red-50 border border-red-200
                                @endif
                            ">                        <div class="flex items-start space-x-2">
                                    @if($rescheduleRequest->status->value === 'pending')
                                        @svg('heroicon-o-clock', 'w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5')
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">{{ __('Reschedule Request Pending') }}</p>
                                            <p class="text-xs text-yellow-600 mt-1">{{ __('Your request is being reviewed by the administration.') }}</p>
                                        </div>
                                    @elseif($rescheduleRequest->status->value === 'approved')
                                        @svg('heroicon-o-check-circle', 'w-5 h-5 text-green-500 flex-shrink-0 mt-0.5')
                                        <div>
                                            <p class="text-sm font-medium text-green-800">{{ __('Reschedule Request Approved') }}</p>
                                            <p class="text-xs text-green-600 mt-1">{{ __('Your defense will be rescheduled soon.') }}</p>
                                        </div>
                                    @elseif($rescheduleRequest->status->value === 'rejected')
                                        @svg('heroicon-o-x-circle', 'w-5 h-5 text-red-500 flex-shrink-0 mt-0.5')
                                        <div>
                                            <p class="text-sm font-medium text-red-800">{{ __('Reschedule Request Rejected') }}</p>
                                            <p class="text-xs text-red-600 mt-1">{{ __('Reason: ') }}{{ $rescheduleRequest->admin_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-3">
                                    <x-filament::button
                                        wire:click="viewRescheduleRequest"
                                        color="secondary"
                                        size="sm"
                                        class="transition-transform duration-200 hover:scale-105"
                                    >
                                        {{ __('View Request Details') }}
                                    </x-filament::button>
                                </div>
                            </div>
                        @endif
                        
                        @if($canRequestReschedule)
                            <div class="mt-3">
                                <x-filament::button
                                    wire:click="redirectToRescheduleForm"
                                    icon="heroicon-o-calendar"
                                    color="warning"
                                    size="sm"
                                    class="transition-transform duration-200 hover:scale-105"
                                >
                                    {{ __('Request Schedule Change') }}
                                </x-filament::button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- No Defense Scheduled Yet -->                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <div class="flex flex-col items-center space-y-3">
                        <div class="p-3 bg-gray-100 rounded-full">
                            @svg('heroicon-o-calendar', 'w-8 h-8 text-gray-400')
                        </div>
                        <h3 class="text-lg font-medium text-gray-700">{{ __('No Defense Scheduled Yet') }}</h3>
                        <p class="text-sm text-gray-500 max-w-md">
                            {{ __('Your defense hasn\'t been scheduled yet. You\'ll see the details here once it\'s ready.') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Defense Tips Section -->            <div class="mt-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                <h4 class="text-sm font-semibold text-blue-800">{{ __('Defense Preparation Tips') }}</h4>
                <ul class="mt-2 space-y-1 text-xs text-blue-700">
                    <li class="flex items-start space-x-2">
                        @svg('heroicon-o-check-circle', 'w-4 h-4 flex-shrink-0 mt-0.5')
                        <span>{{ __('Prepare your presentation at least one week in advance') }}</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        @svg('heroicon-o-check-circle', 'w-4 h-4 flex-shrink-0 mt-0.5')
                        <span>{{ __('Practice your presentation multiple times') }}</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        @svg('heroicon-o-check-circle', 'w-4 h-4 flex-shrink-0 mt-0.5')
                        <span>{{ __('Arrive at least 15 minutes before your scheduled time') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
