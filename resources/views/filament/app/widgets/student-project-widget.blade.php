<x-filament-widgets::widget>
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
        @php
            $project = $this->getProject();
            $midTermReport = $this->getMidTermReport();
        @endphp

        @if($project)
            {{-- Project Header Banner --}}
            <div class="relative h-32 bg-gradient-to-r from-primary-600 to-primary-400 rounded-t-xl">
                <div class="absolute inset-0 bg-black/20 rounded-t-xl"></div>
                <div class="absolute bottom-4 left-4 flex items-center space-x-4">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="w-6 h-6 text-primary-500"
                        />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            {{ __('My Project') }}
                        </h2>
                        <x-filament::badge
                            size="lg"
                            :color="$project->defense_status?->getColor()"
                            icon="{{ $project->defense_status?->getIcon() }}"
                            class="mt-1"
                        >
                         {{ __('Defense'). ' '. $project->defense_status?->getLabel() }}
                        </x-filament::badge>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-8">
                {{-- Project Information --}}
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Title & Description --}}
                    <div class="md:col-span-2">
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-900 transition-all duration-300">
                            <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                {{ $project->title }}
                            </h3>
                            @if($project->description)
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($project->description, 200) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Organization Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary-50 dark:bg-primary-900/50 rounded-lg">
                                <x-filament::icon
                                    icon="heroicon-o-building-office"
                                    class="w-5 h-5 text-primary-500"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ __('Organization') }}
                                </p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $project->organization_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Defense Information Card --}}
                    @if($project->timetable && app(\App\Settings\DisplaySettings::class)->display_plannings)
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('Defense Schedule') }}
                                    </h3>
                                    <x-filament::badge color="primary">
                                        {{ $project->timetable->room->name }}
                                    </x-filament::badge>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center space-x-2">
                                        <x-filament::icon
                                            icon="heroicon-m-calendar"
                                            class="w-5 h-5 text-gray-400"
                                        />
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $project->timetable->timeslot->start_time?->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <x-filament::icon
                                            icon="heroicon-m-clock"
                                            class="w-5 h-5 text-gray-400"
                                        />
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $project->timetable->timeslot->start_time?->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Supervisors Grid --}}
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        'academic' => [
                            'title' => __('Academic Supervisor'),
                            'name' => $project->academic_supervisor()->name ?? $project->academic_supervisor_name,
                            'email' => $project->academic_supervisor()?->email,
                            'icon' => 'heroicon-o-academic-cap'
                        ],
                        'company' => [
                            'title' => __('Company Supervisor'),
                            'name' => $project->externalSupervisor->full_name,
                            'email' => $project->externalSupervisor->email,
                            'icon' => 'heroicon-o-building-office-2'
                        ]
                    ] as $type => $supervisor)
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <x-filament::icon
                                        :icon="$supervisor['icon']"
                                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    />
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ $supervisor['title'] }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $supervisor['name'] }}
                                    </p>
                                    @if(isset($supervisor['email']) && $supervisor['email'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-1 truncate">
                                            <x-filament::icon
                                                icon="heroicon-o-envelope"
                                                class="w-3 h-3 mr-1 flex-shrink-0 text-gray-400"
                                            />
                                            <a href="mailto:{{ $supervisor['email'] }}" class="hover:text-primary-500 transition-colors truncate" title="{{ $supervisor['email'] }}">
                                                {{ $supervisor['email'] }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add after project details but before documents section --}}
                @if($project->canAddCollaborator() && !$this->hasActiveCollaboration())
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('Project Collaboration') }}
                            </h3>
                            @if(!$showCollaboratorForm && !$this->hasCollaborationRequest())

                            <x-filament::button
                                type="button"
                                color="success"
                                icon="heroicon-m-user-plus"
                                wire:click="toggleCollaboratorForm"
                                class="transition-transform duration-300 hover:scale-105"
                            >
                                {{ __('Add Collaborator') }}
                            </x-filament::button>
                            @endif
                        </div>

                        @if($showCollaboratorForm)
                            <div class="mt-4">
                                <form wire:submit="addCollaborator">
                                    {{ $this->collaboratorForm }}

                                    <div class="flex justify-end space-x-2 mt-4">
                                        <x-filament::button
                                            type="submit"
                                            color="success"
                                            icon="heroicon-m-user-plus"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ __('Send Collaboration Request') }}
                                        </x-filament::button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Collaboration Section --}}
                @if(!$project && !$this->hasCollaborationRequest())
                    <div class="border-t pt-4 space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('Project Collaboration') }}
                            </h3>
                        </div>

                        <form wire:submit="sendCollaborationRequest">
                            {{ $this->form }}

                            <x-filament::button
                                type="submit"
                                color="primary"
                                icon="heroicon-m-user-plus"
                                class="w-full transition-transform duration-300 hover:scale-105"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Send Collaboration Request') }}
                            </x-filament::button>
                        </form>
                    </div>
                @endif

                {{-- Collaboration Status Section --}}
                @if($collaborationRequest = $this->getCollaborationRequest())
                    <div class="border-t pt-4">
                        <div class="bg-primary-50 dark:bg-primary-900/50 rounded-lg p-4 space-y-4">
                            {{-- Show different content based on the user's role in the collaboration --}}
                            @if($collaborationRequest->sender_id === auth()->id())
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <x-filament::icon
                                            icon="heroicon-m-clock"
                                            class="w-5 h-5 text-primary-500"
                                        />
                                        <span class="text-sm text-primary-700 dark:text-primary-300">
                                            {{ __('Collaboration request sent to') }}
                                            <strong>{{ $collaborationRequest->receiver->name }}</strong>
                                        </span>
                                    </div>
                                    <x-filament::badge :color="$collaborationRequest->status->getColor()">
                                        {{ $collaborationRequest->status->getLabel() }}
                                    </x-filament::badge>
                                </div>
                                @if($collaborationRequest->status->value === 'pending')
                                    <x-filament::button
                                        wire:click="cancelCollaborationRequest({{ $collaborationRequest->id }})"
                                        color="danger"
                                        icon="heroicon-m-x-mark"
                                        class="w-full"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ __('Cancel Request') }}
                                    </x-filament::button>
                                @endif
                            @else
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <x-filament::icon
                                            icon="heroicon-m-user"
                                            class="w-5 h-5 text-primary-500"
                                        />
                                        <span class="text-sm text-primary-700 dark:text-primary-300">
                                            {{ __('Collaboration request from') }}
                                            <strong>{{ $collaborationRequest->sender->name }}</strong>
                                        </span>
                                    </div>
                                    <x-filament::badge :color="$collaborationRequest->status->getColor()">
                                        {{ $collaborationRequest->status->getLabel() }}
                                    </x-filament::badge>
                                </div>

                                @if($collaborationRequest->status->value === 'pending')
                                    <div class="flex space-x-2">
                                        <x-filament::button
                                            wire:click="acceptCollaborationRequest({{ $collaborationRequest->id }})"
                                            color="success"
                                            icon="heroicon-m-check"
                                            class="flex-1"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ __('Accept') }}
                                        </x-filament::button>
                                        <x-filament::button
                                            wire:click="rejectCollaborationRequest({{ $collaborationRequest->id }})"
                                            color="danger"
                                            icon="heroicon-m-x-mark"
                                            class="flex-1"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ __('Reject') }}
                                        </x-filament::button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/50 rounded-lg p-4 space-y-4">
                        <div class="flex items-center space-x-3">
                        <x-filament::icon
                            icon="heroicon-m-information-circle"
                            class="w-5 h-5 text-primary-500"
                        />
                        <span class="text-sm text-primary-700 dark:text-primary-300">
                            {{ __('Notice : To cancel an accepted collaboration, you need to contact the administration.') }}
                        </span>
                        </div>
                    </div>
                @endif

                {{-- Mid-Term Report Section --}}
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Mid-Term Report') }}
                        </h3>
                        <x-filament::badge color="warning" class="ml-2">
                            {{ __('Test Feature') }}
                        </x-filament::badge>
                    </div>
                    
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mb-3">
                        {{ __('This is a test feature and may change in the future.') }}
                    </p>

                    @if($midTermReport)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('You submitted your mid-term report on:') }}
                                <strong>{{ $midTermReport->submitted_at->format('M d, Y H:i') }}</strong>
                            </p>
                            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                                    {{ $midTermReport->content }}
                                </p>
                            </div>
                            @if($midTermReport->is_read_by_supervisor)
                                <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                                    {{ __('Your supervisor has read the report.') }}
                                </p>
                            @else
                                <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                                    {{ __('Your supervisor has not read the report yet.') }}
                                </p>
                            @endif
                        </div>
                    @else
                        <form wire:submit="submitMidTermReport" class="mt-4">
                            {{ $this->form }}

                            <x-filament::button
                                type="submit"
                                color="primary"
                                class="mt-4"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Submit Report') }}
                            </x-filament::button>
                        </form>
                    @endif
                </div>

                {{-- View Details Link --}}
                <div class="border-t pt-4">
                    <x-filament::button
                        color="gray"
                        icon="heroicon-m-arrow-top-right-on-square"
                        class="w-full transition-transform duration-300 hover:scale-105"
                        tag="a"
                        :href="route('filament.app.resources.projects.view', ['record' => $project])"
                    >
                        {{ __('View Full Details') }}
                    </x-filament::button>
                </div>
            </div>
        @else
            {{-- Empty State with Improved Design --}}
            <div class="px-6 py-12 space-y-6">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-50 dark:bg-primary-950 mb-4">
                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="w-10 h-10 text-primary-500"
                        />
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('No Project Yet') }}
                    </h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        {{ __('You haven\'t been assigned to any project for this academic year.') }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
