<div
    class="space-y-6"
    wire:poll.{{ $this->pooling }}
    x-data="{
        showHelp: false,
        tooltips: {
            supervisor: '{{ __("Select a supervisor for this project") }}',
            firstReviewer: '{{ __("First reviewer can be assigned after selecting a supervisor") }}',
            secondReviewer: '{{ __("Second reviewer can be assigned after selecting first reviewer") }}',
            status: {
                pending: '{{ __("No assignments yet") }}',
                supervisor: '{{ __("Supervisor assigned, waiting for reviewers") }}',
                firstReviewer: '{{ __("First reviewer assigned, needs second reviewer") }}',
                complete: '{{ __("All assignments complete") }}'
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
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3" wire:loading.class="opacity-50">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg group"
             x-tooltip.raw="{{ __('Total number of projects in your department') }}"
        >
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->stats['total'] }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Total Projects') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg group"
             x-tooltip.raw="{{ __('Projects that need supervisor and reviewer assignments') }}"
        >
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-warning-100 dark:bg-warning-900">
                        <x-heroicon-o-clock class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->stats['pending'] }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Pending Assignment') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg group"
             x-tooltip.raw="{{ __('Projects with complete supervisor and reviewer assignments') }}"
        >
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-success-100 dark:bg-success-900">
                        <x-heroicon-o-check-badge class="w-6 h-6 text-success-600 dark:text-success-400" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->stats['assigned'] }}</h2>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Fully Assigned') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3" wire:loading.class="opacity-50">
        @foreach($projects as $project)
        <div
            class="group bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            wire:key="project-{{ $project->id }}"
            x-data="{
                showDetails: false,
                saving: false,
                flash: false,
                supervisorSelected: @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->exists()),
                firstReviewerSelected: @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->exists()),
                secondReviewerSelected: @js($project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->exists())
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
                        <span x-show="supervisorSelected" class="text-success-500">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                        </span>
                    </label>
                    <select
                        wire:change="assignSupervisor({{ $project->id }}, $event.target.value)"
                        x-on:change="saving = true; supervisorSelected = $event.target.value !== ''"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 transition-colors duration-200"
                    >
                        <option value="">{{ __('Select Supervisor') }}</option>
                        @foreach($departmentProfessors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::Supervisor)->first()?->id === $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- First Reviewer Selection --}}
                <div
                    x-show="supervisorSelected"
                    class="relative"
                    x-tooltip.raw="tooltips.firstReviewer"
                >
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('First Reviewer') }}
                    </label>
                    <select
                        wire:change="assignFirstReviewer({{ $project->id }}, $event.target.value)"
                        x-on:change="saving = true; firstReviewerSelected = $event.target.value !== ''"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600"
                    >
                        <option value="">{{ __('Select First Reviewer') }}</option>
                        @foreach($departmentProfessors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::FirstReviewer)->first()?->id === $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Second Reviewer Selection --}}
                <div
                    x-show="firstReviewerSelected"
                    x-transition
                    class="relative"
                    x-tooltip.raw="tooltips.secondReviewer"
                >
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Second Reviewer') }}
                    </label>
                    <select
                        wire:change="assignSecondReviewer({{ $project->id }}, $event.target.value)"
                        x-on:change="saving = true; secondReviewerSelected = $event.target.value !== ''"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600"
                    >
                        <option value="">{{ __('Select Second Reviewer') }}</option>
                        @foreach($departmentProfessors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                {{ $project->professors()->wherePivot('jury_role', \App\Enums\JuryRole::SecondReviewer)->first()?->id === $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Assignment Progress --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ __('Assignment Progress') }}</span>
                        <span x-text="(() => {
                            if (secondReviewerSelected) return '100%';
                            if (firstReviewerSelected) return '66%';
                            if (supervisorSelected) return '33%';
                            return '0%';
                        })()"></span>
                    </div>
                    <div class="mt-2 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-primary-500 transition-all duration-500"
                            :style="{ width: (() => {
                                if (secondReviewerSelected) return '100%';
                                if (firstReviewerSelected) return '66%';
                                if (supervisorSelected) return '33%';
                                return '0%';
                            })() }"
                        ></div>
                    </div>
                </div>

            </div>

            {{-- Loading Overlay with Improved Animation --}}
            <div
                wire:loading
                wire:target="assignSupervisor, assignFirstReviewer, assignSecondReviewer"
                x-show="saving"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-200/50 dark:bg-gray-700/50 backdrop-blur-sm flex items-center justify-center"
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
