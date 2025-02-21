<div
    class="space-y-6"
    wire:poll.{{ $this->pooling }}
    x-data="{
        showHelp: false,
        showSearchBox: @entangle('showSearch'),
        search: @entangle('search'),
        focused: false,
        tooltips: {
            supervisor: @js(__('Select a supervisor for this project')),
            firstReviewer: @js(__('First reviewer can be assigned after selecting a supervisor')),
            secondReviewer: @js(__('Second reviewer can be assigned after selecting first reviewer')),
            status: {
                pending: @js(__('No assignments yet')),
                supervisor: @js(__('Supervisor assigned, waiting for reviewers')),
                firstReviewer: @js(__('First reviewer assigned, needs second reviewer')),
                complete: @js(__('All assignments complete'))
            },
            filters: {
                all: @js(__('Show all projects regardless of their assignment status')),
                pendingSupervisor: @js(__('Projects that do not have a supervisor assigned yet')),
                pendingReviewers: @js(__('Projects that have a supervisor but are missing one or both reviewers')),
                assigned: @js(__('Projects with all roles assigned (supervisor and both reviewers)'))
            }
        }
    }"
>
    {{-- Help and Search Buttons --}}
    <div class="flex justify-between items-center">
        <div class="relative w-full max-w-sm h-10">
            {{-- Search Button --}}
            <button
                x-show="!showSearchBox"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="showSearchBox = true; $nextTick(() => $refs.searchInput.focus())"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                {{ __('Search') }}
            </button>

            {{-- Search Input --}}
            <div
                x-show="showSearchBox"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="w-full"
            >
                <div class="relative">
                    <input
                        x-ref="searchInput"
                        type="text"
                        wire:model.live.debounce.100ms="search"  {{-- Changed to wire:model.live with shorter debounce --}}
                        placeholder="{{ __('Search by ID_PFE, student names or organization...') }}"
                        class="w-full h-10 pl-10 pr-10 text-sm border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                        style="padding-left: 1rem;"
                        @keydown.escape="showSearchBox = false; search = ''"
                        @blur="!focused && !search && (showSearchBox = false)"
                    />
                    @if($search)
                        <button 
                            @click="search = ''; showSearchBox = false"
                            @mouseenter="focused = true"
                            @mouseleave="focused = false"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        >
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Help button --}}
        <button
            @click="showHelp = !showHelp"
            class="ml-2 inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            <x-heroicon-o-question-mark-circle class="w-5 h-5 mr-2" />
            {{ __('How it works') }}
        </button>
    </div>

    {{-- Remove the old search panel since we've integrated it into the header --}}

    {{-- Help Panel --}}
    <div
        x-show="showHelp"
        x-transition
        class="bg-blue-50 dark:bg-blue-900/50 p-4 rounded-lg mb-4"
    >
        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">{{ __('Assignment Process') }}</h3>
        <ul class="list-disc list-inside space-y-2 text-blue-800 dark:text-blue-200">
            <li>{{ __('First, assign a supervisor to the project') }}</li>
            <li>{{ __('Once a supervisor is assigned, you can select a first reviewer') }}</li>
            <li>{{ __('After selecting the first reviewer, you can assign a second reviewer') }}</li>
            <li>{{ __('The status icon will update as you complete each step') }}</li>
            <li>{{ __('You can download all your projects details in the projects section') }}</li>
        </ul>
    </div>

    {{-- Stats Section with tooltips --}}
    <div class="px-4 sm:px-6 lg:px-8 mb-2">
        <div class="flex items-center justify-between gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4" />
                <span class="font-medium">{{ __('Filter by status') }} :</span>
            </div>
            @if($activeFilter !== 'all')
                <button 
                    wire:click="setFilter('all')"
                    class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center gap-1"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    {{ __('Clear filter') }}
                </button>
            @endif
        </div>
        <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory" wire:loading.class="opacity-50">
            {{-- Total Projects Card --}}
            <div class="snap-start relative flex-1 min-w-[200px] max-w-[240px]" x-tooltip="tooltips.filters.all">
                <div wire:click="setFilter('all')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg border-2 transition-all duration-200"
                     :class="{ 
                        'border-primary-500 shadow-lg shadow-primary-100 dark:shadow-none': '{{ $activeFilter }}' === 'all',
                        'border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800': '{{ $activeFilter }}' !== 'all'
                     }">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary-100 dark:bg-primary-900/50 rounded-full p-2">
                            <x-heroicon-o-academic-cap class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                {{ __('Total Projects') }}
                                <x-heroicon-o-adjustments-horizontal class="w-3 h-3" />
                            </p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Supervisor Card --}}
            <div class="snap-start relative flex-1 min-w-[200px] max-w-[240px]" x-tooltip="tooltips.filters.pendingSupervisor">
                <div wire:click="setFilter('pendingSupervisor')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg border-2 transition-all duration-200"
                     :class="{
                        'border-danger-500 shadow-lg shadow-danger-100 dark:shadow-none': '{{ $activeFilter }}' === 'pendingSupervisor',
                        'border-gray-200 dark:border-gray-700 hover:border-danger-200 dark:hover:border-danger-800': '{{ $activeFilter }}' !== 'pendingSupervisor'
                     }">
                    <div class="flex items-center gap-3">
                        <div class="bg-danger-100 dark:bg-danger-900/50 rounded-full p-2">
                            <x-heroicon-o-user-minus class="w-5 h-5 text-danger-600 dark:text-danger-400" />
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                {{ __('Pending Supervisor') }}
                                <x-heroicon-o-adjustments-horizontal class="w-3 h-3" />
                            </p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->stats['pendingSupervisor'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending Reviewers Card --}}
            <div class="snap-start relative flex-1 min-w-[200px] max-w-[240px]" x-tooltip="tooltips.filters.pendingReviewers">
                <div wire:click="setFilter('pendingReviewers')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg border-2 transition-all duration-200"
                     :class="{ 
                        'border-warning-500 shadow-lg shadow-warning-100 dark:shadow-none': '{{ $activeFilter }}' === 'pendingReviewers',
                        'border-gray-200 dark:border-gray-700 hover:border-warning-200 dark:hover:border-warning-800': '{{ $activeFilter }}' !== 'pendingReviewers'
                     }">
                    <div class="flex items-center gap-3">
                        <div class="bg-warning-100 dark:bg-warning-900/50 rounded-full p-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                {{ __('Pending Reviewers') }}
                                <x-heroicon-o-adjustments-horizontal class="w-3 h-3" />
                            </p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->stats['pendingReviewers'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fully Assigned Card --}}
            <div class="snap-start relative flex-1 min-w-[200px] max-w-[240px]" x-tooltip="tooltips.filters.assigned">
                <div wire:click="setFilter('assigned')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg border-2 transition-all duration-200"
                     :class="{ 
                        'border-success-500 shadow-lg shadow-success-100 dark:shadow-none': '{{ $activeFilter }}' === 'assigned',
                        'border-gray-200 dark:border-gray-700 hover:border-success-200 dark:hover:border-success-800': '{{ $activeFilter }}' !== 'assigned'
                     }">
                    <div class="flex items-center gap-3">
                        <div class="bg-success-100 dark:bg-success-900/50 rounded-full p-2">
                            <x-heroicon-o-check-badge class="w-5 h-5 text-success-600 dark:text-success-400" />
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                {{ __('Assigned') }}
                                <x-heroicon-o-adjustments-horizontal class="w-3 h-3" />
                            </p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->stats['assigned'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Filter Indicator --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <span class="mr-2">{{ __('Showing:') }}</span>
            <span class="font-medium">
                @if($activeFilter === 'pendingSupervisor')
                    {{ __('Projects Needing Supervisor') }}
                @elseif($activeFilter === 'pendingReviewers')
                    {{ __('Projects Needing Reviewers') }}
                @elseif($activeFilter === 'assigned')
                    {{ __('Fully Assigned Projects') }}
                @else
                    {{ __('All Projects') }}
                @endif
            </span>
        </div>
    </div>

    {{-- Projects Grid --}}
    <div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
            @foreach($projects as $project)
            <div
                class="group bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative"
                wire:key="project-{{ $project->id }}"
                x-data="{
                    showDetails: false,
                    savingProject: false,
                    flash: false,
                    hasSupervisor: @js($project->has_supervisor),
                    hasFirstReviewer: @js($project->has_first_reviewer),
                    init() {
                        this.$watch('hasSupervisor', value => {
                            if (!value) {
                                this.hasFirstReviewer = false;
                            }
                        });
                    },
                    updateSupervisorState(exists) {
                        this.hasSupervisor = exists;
                        this.savingProject = false;
                        if (!exists) {
                            this.hasFirstReviewer = false;
                        }
                    },
                    updateFirstReviewerState(exists) {
                        this.hasFirstReviewer = exists;
                        this.savingProject = false;
                    }
                }"
                wire:loading.class="opacity-25 pointer-events-none"
                wire:target="assignSupervisor.{{ $project->id }},assignFirstReviewer.{{ $project->id }},assignSecondReviewer.{{ $project->id }}"
                @supervisor-assigned.window="if ($event.detail.projectId === {{ $project->id }}) {
                    updateSupervisorState($event.detail.exists);
                }"
                @reviewer-assigned.window="if ($event.detail.projectId === {{ $project->id }}) {
                    updateFirstReviewerState($event.detail.exists);
                }"
            >
                {{-- Project Header with Status Info --}}
                <div
                    class="flex justify-between items-start cursor-pointer p-4"
                    @click="showDetails = !showDetails"
                    x-tooltip.raw="{{ __('Click to expand/collapse assignment options') }}"
                >
                    <div class="group-hover:translate-x-2 transition-transform duration-300">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                {{ $project->students_names }}
                                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    #{{ $project->final_internship_agreements->first()?->student?->id_pfe }}
                                </span>
                            </h3>
                            <a 
                                href="{{ route('filament.Administration.resources.projects.view', $project) }}"
                                class="text-gray-400 hover:text-primary-500 transition-colors"
                                @click.stop
                                x-tooltip.raw="{{ __('View project details') }}"
                                target="_blank"
                            >
                                <x-heroicon-o-eye class="w-5 h-5" />
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $project->final_internship_agreements->first()?->organization?->name }}
                        </p>
                        {{-- @if($project->suggestedInternalSupervisor())
                            <p class="mt-1 text-sm text-primary-600 dark:text-primary-400 flex items-center gap-1">
                                <x-heroicon-o-user-circle class="w-4 h-4" />
                                <span>{{ __('Student suggested:') }} {{ $project->suggestedInternalSupervisor()->name }}</span>
                            </p>
                        @endif --}}
                    </div>
                    <div
                        class="flex-shrink-0"
                        x-tooltip="tooltips.status[
                            '{{ $project->has_second_reviewer ? 'complete' :
                            ($project->has_first_reviewer ? 'firstReviewer' :
                            ($project->has_supervisor ? 'supervisor' : 'pending')) }}'
                        ]"
                    >
                        @php
                            $statusIcon = match(true) {
                                $project->has_second_reviewer => 'check-circle',
                                $project->has_first_reviewer => 'clock',
                                $project->has_supervisor => 'arrow-path',
                                default => 'x-circle'
                            };
                            $statusColor = match(true) {
                                $project->has_second_reviewer => 'text-success-500',
                                $project->has_first_reviewer => 'text-warning-500',
                                $project->has_supervisor => 'text-info-500',
                                default => 'text-danger-500'
                            };
                        @endphp
                        <x-dynamic-component
                            :component="'heroicon-o-' . $statusIcon"
                            class="w-6 h-6 {{ $statusColor }}"
                        />
                    </div>
                </div>

                {{-- Assignment Fields with Tooltips --}}
                <div class="space-y-3 p-4" x-show="showDetails">
                    {{-- Supervisor Selection --}}
                    <div class="relative" x-tooltip="tooltips.supervisor">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                            <span>{{ __('Supervisor') }}</span>
                            <span x-show="@js($project->has_supervisor)" class="text-success-500">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                            </span>
                        </label>
                        <select
                            wire:change="assignSupervisor({{ $project->id }}, $event.target.value)"
                            x-on:change="savingProject = true"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 transition-colors duration-200"
                        >
                            <option value="">{{ __('Select Supervisor') }}</option>
                            @foreach($departmentProfessors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{ optional($project->academic_supervisor())->id === $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }} (Encad: {{ $professor->supervisor_count }}, Exam: {{ $professor->reviewer_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- First Reviewer Selection --}}
                    <div
                        x-cloak
                        x-show="hasSupervisor"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="relative"
                        wire:key="first-reviewer-{{ $project->id }}"
                        x-tooltip.raw="tooltips.firstReviewer"
                    >
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                            {{ __('First Reviewer') }}
                            <span x-show="@js($project->has_first_reviewer)" class="text-success-500">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                            </span>
                        </label>
                        <select
                            wire:change="assignFirstReviewer({{ $project->id }}, $event.target.value)"
                             x-on:change="savingProject = true"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600"
                        >
                            <option value="">{{ __('Select First Reviewer') }}</option>
                            @foreach($departmentProfessors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{ optional($project->first_reviewer())->id === $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }} (Encad: {{ $professor->supervisor_count }}, Exam: {{ $professor->reviewer_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Second Reviewer Selection --}}
                    <div
                        x-show="hasFirstReviewer"
                        x-transition
                        class="relative"
                        x-tooltip.raw="tooltips.secondReviewer"
                    >
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                            {{ __('Second Reviewer') }}
                            <span x-show="@js($project->has_second_reviewer)" class="text-success-500">
                                <x-heroicon-o-check-circle class="w-4 h-4" />
                            </span>
                        </label>
                        <select
                            wire:change="assignSecondReviewer({{ $project->id }}, $event.target.value)"
                             x-on:change="savingProject = true"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600"
                        >
                            <option value="">{{ __('Select Second Reviewer') }}</option>
                            @foreach($departmentProfessors as $professor)
                                <option
                                    value="{{ $professor->id }}"
                                    {{ optional($project->second_reviewer())->id === $professor->id ? 'selected' : '' }}
                                >
                                    {{ $professor->name }} (Encad: {{ $professor->supervisor_count }}, Exam: {{ $professor->reviewer_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-2">
                        @error('assignment')
                            <p class="text-sm text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Assignment Progress --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ __('Assignment Progress') }}</span>
                             <span x-text="(() => {
                                 const supervisor = @js($project->has_supervisor);
                                 const firstReviewer = @js($project->has_first_reviewer);
                                 const secondReviewer = @js($project->has_second_reviewer);

                                if (secondReviewer) return '100%';
                                if (firstReviewer) return '66%';
                                if (supervisor) return '33%';
                                return '0%';
                            })()"></span>
                        </div>
                        <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-primary-500 transition-all duration-500"
                                :style="{ width: (() => {
                                     const supervisor = @js($project->has_supervisor);
                                     const firstReviewer = @js($project->has_first_reviewer);
                                     const secondReviewer = @js($project->has_second_reviewer);

                                    if (secondReviewer) return '100%';
                                    if (firstReviewer) return '66%';
                                    if (supervisor) return '33%';
                                    return '0%';
                                })() }"
                            ></div>
                        </div>
                    </div>

                </div>

                {{-- Loading Overlay with Improved Animation --}}
                <div
                    wire:loading.delay
                    wire:target="assignSupervisor.{{ $project->id }},assignFirstReviewer.{{ $project->id }},assignSecondReviewer.{{ $project->id }}"
                    x-show="savingProject"
                    x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur flex items-center justify-center z-20"
                >
                    <div class="flex flex-col items-center space-y-2">
                        <svg class="animate-spin h-8 w-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('Saving...') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Load More Button --}}
        @if($projects->count() >= $perPage)
            <div class="mt-6 text-center">
                <button
                    wire:click="loadMore"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="loadMore">
                        {{ __('Display more projects') }}
                    </span>
                    <span wire:loading wire:target="loadMore" class="inline-flex items-center">
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-2 animate-spin" />
                       
                    </span>
                </button>
            </div>
        @endif
    </div>

    {{-- Footer with Help Text --}}
    <div class="mt-4 px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-lg">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center space-x-2">
                <x-heroicon-o-clock class="w-4 h-4" />
                <span>{{ __('Last updated') }}: {{ now()->format('H:i:s') }}</span>
            </p>
            <div class="text-sm text-gray-600 dark:text-gray-400 animate-pulse flex items-center space-x-2">
                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                <span>{{ __('Auto-refreshing...') }}</span>
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Tip: Click on a project card to expand and make assignments. The status icon will update automatically as you progress.') }}
        </p>
    </div>
</div>
