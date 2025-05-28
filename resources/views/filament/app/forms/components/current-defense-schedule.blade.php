<div class="bg-white border rounded-lg p-4">
    @if($timetable)
        <div class="space-y-4">
            <div class="flex justify-between items-start">
                <h3 class="text-base font-medium text-gray-900">{{ $timetable->project->title ?? __('Your Defense') }}</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">                <div class="flex items-center space-x-2">
                    @svg('heroicon-o-calendar', 'w-5 h-5 text-gray-500')
                    <span>
                        <span class="font-medium text-gray-700">{{ __('Date:') }}</span>
                        {{ $timetable->timeslot && $timetable->timeslot->start_time ? $timetable->timeslot->start_time->format('F j, Y') : __('Date TBD') }}
                    </span>
                </div>
                  <div class="flex items-center space-x-2">
                    @svg('heroicon-o-clock', 'w-5 h-5 text-gray-500')
                    <span>
                        <span class="font-medium text-gray-700">{{ __('Time:') }}</span>
                        @if($timetable->timeslot && $timetable->timeslot->start_time && $timetable->timeslot->end_time)
                            {{ $timetable->timeslot->start_time->format('H:i') }} - 
                            {{ $timetable->timeslot->end_time->format('H:i') }}
                        @else
                            {{ __('Time TBD') }}
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center space-x-2">
                    @svg('heroicon-o-map-pin', 'w-5 h-5 text-gray-500')
                    <span>
                        <span class="font-medium text-gray-700">{{ __('Location:') }}</span>
                        {{ $timetable->room->name ?? __('Room TBD') }}
                    </span>
                </div>
                @if($timetable->project && $timetable->project->reviewers->count() > 0)
                <div class="flex items-center space-x-2">
                    @svg('heroicon-o-user-group', 'w-5 h-5 text-gray-500')
                    <span>
                        <span class="font-medium text-gray-700">{{ __('Reviewers:') }}</span>
                        @foreach($timetable->project->reviewers as $reviewer)
                            {{ $reviewer->full_name }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
                @endif
            </div>
              <div class="mt-2 text-xs text-gray-500">
                @if($timetable->created_at)
                    {{ __('Scheduled on') }} {{ $timetable->created_at->format('F j, Y') }}
                    @if($timetable->scheduledBy)
                        {{ __('by') }} {{ $timetable->scheduledBy->name }}
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="text-center p-4">
            <p class="text-gray-500">{{ __('No defense scheduled yet.') }}</p>
        </div>
    @endif
</div>
