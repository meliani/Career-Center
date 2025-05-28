<div class="bg-white border rounded-lg p-4">
    @if($request)
        <div class="space-y-4">
            <!-- Request Status Card -->                <div class="flex items-start space-x-3">
                    @if($request->status->value === 'pending')
                        <div class="p-2 rounded-full bg-yellow-100">
                            @svg('heroicon-o-clock', 'w-5 h-5 text-yellow-600')
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-yellow-800">{{ __('Request Status: Pending Review') }}</h3>
                            <p class="text-sm text-yellow-600 mt-1">
                                {{ __('Your request has been submitted and is waiting for review by the administration.') }}
                            </p>
                        </div>
                    @elseif($request->status->value === 'approved')
                        <div class="p-2 rounded-full bg-green-100">
                            @svg('heroicon-o-check-circle', 'w-5 h-5 text-green-600')
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-green-800">{{ __('Request Status: Approved') }}</h3>
                            <p class="text-sm text-green-600 mt-1">
                                {{ __('Your request has been approved. Your defense will be rescheduled soon.') }}
                            </p>
                        </div>
                    @elseif($request->status->value === 'rejected')
                        <div class="p-2 rounded-full bg-red-100">
                            @svg('heroicon-o-x-circle', 'w-5 h-5 text-red-600')
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-red-800">{{ __('Request Status: Rejected') }}</h3>
                            <p class="text-sm text-red-600 mt-1">
                                {{ __('Unfortunately, your request has been rejected.') }}
                            </p>
                        </div>
                    @endif
                </div>
              <!-- Request Details -->            <div class="grid grid-cols-1 gap-3 text-sm">
                <div class="flex items-center space-x-2">
                    @svg('heroicon-o-calendar', 'w-5 h-5 text-gray-500')
                    <span>                        <span class="font-medium text-gray-700">{{ __('Preferred Timeslot:') }}</span>
                        @if($request->preferredTimeslot)
                            {{ $request->preferredTimeslot->start_time->format('l, F j, Y') }} at 
                            {{ $request->preferredTimeslot->start_time->format('H:i') }} - 
                            {{ $request->preferredTimeslot->end_time->format('H:i') }}
                        @else
                            {{ __('Not specified') }}
                        @endif
                    </span>                </div>
                
                <div class="col-span-2">
                    <div class="flex items-start space-x-2">
                        @svg('heroicon-o-chat-bubble-bottom-center-text', 'w-5 h-5 text-gray-500 mt-0.5')
                        <div>
                            <span class="font-medium text-gray-700">{{ __('Reason:') }}</span>
                            <p class="mt-1 text-gray-600">{{ $request->reason }}</p>
                        </div>
                    </div>
                </div>
                
                @if($request->status->value === 'rejected' && $request->admin_notes)
                <div class="col-span-2">
                    <div class="flex items-start space-x-2">
                        @svg('heroicon-o-exclamation-circle', 'w-5 h-5 text-red-500 mt-0.5')
                        <div>
                            <span class="font-medium text-red-700">{{ __('Rejection Reason:') }}</span>
                            <p class="mt-1 text-red-600">{{ $request->admin_notes }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Request Metadata -->
            <div class="mt-2 text-xs text-gray-500">
                {{ __('Requested on') }} {{ $request->created_at->format('F j, Y') }}
                @if($request->processed_at)
                    <br>{{ __('Processed on') }} {{ $request->processed_at->format('F j, Y') }}
                    @if($request->processedBy)
                        {{ __('by') }} {{ $request->processedBy->name }}
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="text-center p-4">
            <p class="text-gray-500">{{ __('No reschedule request found.') }}</p>
        </div>
    @endif
</div>
