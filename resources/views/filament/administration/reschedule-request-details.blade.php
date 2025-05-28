@php
    $record = $record;
@endphp

<div class="space-y-6">
    <!-- Student Information -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Student Information</h3>            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Student Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->student ? $record->student->first_name . ' ' . $record->student->last_name : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->student->email ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->student->phone ?? 'N/A' }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Information -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Project Information</h3>
            <div class="mt-5">
                <dt class="text-sm font-medium text-gray-500">Project Title</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $record->timetable->project->title ?? 'N/A' }}</dd>
                
                @if($record->timetable->project->description)
                    <dt class="text-sm font-medium text-gray-500 mt-3">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ Str::limit($record->timetable->project->description, 200) }}</dd>
                @endif
                
                @if($record->timetable->project->supervisor)
                    <dt class="text-sm font-medium text-gray-500 mt-3">Supervisor</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->timetable->project->supervisor->full_name ?? 'N/A' }}</dd>
                @endif
            </div>
        </div>
    </div>

    <!-- Current Defense Schedule -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Current Defense Schedule</h3>
            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($record->timetable->timeslot)
                            {{ $record->timetable->timeslot->start_time->format('l, F j, Y') }}<br>
                            {{ $record->timetable->timeslot->start_time->format('H:i') }} - {{ $record->timetable->timeslot->end_time->format('H:i') }}
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Room</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->timetable->room->name ?? 'TBD' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Days Until Defense</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($record->timetable->timeslot)
                            @php
                                $days = $record->timetable->timeslot->start_time->diffInDays(now(), false);
                                $class = $days <= 3 ? 'text-red-600' : ($days <= 7 ? 'text-yellow-600' : 'text-green-600');
                            @endphp
                            <span class="{{ $class }} font-medium">{{ $days }} days</span>
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Requested New Schedule -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Requested New Schedule</h3>
            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested Date & Time</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($record->preferredTimeslot)
                            {{ $record->preferredTimeslot->start_time->format('l, F j, Y') }}<br>
                            {{ $record->preferredTimeslot->start_time->format('H:i') }} - {{ $record->preferredTimeslot->end_time->format('H:i') }}
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Requested Room</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->preferredRoom->name ?? 'Any available room' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Availability Status</dt>
                    <dd class="mt-1 text-sm">
                        @if($record->status->value === 'pending' && $record->preferredTimeslot)
                            @php
                                $available = App\Services\ProfessorService::checkJuryAvailability(
                                    $record->preferredTimeslot,
                                    $record->timetable->project,
                                    $record->timetable_id
                                );
                            @endphp
                            @if($available)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Available
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ✗ Conflict
                                </span>
                            @endif
                        @else
                            <span class="text-gray-500">N/A</span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Request Details</h3>
            <div class="mt-5">
                <dt class="text-sm font-medium text-gray-500">Student Reason</dt>
                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $record->reason ?? 'No reason provided' }}</dd>
                
                <dt class="text-sm font-medium text-gray-500 mt-4">Status</dt>
                <dd class="mt-1">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$record->status->value] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($record->status->value) }}
                    </span>
                </dd>
                
                <dt class="text-sm font-medium text-gray-500 mt-4">Submitted On</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $record->created_at->format('F j, Y \a\t H:i') }}</dd>
                
                @if($record->processed_at)
                    <dt class="text-sm font-medium text-gray-500 mt-4">Processed On</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->processed_at->format('F j, Y \a\t H:i') }}</dd>
                @endif
                
                @if($record->processor)
                    <dt class="text-sm font-medium text-gray-500 mt-4">Processed By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $record->processor->name }}</dd>
                @endif
                
                @if($record->admin_notes)
                    <dt class="text-sm font-medium text-gray-500 mt-4">Admin Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $record->admin_notes }}</dd>
                @endif
            </div>
        </div>
    </div>

    <!-- Jury Information -->
    @if($record->timetable->project && $record->timetable->project->jury)
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Jury Members</h3>
                <div class="mt-5">
                    @foreach($record->timetable->project->jury as $member)
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="text-sm text-gray-900">{{ $member->full_name }}</span>
                            <span class="text-xs text-gray-500">({{ $member->role ?? 'Member' }})</span>
                            @if($record->status->value === 'pending' && $record->preferredTimeslot)
                                @php
                                    // You could add individual professor availability check here
                                @endphp
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
