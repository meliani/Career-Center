<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6" x-data="{
            selectedStatus: 'all',
            getVisibleCount(projects) {
                return projects.filter(project =>
                    this.selectedStatus === 'all' ||
                    project.status === this.selectedStatus
                ).length;
            }
        }">
            <div class="relative -mx-6 -mt-6 mb-6 px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-primary-500/10 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-academic-cap"
                                class="w-5 h-5 text-primary-500"
                            />
                        </div>
                        <h3 class="text-lg font-bold tracking-tight text-gray-950">
                            {{ __('Projects Defense Management') }}
                        </h3>
                    </div>

                    <div class="flex items-center space-x-3">
                        <select x-model="selectedStatus" class="pl-3 pr-10 py-2 text-sm bg-white/50 backdrop-blur-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition duration-200">
                            <option value="all">{{ __('All Statuses') }}</option>
                            @foreach(['Pending', 'Authorized', 'Completed'] as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($this->getProjects() as $project)
                    <div x-show="selectedStatus === 'all' || selectedStatus === '{{ $project->defense_status }}'"
                         x-transition
                         class="relative group">
                        <div class="h-full rounded-lg border border-gray-200 hover:border-primary-500 hover:ring-1 hover:ring-primary-500 transition-all duration-200 p-4 cursor-pointer"
                             x-data="{ expanded: false }"
                             @mouseenter="expanded = true"
                             @mouseleave="expanded = false">

                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium text-gray-900">{{ $project->organization->name }}</div>
                                <div @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-gray-100 text-gray-600' => $project->defense_status === 'Pending',
                                    'bg-green-100 text-green-600' => $project->defense_status === 'Authorized',
                                    'bg-blue-100 text-blue-600' => $project->defense_status === 'Completed',
                                    'bg-yellow-100 text-yellow-600' => $project->defense_status === 'Postponed',
                                    'bg-red-100 text-red-600' => $project->defense_status === 'Rejected',
                                ])>
                                    {{ $project->defense_status }}
                                </div>
                            </div>

                            <div class="text-sm text-gray-600 mb-2">
                                {{ $project->students_names }}
                            </div>

                            <div x-show="expanded"
                                 x-transition
                                 class="mt-3 space-y-3">
                                <div class="text-sm text-gray-800">
                                    <strong>{{ $project->title }}</strong>
                                </div>

                                @if($project->timetable && $project->timetable->timeslot)
                                    <div class="text-sm text-gray-600">
                                        <strong>{{ __('Defense:') }}</strong> {{ $project->timetable->timeslot->start_time->format('d M Y H:i') }}
                                    </div>
                                @endif

                                <div class="flex justify-end space-x-2 pt-2">
                                    <button wire:click="mountAction('scheduleDefense', { projectId: '{{ $project->id }}' })" class="px-3 py-1 text-xs bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                        {{ __('Schedule') }}
                                    </button>
                                    <button wire:click="mountAction('changeStatus', { projectId: '{{ $project->id }}' })" class="px-3 py-1 text-xs bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                        {{ __('Status') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
