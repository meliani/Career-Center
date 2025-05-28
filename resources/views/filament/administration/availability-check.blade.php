@php
    $record = $record;
    $timeslot = $record->preferredTimeslot;
    $project = $record->timetable->project;
@endphp

<div class="space-y-6">
    @if($timeslot && $project)
        <!-- Requested Time Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-blue-900 mb-2">Requested Schedule</h3>
            <p class="text-blue-800">
                <strong>Date:</strong> {{ $timeslot->start_time->format('l, F j, Y') }}<br>
                <strong>Time:</strong> {{ $timeslot->start_time->format('H:i') }} - {{ $timeslot->end_time->format('H:i') }}<br>
                @if($record->preferredRoom)
                    <strong>Room:</strong> {{ $record->preferredRoom->name }}
                @endif
            </p>
        </div>

        <!-- Professor Availability -->
        <div class="bg-white border rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Professor Availability</h3>
            </div>
            <div class="p-4">
                @php
                    $isAvailable = App\Services\ProfessorService::checkJuryAvailability(
                        $timeslot,
                        $project,
                        $record->timetable_id
                    );
                @endphp
                
                @if($isAvailable)
                    <div class="flex items-center space-x-2 text-green-600 mb-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">All professors are available</span>
                    </div>
                @else
                    <div class="flex items-center space-x-2 text-red-600 mb-4">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Professor conflicts detected</span>
                    </div>
                @endif

                <!-- Individual Professor Status -->
                @if($project->jury && $project->jury->count() > 0)
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Individual Professor Status:</h4>
                        @foreach($project->jury as $professor)
                            @php
                                // Check individual professor availability
                                $professorConflicts = App\Models\Timetable::where('professor_id', $professor->id)
                                    ->whereHas('timeslot', function($q) use ($timeslot) {
                                        $q->where('start_time', '<', $timeslot->end_time)
                                          ->where('end_time', '>', $timeslot->start_time);
                                    })
                                    ->where('id', '!=', $record->timetable_id)
                                    ->with(['project', 'room', 'timeslot'])
                                    ->get();
                            @endphp
                            
                            <div class="flex items-start space-x-3 p-3 border rounded">
                                <div class="flex-shrink-0">
                                    @if($professorConflicts->isEmpty())
                                        <div class="w-3 h-3 bg-green-400 rounded-full mt-1"></div>
                                    @else
                                        <div class="w-3 h-3 bg-red-400 rounded-full mt-1"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $professor->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $professor->email }}</p>
                                    
                                    @if($professorConflicts->isNotEmpty())
                                        <div class="mt-2">
                                            <p class="text-xs text-red-600 font-medium">Conflicts:</p>
                                            @foreach($professorConflicts as $conflict)
                                                <p class="text-xs text-red-500 ml-2">
                                                    • {{ $conflict->timeslot->start_time->format('H:i') }}-{{ $conflict->timeslot->end_time->format('H:i') }}: 
                                                    {{ Str::limit($conflict->project->title ?? 'Unknown Project', 30) }}
                                                    @if($conflict->room)
                                                        ({{ $conflict->room->name }})
                                                    @endif
                                                </p>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-green-600 mt-1">Available</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No jury members assigned to this project yet.</p>
                @endif
            </div>
        </div>

        <!-- Room Availability -->
        @if($record->preferredRoom)
            <div class="bg-white border rounded-lg">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Room Availability</h3>
                </div>
                <div class="p-4">
                    @php
                        $roomConflicts = App\Models\Timetable::where('room_id', $record->preferredRoom->id)
                            ->whereHas('timeslot', function($q) use ($timeslot) {
                                $q->where('start_time', '<', $timeslot->end_time)
                                  ->where('end_time', '>', $timeslot->start_time);
                            })
                            ->where('id', '!=', $record->timetable_id)
                            ->with(['project', 'student', 'timeslot'])
                            ->get();
                    @endphp
                    
                    @if($roomConflicts->isEmpty())
                        <div class="flex items-center space-x-2 text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ $record->preferredRoom->name }} is available</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 text-red-600 mb-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ $record->preferredRoom->name }} has conflicts</span>
                        </div>
                        
                        <div class="space-y-2">
                            <h5 class="text-sm font-medium text-gray-900">Room conflicts:</h5>
                            @foreach($roomConflicts as $conflict)
                                <div class="text-sm text-red-600 ml-4">
                                    • {{ $conflict->timeslot->start_time->format('H:i') }}-{{ $conflict->timeslot->end_time->format('H:i') }}: 
                                    {{ $conflict->student->full_name ?? 'Unknown Student' }} - 
                                    {{ Str::limit($conflict->project->title ?? 'Unknown Project', 30) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Alternative Suggestions -->
        @if(!$isAvailable || ($record->preferredRoom && $roomConflicts->isNotEmpty()))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-lg font-medium text-yellow-900 mb-2">Recommendations</h3>
                <div class="text-yellow-800 text-sm space-y-1">
                    @if(!$isAvailable)
                        <p>• Consider suggesting alternative timeslots when the jury is available</p>
                        <p>• Contact professors to confirm their availability</p>
                    @endif
                    @if($record->preferredRoom && $roomConflicts->isNotEmpty())
                        <p>• Suggest alternative rooms for the requested timeslot</p>
                        <p>• Check room capacity requirements for the defense</p>
                    @endif
                    <p>• Consider partial approval with modifications</p>
                </div>
            </div>
        @endif

    @else
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <p class="text-gray-600">No timeslot or project information available for availability check.</p>
        </div>
    @endif
</div>
