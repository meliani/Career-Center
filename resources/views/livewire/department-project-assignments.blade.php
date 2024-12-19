<div
    class="space-y-6"
    wire:poll.{{ $this->pooling }}
    x-data="{
        showHelp: false,
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
    {{-- Help Button --}}
    <div class="flex justify-end">
        <button
            @click="showHelp = !showHelp"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
            <x-heroicon-o-question-mark-circle class="w-5 h-5 mr-2" />
            {{ __('How it works') }}
        </button>
    </div>

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
        </ul>
    </div>

    {{-- Stats Section with tooltips --}}
    <div class="px-4 sm:px-6 lg:px-8 mb-2">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
            <x-heroicon-o-funnel class="w-4 h-4" />
            <span>{{ __('Filter by status:') }}</span>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2" wire:loading.class="opacity-50">
            {{-- Total Projects Card --}}
            <div class="relative flex-1 min-w-[180px] max-w-[200px]" x-tooltip="tooltips.filters.all">
                <div wire:click="setFilter('all')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg ring-1 ring-black ring-opacity-5 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                     :class="{ 'ring-2 ring-primary-500': '{{ $activeFilter }}' === 'all' }">
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
            <div class="relative flex-1 min-w-[180px] max-w-[200px]" x-tooltip="tooltips.filters.pendingSupervisor">
                <div wire:click="setFilter('pendingSupervisor')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg ring-1 ring-black ring-opacity-5 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                     :class="{ 'ring-2 ring-danger-500': '{{ $activeFilter }}' === 'pendingSupervisor' }">
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
            <div class="relative flex-1 min-w-[180px] max-w-[200px]" x-tooltip="tooltips.filters.pendingReviewers">
                <div wire:click="setFilter('pendingReviewers')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg ring-1 ring-black ring-opacity-5 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                     :class="{ 'ring-2 ring-warning-500': '{{ $activeFilter }}' === 'pendingReviewers' }">
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
            <div class="relative flex-1 min-w-[180px] max-w-[200px]" x-tooltip="tooltips.filters.assigned">
                <div wire:click="setFilter('assigned')"
                     class="relative bg-white dark:bg-gray-800 rounded-lg ring-1 ring-black ring-opacity-5 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                     :class="{ 'ring-2 ring-success-500': '{{ $activeFilter }}' === 'assigned' }">
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
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        @foreach($projects as $project)
        <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative"
            wire:key="project-{{ $project->id }}"
            x-data="{
                showDetails: false,
                savingProject: false,
                flash: false,
                hasSupervisor: @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists()),
                hasFirstReviewer: @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists()),
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                        {{ $project->students_names }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $project->final_internship_agreements->first()?->organization?->name }}
                    </p>
                </div>
                <div
                    class="flex-shrink-0"
                    x-tooltip="tooltips.status[
                        '{{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists() ? 'complete' :
                        ($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists() ? 'firstReviewer' :
                        ($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists() ? 'supervisor' : 'pending')) }}'
                    ]"
                >
                    @php
                        $statusIcon = match(true) {
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists() => 'check-circle',
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists() => 'clock',
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists() => 'arrow-path',
                            default => 'x-circle'
                        };
                        $statusColor = match(true) {
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists() => 'text-success-500',
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists() => 'text-warning-500',
                            $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists() => 'text-info-500',
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
                <div class="relative" x-tooltip.raw="tooltips.supervisor">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                        <span>{{ __('Supervisor') }}</span>
                        <span x-show="@js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists())" class="text-success-500">
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
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->first()?->id === $professor->id ? 'selected' : '' }}
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
                        <span x-show="@js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists())" class="text-success-500">
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
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->first()?->id === $professor->id ? 'selected' : '' }}
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
                        <span x-show="@js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists())" class="text-success-500">
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
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->first()?->id === $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }} (Encad: {{ $professor->supervisor_count }}, Exam: {{ $professor->reviewer_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Assignment Progress --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ __('Assignment Progress') }}</span>
                         <span x-text="(() => {
                             const supervisor = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists());
                             const firstReviewer = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists());
                             const secondReviewer = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists());

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
                                 const supervisor = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists());
                                 const firstReviewer = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists());
                                 const secondReviewer = @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists());

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
