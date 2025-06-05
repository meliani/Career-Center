<x-filament-panels::page>
    {{-- Enhanced Beautiful Filter Widget --}}
    <div 
        x-data="{ 
            isCollapsed: $persist(false).as('project-tabs-collapsed'),
            isPinned: $persist(false).as('project-tabs-pinned'),
            isHovered: false
        }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        class="overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6 transition-all duration-300 border border-gray-200 dark:border-gray-700"
        :class="{ 
            'h-auto': isPinned || (!isCollapsed && !isPinned) || (isCollapsed && isHovered),
            'h-16': isCollapsed && !isPinned && !isHovered
        }"
    >
        {{-- Enhanced Header with Gradient Background --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{__('Project Filters')}}
                    </h3>
                </div>
                
                {{-- Enhanced Active Filters Display --}}
                <div class="flex items-center gap-2">
                    @if (($this->activeTab ?? 'all') !== 'all')
                        @php
                            $tabLabel = match($this->activeTab) {
                                'programmed' => __('Programmed'),
                                'not_programmed' => __('Not Programmed'),
                                default => ucfirst($this->activeTab)
                            };
                        @endphp
                        <div class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 rounded-full border border-blue-200 dark:border-blue-800">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $tabLabel }}
                        </div>
                    @endif
                    @if ($this->defenseStatusTab ?? false)
                        @php
                            $statusEnum = \App\Enums\DefenseStatus::from($this->defenseStatusTab);
                        @endphp
                        <div class="inline-flex items-center gap-1">
                            <x-filament::badge :color="$statusEnum->getColor()" size="md">
                                {{ $statusEnum->getLabel() }}
                            </x-filament::badge>
                        </div>
                    @endif
                    @if (($this->activeTab ?? 'all') === 'all' && !($this->defenseStatusTab ?? false))
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-md">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            No filters active
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Enhanced Pin Button --}}
            <button
                @click="isPinned = !isPinned"
                :class="{ 
                    'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400': isPinned,
                    'bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300': !isPinned 
                }"
                class="flex h-8 w-8 items-center justify-center rounded-lg transition-all duration-200 shadow-sm"
                :title="isPinned ? 'Unpin filters' : 'Pin filters open'"
            >
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2L13 5H11V9H9V5H7L10 2Z" />
                    <path d="M4 10H16V12H4V10Z" />
                    <path d="M7 15H13V17H7V15Z" />
                </svg>
            </button>
        </div>        {{-- Enhanced Collapsible Content --}}
        <div 
            x-show="isPinned || (!isCollapsed && !isPinned) || (isCollapsed && isHovered)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="bg-white dark:bg-gray-900"
        >
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                {{-- Programming Status Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-100 dark:bg-blue-900/50">
                                <svg class="h-3 w-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{__('Programming Status')}}</h4>
                        </div>
                        @if (($this->activeTab ?? 'all') !== 'all')
                            <button
                                wire:click="$set('activeTab', 'all')"
                                class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-2 py-1 rounded-md transition-colors"
                                title="Clear Filter"
                            >
                                Clear
                            </button>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->getProgrammingStatusTabs() as $tabKey => $tab)
                            @php
                                $isActive = ($this->activeTab ?? 'all') === $tabKey;
                            @endphp
                            <button
                                wire:click="$set('activeTab', '{{ $tabKey }}')"
                                @if($isActive)
                                    style="background-color: #2563eb !important; color: white !important; border-color: #2563eb !important;"
                                    class="group relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 shadow-lg shadow-blue-500/25 border"
                                    onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.borderColor='#1d4ed8';"
                                    onmouseout="this.style.backgroundColor='#2563eb'; this.style.borderColor='#2563eb';"
                                @else
                                    class="group relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 shadow-sm border bg-white hover:bg-gray-50 text-gray-700 border-gray-200 hover:border-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 dark:border-gray-700 dark:hover:border-gray-600"
                                @endif
                            >
                                {{ $tab->getLabel() }}
                                @if ($tab->getBadge())
                                    <span 
                                        @if($isActive)
                                            style="background-color: rgba(255,255,255,0.3); color: white;"
                                        @endif
                                        class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold
                                        @if(!$isActive)
                                            bg-gray-100 text-gray-600 group-hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:group-hover:bg-gray-600
                                        @endif">
                                        {{ $tab->getBadge() }}
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Defense Status Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-green-100 dark:bg-green-900/50">
                                <svg class="h-3 w-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{__('Defense Status')}}</h4>
                        </div>
                        @if ($this->defenseStatusTab ?? false)
                            <button
                                wire:click="$set('defenseStatusTab', null)"
                                class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-2 py-1 rounded-md transition-colors"
                                title="Clear Filter"
                            >
                                Clear
                            </button>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->getDefenseStatusTabs() as $statusKey => $statusTab)
                            @php
                                $isActive = ($this->defenseStatusTab ?? '') === $statusKey;
                                $statusEnum = \App\Enums\DefenseStatus::from($statusKey);
                                $statusColor = $statusEnum->getColor();
                                
                                $colorMap = [
                                    'success' => ['bg' => '#059669', 'hover' => '#047857'],
                                    'warning' => ['bg' => '#eab308', 'hover' => '#ca8a04'],
                                    'danger' => ['bg' => '#dc2626', 'hover' => '#b91c1c'],
                                    'primary' => ['bg' => '#2563eb', 'hover' => '#1d4ed8'],
                                    'secondary' => ['bg' => '#4b5563', 'hover' => '#374151'],
                                ];
                                
                                $colors = $colorMap[$statusColor] ?? $colorMap['secondary'];
                            @endphp
                            <button
                                wire:click="$set('defenseStatusTab', '{{ $statusKey }}')"
                                @if($isActive)
                                    style="background-color: {{ $colors['bg'] }} !important; color: white !important; border-color: {{ $colors['bg'] }} !important;"
                                    class="group relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 shadow-lg border"
                                    onmouseover="this.style.backgroundColor='{{ $colors['hover'] }}'; this.style.borderColor='{{ $colors['hover'] }}';"
                                    onmouseout="this.style.backgroundColor='{{ $colors['bg'] }}'; this.style.borderColor='{{ $colors['bg'] }}';"
                                @else
                                    class="group relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 shadow-sm border
                                        @if($statusColor === 'success')
                                            bg-white hover:bg-green-50 text-green-700 border-green-200 hover:border-green-300 dark:bg-gray-800 dark:hover:bg-green-900/20 dark:text-green-400 dark:border-green-800
                                        @elseif($statusColor === 'warning')
                                            bg-white hover:bg-yellow-50 text-yellow-700 border-yellow-200 hover:border-yellow-300 dark:bg-gray-800 dark:hover:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800
                                        @elseif($statusColor === 'danger')
                                            bg-white hover:bg-red-50 text-red-700 border-red-200 hover:border-red-300 dark:bg-gray-800 dark:hover:bg-red-900/20 dark:text-red-400 dark:border-red-800
                                        @elseif($statusColor === 'primary')
                                            bg-white hover:bg-blue-50 text-blue-700 border-blue-200 hover:border-blue-300 dark:bg-gray-800 dark:hover:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800
                                        @else
                                            bg-white hover:bg-gray-50 text-gray-700 border-gray-200 hover:border-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 dark:border-gray-700
                                        @endif"
                                @endif
                            >
                                <span class="truncate">{{ $statusTab->getLabel() }}</span>
                                @if ($statusTab->getBadge())
                                    <span 
                                        @if($isActive)
                                            style="background-color: rgba(255,255,255,0.3); color: white;"
                                        @endif
                                        class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold
                                        @if(!$isActive)
                                            @if($statusColor === 'success')
                                                bg-green-100 text-green-700 group-hover:bg-green-200 dark:bg-green-900/50 dark:text-green-300
                                            @elseif($statusColor === 'warning')
                                                bg-yellow-100 text-yellow-700 group-hover:bg-yellow-200 dark:bg-yellow-900/50 dark:text-yellow-300
                                            @elseif($statusColor === 'danger')
                                                bg-red-100 text-red-700 group-hover:bg-red-200 dark:bg-red-900/50 dark:text-red-300
                                            @elseif($statusColor === 'primary')
                                                bg-blue-100 text-blue-700 group-hover:bg-blue-200 dark:bg-blue-900/50 dark:text-blue-300
                                            @else
                                                bg-gray-100 text-gray-700 group-hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300
                                            @endif
                                        @endif">
                                        {{ $statusTab->getBadge() }}
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>        {{-- Enhanced Collapsed State Preview --}}
        <div 
            x-show="isCollapsed && !isPinned && !isHovered"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="px-6 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-t border-gray-200 dark:border-gray-600"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @php
                        $programmingLabel = match($this->activeTab ?? 'all') {
                            'all' => __('All Projects'),
                            'programmed' => __('Programmed'),
                            'not_programmed' => __('Not Programmed'),
                            default => ucfirst($this->activeTab ?? 'all')
                        };
                    @endphp
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="font-medium">Programming:</span> 
                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $programmingLabel }}</span>
                    </span>
                    @if ($this->defenseStatusTab ?? false)
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            <span class="font-medium">Status:</span> 
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ \App\Enums\DefenseStatus::from($this->defenseStatusTab)->getLabel() }}</span>
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    <span>Hover to expand</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Original Table Content --}}
    {{ $this->table }}
</x-filament-panels::page>
