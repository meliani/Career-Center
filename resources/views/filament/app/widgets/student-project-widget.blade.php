<x-filament-widgets::widget>
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
        @php
            $project = $this->getProject();
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
                    @if($project->timetable)
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
                            'name' => $project->academic_supervisor,
                            'icon' => 'heroicon-o-academic-cap'
                        ],
                        'company' => [
                            'title' => __('Company Supervisor'),
                            'name' => $project->externalSupervisor->full_name,
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
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ $supervisor['title'] }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $supervisor['name'] }}
                                    </p>
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
                            <x-filament::button
                                type="button"
                                color="success"
                                icon="heroicon-m-user-plus"
                                wire:click="toggleCollaboratorForm"
                                class="transition-transform duration-300 hover:scale-105"
                            >
                                {{ __('Add Collaborator') }}
                            </x-filament::button>
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
                                            {{ __('Add Collaborator') }}
                                        </x-filament::button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Documents Section --}}
                @if($project->defense_status?->value === 'authorized')
                    <div class="border-t pt-4 space-y-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Available Documents') }}
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @if($project->evaluation_sheet_url)
                                <x-filament::button
                                    color="primary"
                                    icon="heroicon-m-document"
                                    class="w-full transition-transform duration-300 hover:scale-105"
                                    tag="a"
                                    href="{{ $project->evaluation_sheet_url }}"
                                    target="_blank"
                                >
                                    {{ __('View Evaluation Sheet') }}
                                </x-filament::button>
                            @endif
                        </div>
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
                @elseif($this->hasCollaborationRequest() && !$this->hasActiveCollaboration())
                    <div class="border-t pt-4">
                        <div class="bg-primary-50 dark:bg-primary-900/50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <x-filament::icon
                                    icon="heroicon-m-information-circle"
                                    class="w-5 h-5 text-primary-500"
                                />
                                <p class="text-sm text-primary-700 dark:text-primary-300">
                                    {{ __('You have a pending or active collaboration request.') }}
                                </p>
                            </div>
                        </div>
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
                @endif

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

                {{-- Collaboration Section --}}
                @if(!$project && !$this->hasCollaborationRequest())
                    <div class="max-w-sm mx-auto">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white dark:bg-gray-800 px-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Looking for a teammate?') }}
                                </span>
                            </div>
                        </div>

                        @if($this->hasExistingProject())
                            <div class="mt-6 flex flex-col items-center space-y-4">
                                <form wire:submit="sendCollaborationRequest" class="w-full space-y-4">
                                    {{ $this->form }}

                                    <x-filament::button
                                        type="submit"
                                        color="primary"
                                        icon="heroicon-m-user-plus"
                                        class="w-full"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ __('Send Collaboration Request') }}
                                    </x-filament::button>
                                </form>
                            </div>
                        @else
                            <div class="mt-6">
                                <div class="bg-warning-50 dark:bg-warning-900/50 rounded-xl p-4">
                                    <div class="flex items-center space-x-3">
                                        <x-filament::icon
                                            icon="heroicon-m-exclamation-triangle"
                                            class="w-5 h-5 text-warning-500 shrink-0"
                                        />
                                        <p class="text-sm text-warning-700 dark:text-warning-300">
                                            {{ __('You need to have an existing project or internship agreement before you can send collaboration requests.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Pending/Active Request Status --}}
                @if($this->hasCollaborationRequest())
                    <div class="max-w-sm mx-auto">
                        <div class="bg-primary-50 dark:bg-primary-900/50 rounded-xl p-4">
                            <div class="flex items-center space-x-3">
                                <x-filament::icon
                                    icon="heroicon-m-information-circle"
                                    class="w-5 h-5 text-primary-500 shrink-0"
                                />
                                <p class="text-sm text-primary-700 dark:text-primary-300">
                                    {{ __('You have a pending or active collaboration request.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
