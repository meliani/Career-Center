<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <x-filament::icon
                        icon="heroicon-o-calendar"
                        class="w-5 h-5 text-primary-500"
                    />
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        {{ __('Timeline') }}
                    </h3>
                </div>
            </div>

            <div class="relative">
                <div class="absolute left-4 top-0 w-px h-full bg-gray-200" aria-hidden="true"></div>

                <ul class="space-y-6" x-data="{ openMonths: {} }">
                    @foreach($this->getGroupedTimelines() as $month => $timelines)
                        <li class="mb-4"
                            x-data="{
                                isOpen: true,
                                monthKey: '{{ str_replace(' ', '_', $month) }}'
                            }"
                            x-init="openMonths[monthKey] = true">

                            <h3 class="text-sm font-medium text-gray-500 mb-2 cursor-pointer flex items-center"
                                @click="isOpen = !isOpen; openMonths[monthKey] = isOpen">
                                <svg x-show="!isOpen" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <svg x-show="isOpen" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                {{ $month }}
                            </h3>

                            <div x-show="isOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2">
                                <ul class="space-y-4">
                                    @foreach($timelines as $index => $timeline)
                                        @if($index < 5 || $this->showAll)
                                            <li class="flex items-start">
                                                <div class="relative mt-3 mr-4 flex-shrink-0">
                                                    <div @class([
                                                        'h-3 w-3 rounded-full ring-4',
                                                        'ring-white' => !$timeline->is_highlight,
                                                        'ring-yellow-100' => $timeline->is_highlight,
                                                    ]) style="background-color: {{ $timeline->category->getColor() }}"></div>
                                                </div>

                                                <div @class([
                                                    'flex-grow rounded-md p-3 ring-1 ring-inset transition-shadow',
                                                    'ring-gray-200 hover:ring-gray-300' => !$timeline->is_highlight,
                                                    'ring-yellow-200 bg-yellow-50 hover:ring-yellow-300' => $timeline->is_highlight,
                                                ])>
                                                    <div class="flex justify-between gap-x-4">
                                                        <div class="flex items-center gap-2">
                                                            <x-filament::icon
                                                                :icon="$timeline->category->getIcon()"
                                                                class="w-4 h-4 text-gray-400"
                                                            />
                                                            <span class="py-0.5 text-xs leading-5 text-gray-500">
                                                                <span class="font-medium text-gray-900">
                                                                    {{ $timeline->start_date->format('M d, Y') }}
                                                                    @if($timeline->end_date)
                                                                        - {{ $timeline->end_date->format('M d, Y') }}
                                                                    @endif
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-x-2">
                                                            <span @class([
                                                                'px-2 py-1 text-xs font-medium rounded-full',
                                                                'bg-gray-100 text-gray-600' => $timeline->status->getColor() === 'gray',
                                                                'bg-info-100 text-info-600' => $timeline->status->getColor() === 'info',
                                                                'bg-success-100 text-success-600' => $timeline->status->getColor() === 'success',
                                                                'bg-danger-100 text-danger-600' => $timeline->status->getColor() === 'danger',
                                                            ])>
                                                                {{ $timeline->status->getLabel() }}
                                                            </span>
                                                            <span @class([
                                                                'px-2 py-1 text-xs font-medium rounded-full',
                                                                'bg-gray-100 text-gray-600' => $timeline->priority->getColor() === 'gray',
                                                                'bg-warning-100 text-warning-600' => $timeline->priority->getColor() === 'warning',
                                                                'bg-success-100 text-success-600' => $timeline->priority->getColor() === 'success',
                                                                'bg-danger-100 text-danger-600' => $timeline->priority->getColor() === 'danger',
                                                            ])>
                                                                {{ $timeline->priority->getLabel() }}
                                                            </span>
                                                            @if($timeline->is_highlight)
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-600">
                                                                    {{ __('Highlight') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="text-sm font-semibold leading-6 text-gray-900 mt-2">{{ $timeline->title }}</p>
                                                    @if($timeline->description)
                                                        <p class="mt-1 text-sm leading-6 text-gray-500">{{ $timeline->description }}</p>
                                                    @endif
                                                    @if($timeline->assignedUsers->isNotEmpty())
                                                        <div class="mt-2 flex items-center gap-x-2">
                                                            <x-filament::icon
                                                                icon="heroicon-m-users"
                                                                class="w-4 h-4 text-gray-400"
                                                            />
                                                            <span class="text-xs text-gray-500">
                                                                {{ $timeline->assignedUsers->pluck('name')->join(', ') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>

                                @if(count($timelines) > 5 && !$this->showAll)
                                    <button wire:click="loadMore"
                                            class="mt-4 text-sm text-blue-600 hover:text-blue-800">
                                        Show More ({{ count($timelines) - 5 }} remaining)
                                    </button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

