<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6" x-data="{
            selectedMonth: 'all',
            selectedStatus: 'all',
            isFiltered() {
                return this.selectedMonth !== 'all'
            },
            getVisibleCount(timelines) {
                return timelines.filter(timeline =>
                    this.selectedStatus === 'all' ||
                    timeline.status === this.selectedStatus
                ).length;
            }
        }">
            <div class="relative -mx-6 -mt-6 mb-6 px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-primary-500/10 rounded-lg">
                            <x-filament::icon
                                icon="heroicon-o-calendar"
                                class="w-5 h-5 text-primary-500"
                            />
                        </div>
                        <h3 class="text-lg font-bold tracking-tight text-gray-950">
                            {{ __('Yearly Timeline') }}
                        </h3>
                    </div>

                    <div class="flex items-center space-x-3">
                        <select x-model="selectedStatus" class="pl-3 pr-10 py-2 text-sm bg-white/50 backdrop-blur-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition duration-200">
                            <option value="all">{{ __('All Statuses') }}</option>
                            @foreach(\App\Enums\TimelineStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                            @endforeach
                        </select>

                        <select x-model="selectedMonth" class="pl-3 pr-10 py-2 text-sm bg-white/50 backdrop-blur-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition duration-200">
                            <option value="all">{{ __('All Months') }}</option>
                            @foreach($this->getGroupedTimelines()->sortByDesc(function($timelines, $month) {
                                return \Carbon\Carbon::parse($month)->format('Y-m');
                            }) as $month => $timelines)
                                <option value="{{ str_replace(' ', '_', $month) }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="relative pl-4">
                <div class="absolute left-4 top-0 w-0.5 h-full bg-gray-200 -ml-0.5" aria-hidden="true"></div>

                <ul class="relative space-y-8 pt-2">
                    @foreach($this->getGroupedTimelines()->sortByDesc(function($timelines, $month) {
                        return \Carbon\Carbon::parse($month)->format('Y-m');
                    }) as $month => $timelines)
                        <li class="mb-6" x-show="selectedMonth === 'all' || selectedMonth === '{{ str_replace(' ', '_', $month) }}'">
                            <!-- Month header -->
                            <div x-data="{
                                isOpen: @if(\Carbon\Carbon::parse($month)->format('F Y') === now()->format('F Y')) true @else false @endif,
                                timelines: {{
                                    Js::from($timelines->map(fn($timeline) => [
                                        'status' => $timeline->status->value
                                    ]))
                                }}
                            }"
                            x-effect="if(selectedMonth === '{{ str_replace(' ', '_', $month) }}') isOpen = true"
                            class="mb-4">
                                <h3 class="text-base font-bold text-gray-700 mb-3 flex items-center justify-between"
                                    :class="{ 'cursor-pointer hover:text-primary-500 transition-colors duration-200': !isFiltered() }"
                                    @click="if(!isFiltered()) { isOpen = !isOpen }">
                                    <div class="flex items-center">
                                        <template x-if="!isFiltered()">
                                            <svg x-show="!isOpen" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!isFiltered()">
                                            <svg x-show="isOpen" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </template>
                                        <span>{{ $month }}</span>
                                    </div>

                                    <div x-show="!isOpen"
                                         x-transition
                                         class="flex items-center">
                                        <span class="px-2 py-0.5 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                                            <span x-text="getVisibleCount(timelines)"></span> {{ __('items') }}
                                        </span>
                                    </div>
                                </h3>

                                <!-- Timeline items with improved spacing -->
                                <div x-show="isOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform translate-y-0"
                                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                                     class="space-y-4">
                                    @foreach($timelines as $index => $timeline)
                                        <div class="timeline-item cursor-pointer"
                                             x-data="{ showDescription: false }"
                                             @mouseenter="showDescription = true"
                                             @mouseleave="showDescription = false"
                                             :class="{ 'scale-[1.02] shadow-lg': showDescription }"
                                             x-show="(selectedMonth === 'all' || selectedMonth === '{{ str_replace(' ', '_', $month) }}') && (selectedStatus === 'all' || selectedStatus === '{{ $timeline->status->value }}')"
                                             class="transform transition-all duration-200 ease-in-out">
                                            <div class="relative mt-3 mr-4 flex-shrink-0">
                                                <div @class([
                                                    'h-4 w-4 rounded-full ring-4',
                                                    'ring-white' => !$timeline->is_highlight,
                                                    'ring-yellow-100' => $timeline->is_highlight,
                                                ]) style="background-color: {{ $timeline->category->getColor() }}"></div>
                                            </div>

                                            <div @class([
                                                'flex-grow rounded-lg p-4 ring-1 ring-inset transition-all duration-200',
                                                'ring-gray-200 hover:ring-gray-300 hover:bg-gray-50' => !$timeline->is_highlight,
                                                'ring-yellow-200 bg-yellow-50 hover:ring-yellow-300 hover:bg-yellow-100' => $timeline->is_highlight,
                                            ])>
                                                <div class="flex justify-between items-start">
                                                    <div class="font-medium">{{ $timeline->title }}</div>
                                                    <div class="flex items-center gap-x-2">
                                                        <span @class([
                                                            'px-2 py-0.5 text-xs font-medium rounded-full',
                                                            'bg-gray-100 text-gray-600' => $timeline->status->getColor() === 'gray',
                                                            'bg-blue-100 text-blue-600' => $timeline->status->getColor() === 'info',
                                                            'bg-green-100 text-green-600' => $timeline->status->getColor() === 'success',
                                                            'bg-red-100 text-red-600' => $timeline->status->getColor() === 'danger',
                                                        ])>
                                                            {{ $timeline->status->getLabel() }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Description with hover effect -->
                                                <div x-show="showDescription"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 max-h-0"
                                                     x-transition:enter-end="opacity-100 max-h-96"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 max-h-96"
                                                     x-transition:leave-end="opacity-0 max-h-0"
                                                     class="mt-2 text-sm text-gray-600 overflow-hidden">
                                                    {{ $timeline->description }}
                                                </div>

                                                <!-- Avatars and Priority Badge -->
                                                <div x-show="showDescription"
                                                     x-transition
                                                     class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                                                    <div class="flex -space-x-2 overflow-hidden">
                                                        @foreach($timeline->assignedUsers as $user)
                                                        <div title="{{ $user->name }}">
                                                            <x-filament-panels::avatar.user :user="$user" />
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <span @class([
                                                        'px-2 py-1 text-xs font-medium rounded-full',
                                                        'bg-gray-100 text-gray-600' => $timeline->priority->getColor() === 'gray',
                                                        'bg-yellow-100 text-yellow-600' => $timeline->priority->getColor() === 'warning',
                                                        'bg-green-100 text-green-600' => $timeline->priority->getColor() === 'success',
                                                        'bg-red-100 text-red-600' => $timeline->priority->getColor() === 'danger',
                                                    ])>
                                                        {{ $timeline->priority->getLabel() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

