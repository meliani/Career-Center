<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $project = $this->getProject();
        @endphp

        @if($project)
            <div class="transition-all duration-300 hover:shadow-lg rounded-xl">
                <div class="space-y-6">
                    {{-- Header with Status --}}
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-primary-100 rounded-lg">
                                <x-filament::icon
                                    icon="heroicon-o-document-text"
                                    class="w-6 h-6 text-primary-500"
                                />
                            </div>
                            <h2 class="text-xl font-bold tracking-tight">
                                {{ __('My Project') }}
                            </h2>
                        </div>
                        <div>
                            <x-filament::badge
                                :color="$project->defense_status?->getColor()"
                                icon="{{ $project->defense_status?->getIcon() }}"
                                class="animate-pulse"
                            >
                                {{ $project->defense_status?->getLabel() }}
                            </x-filament::badge>
                        </div>
                    </div>

                    {{-- Project Details --}}
                    <div class="space-y-6">
                        <div class="group transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-900 p-3 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors duration-300">
                                {{ __('Project Title') }}
                            </h3>
                            <div class="mt-1 text-lg font-semibold">
                                {{ $project->title }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="group transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-900 p-3 rounded-lg">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors duration-300">
                                    {{ __('Organization') }}
                                </h3>
                                <div class="mt-1">
                                    <x-filament::badge size="lg" class="group-hover:scale-105 transition-transform duration-300">
                                        {{ $project->organization_name }}
                                    </x-filament::badge>
                                </div>
                            </div>

                            @if($project->timetable)
                                <div class="group transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-900 p-3 rounded-lg">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors duration-300">
                                        {{ __('Defense Information') }}
                                    </h3>
                                    <div class="mt-1 space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <x-filament::icon
                                                icon="heroicon-m-calendar"
                                                class="w-4 h-4 text-gray-400 group-hover:text-primary-500"
                                            />
                                            <span>{{ $project->timetable->timeslot->start_time?->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <x-filament::icon
                                                icon="heroicon-m-clock"
                                                class="w-4 h-4 text-gray-400 group-hover:text-primary-500"
                                            />
                                            <span>{{ $project->timetable->timeslot->start_time?->format('H:i') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <x-filament::icon
                                                icon="heroicon-m-building-office"
                                                class="w-4 h-4 text-gray-400 group-hover:text-primary-500"
                                            />
                                            <span>{{ $project->timetable->room->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Supervisors Section --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="group transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-900 p-3 rounded-lg">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors duration-300">
                                    {{ __('Academic Supervisor') }}
                                </h3>
                                <div class="mt-1 flex items-center space-x-2">
                                    <x-filament::icon
                                        icon="heroicon-m-academic-cap"
                                        class="w-4 h-4 text-gray-400 group-hover:text-primary-500"
                                    />
                                    <span>{{ $project->academic_supervisor }}</span>
                                </div>
                            </div>

                            <div class="group transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-900 p-3 rounded-lg">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-500 transition-colors duration-300">
                                    {{ __('Company Supervisor') }}
                                </h3>
                                <div class="mt-1 flex items-center space-x-2">
                                    <x-filament::icon
                                        icon="heroicon-m-user"
                                        class="w-4 h-4 text-gray-400 group-hover:text-primary-500"
                                    />
                                    <span>{{ $project->externalSupervisor->full_name }}</span>
                                </div>
                            </div>
                        </div>

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
                </div>
            </div>
        @else
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <x-filament::icon
                        icon="heroicon-o-document-text"
                        class="w-8 h-8 text-gray-400"
                    />
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg">
                    {{ __('No project found for this academic year.') }}
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
