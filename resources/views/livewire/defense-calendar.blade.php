<div>
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-gray-200">
                <div class="text-center sm:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Calendrier des Soutenances {{ \App\Models\Year::current()->name }}</h2>
                    <p class="mt-2 text-sm text-gray-600">Aperçu des soutenances planifiées et non planifiées</p>
                </div>
                <div class="flex-shrink-0">
                    <img src="{{ asset('/svg/logo-colors.svg') }}" alt="Logo" class="h-16 sm:h-20">
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50/50 px-4 py-4">
                    <!-- Search and Filter Fields -->
                    <div class="flex flex-col gap-4">
                        <!-- Search Bar and Field Filter -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="sm:col-span-3">
                                <label for="search" class="block text-sm font-medium text-gray-900 mb-1">Rechercher</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-lg border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" placeholder="Rechercher par nom, ID, organisation...">
                                </div>
                            </div>
                            <div>
                                <label for="searchField" class="block text-sm font-medium text-gray-900 mb-1">Filtrer par</label>
                                <select wire:model.live="searchField" class="w-full rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                    <option value="all">Tous les champs</option>
                                    <option value="student">Nom de l'étudiant</option>
                                    <option value="pfe_id">ID PFE</option>
                                    <option value="professor">Professeur</option>
                                    <option value="organization">Organisation</option>
                                </select>
                            </div>
                        </div>

                        <!-- Program Filter Tabs -->
                        <div class="-mx-4 px-4 border-t border-gray-200 pt-4">
                            <div class="flex overflow-x-auto hide-scrollbar">
                                <div class="flex space-x-2">
                                    <button type="button" wire:click="$set('programFilter', '')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-gray-100 text-gray-900 ring-1 ring-gray-200' => empty($programFilter),
                                                'text-gray-500 hover:bg-gray-50' => !empty($programFilter),
                                            ])>
                                        <x-heroicon-o-funnel class="w-4 h-4" />
                                        Tous
                                    </button>
                                    
                                    <button type="button" wire:click="$set('programFilter', 'AMOA')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-primary-100 text-primary-900 ring-1 ring-primary-200' => $programFilter === 'AMOA',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'AMOA',
                                            ])>
                                        <x-heroicon-o-light-bulb class="w-4 h-4" />
                                        AMOA
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'ASEDS')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-secondary-100 text-secondary-900 ring-1 ring-secondary-200' => $programFilter === 'ASEDS',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'ASEDS',
                                            ])>
                                        <x-heroicon-o-code-bracket class="w-4 h-4" />
                                        ASEDS
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'DATA')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-success-100 text-success-900 ring-1 ring-success-200' => $programFilter === 'DATA',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'DATA',
                                            ])>
                                        <x-heroicon-o-chart-bar class="w-4 h-4" />
                                        DATA
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'ICCN')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-danger-100 text-danger-900 ring-1 ring-danger-200' => $programFilter === 'ICCN',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'ICCN',
                                            ])>
                                        <x-heroicon-o-shield-check class="w-4 h-4" />
                                        ICCN
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'SESNUM')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-warning-100 text-warning-900 ring-1 ring-warning-200' => $programFilter === 'SESNUM',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'SESNUM',
                                            ])>
                                        <x-heroicon-o-viewfinder-circle class="w-4 h-4" />
                                        SESNUM
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'SMART-ICT')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-info-100 text-info-900 ring-1 ring-info-200' => $programFilter === 'SMART-ICT',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'SMART-ICT',
                                            ])>
                                        <x-heroicon-o-cpu-chip class="w-4 h-4" />
                                        SMART-ICT
                                    </button>

                                    <button type="button" wire:click="$set('programFilter', 'SUD')"
                                            @class([
                                                'shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                                'bg-gray-100 text-gray-900 ring-1 ring-gray-200' => $programFilter === 'SUD',
                                                'text-gray-500 hover:bg-gray-50' => $programFilter !== 'SUD',
                                            ])>
                                        <x-heroicon-o-circle-stack class="w-4 h-4" />
                                        SUD
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Scheduled defenses section -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <h3 class="text-xl font-semibold text-green-600 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Soutenances planifiées
                    </h3>
                    <span class="inline-flex items-center px-4 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        {{ $data->count() }} soutenances planifiées
                    </span>
                </div>

                <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 table-fixed">
                        <thead>
                            <tr class="bg-gray-50">
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 w-[30%] sm:w-auto">Date & Lieu</th>
                                <th scope="col" class="hidden sm:table-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sm:w-auto">Autorisation</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-[35%] sm:w-auto">Étudiant</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-[35%] sm:w-auto">Jury</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($data as $entry)
                                @if(isset($entry['type']) && $entry['type'] === 'holiday')
                                    <tr wire:key="holiday-{{ $entry['id'] }}" class="bg-emerald-50">
                                        <td colspan="4" class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                                        </svg>
                                                    </span>
                                                    <div>
                                                        <p class="text-lg font-semibold text-emerald-900 rtl">
                                                            {{ $entry['islamic_date'] ?? '' }}
                                                        </p>
                                                        <p class="text-emerald-700 mt-1">
                                                            {{ $entry['gregorian_date'] ?? '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center rounded-md bg-emerald-100/60 px-4 py-2 text-sm font-medium text-emerald-700 rtl">
                                                    عطلة
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr wire:key="defense-{{ $entry['id'] }}" class="hover:bg-gray-50">
                                        <td class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                                            <div class="flex flex-col gap-2">
                                                <div class="sm:flex sm:items-center block">
                                                    <div class="flex items-center gap-2 text-gray-900">
                                                        <svg class="h-4 w-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span class="font-medium">{{ $entry['Date Soutenance'] ?? '' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-1.5">
                                                    @if(isset($entry['Heure']) && $entry['Heure'] !== 'Non définie')
                                                        <div class="flex items-center gap-2">
                                                            <svg class="h-4 w-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-gray-700 text-sm">{{ $entry['Heure'] }}</span>
                                                        </div>
                                                    @endif
                                                    @if(isset($entry['Lieu']) && $entry['Lieu'] !== 'Non définie')
                                                        <div class="flex items-center gap-2">
                                                            <svg class="h-4 w-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                            </svg>
                                                            <span class="text-gray-700 text-sm">{{ $entry['Lieu'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hidden sm:table-cell px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                                @if($entry['type'] === 'holiday') bg-emerald-50 text-emerald-700 ring-emerald-600/20
                                                @elseif($entry['Autorisation']['status'] === 'success') bg-green-50 text-green-700 ring-green-600/20
                                                @elseif($entry['Autorisation']['status'] === 'danger') bg-red-50 text-red-700 ring-red-600/20
                                                @else bg-yellow-50 text-yellow-700 ring-yellow-600/20 @endif">
                                                @if($entry['type'] === 'holiday')
                                                    Jour férié
                                                @else
                                                    {{ $entry['Autorisation']['message'] }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-900">
                                            <div class="flex flex-col gap-1.5">
                                                @if($entry['type'] !== 'holiday')
                                                    <div class="sm:hidden shrink-0 flex justify-end">
                                                        @if($entry['Autorisation']['status'] === 'success')
                                                            <div class="h-2 w-2 rounded-full bg-green-400"></div>
                                                        @elseif($entry['Autorisation']['status'] === 'danger')
                                                            <div class="h-2 w-2 rounded-full bg-red-400"></div>
                                                        @else
                                                            <div class="h-2 w-2 rounded-full bg-yellow-400"></div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @forelse($entry['Students'] ?? [] as $index => $student)
                                                    <div class="min-w-0 flex-1 {{ $index > 0 ? 'border-t border-gray-100 pt-1.5' : '' }}">
                                                        <div class="flex items-center justify-between gap-1.5">
                                                            <div class="flex items-center gap-1.5">
                                                                @if($index === 0)
                                                                    <p class="font-medium text-gray-900 truncate">{{ $student['name'] }}</p>
                                                                @else
                                                                    <div class="flex items-center gap-1">
                                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-600">
                                                                            Binôme {{ $index + 1 }}
                                                                        </span>
                                                                        <p class="font-medium text-gray-900 truncate">{{ $student['name'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @if($index === 0 && isset($entry['AdminSupervisor']))
                                                                <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 px-1.5 py-0.5 text-[10px] font-medium text-orange-700 ring-1 ring-inset ring-orange-700/10 shrink-0">
                                                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                    </svg>
                                                                    Référent: {{ $entry['AdminSupervisor'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            <div class="flex flex-col gap-1.5">
                                                                <div class="flex items-center gap-1.5">
                                                                    <span class="text-gray-500 text-xs font-medium">{{ $student['id_pfe'] ?? 'N/A' }}</span>
                                                                    @if(isset($student['program']) && $student['program'] !== 'N/A')
                                                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-[10px] font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                                            {{ $student['program'] }}
                                                                        </span>
                                                                    @endif
                                                                    @if(isset($student['exchange_partner']))
                                                                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                                            <svg class="h-3 w-3 mr-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                                            </svg>
                                                                            {{ $student['exchange_partner'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                @if($index === 0)
                                                                    @if(isset($entry['Organisation']) && $entry['Organisation'] !== 'Non définie')
                                                                        <div class="flex items-center gap-1.5">
                                                                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                                {{ $entry['Organisation'] }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                 
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-gray-500">Non assigné</div>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-900">
                                            <div class="space-y-1.5 min-w-0">
                                                @foreach(explode("\n", $entry['Jury'] ?? '') as $juryLine)
                                                    @if(str_starts_with($juryLine, 'Encadrant:'))
                                                        <div class="group relative">
                                                            <div class="flex items-center gap-1.5 min-w-0">
                                                                <span class="hidden sm:inline-flex items-center shrink-0 rounded bg-blue-50 px-1.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                                    Encadrant
                                                                </span>
                                                                <span class="sm:hidden inline-flex items-center shrink-0 rounded-full bg-blue-50 w-5 h-5 justify-center text-[10px] font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" title="Encadrant">
                                                                    E
                                                                </span>
                                                                <span class="text-sm text-gray-900 truncate">{{ trim(str_replace('Encadrant:', '', $juryLine)) }}</span>
                                                            </div>
                                                            <div class="sm:hidden absolute left-0 -top-8 bg-gray-900 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                                Encadrant
                                                            </div>
                                                        </div>
                                                    @elseif(str_starts_with($juryLine, 'Examinateurs:'))
                                                        <div class="group relative">
                                                            <div class="flex items-center gap-1.5 min-w-0">
                                                                <span class="hidden sm:inline-flex items-center shrink-0 rounded bg-purple-50 px-1.5 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                                                    Examinateurs
                                                                </span>
                                                                <span class="sm:hidden inline-flex items-center shrink-0 rounded-full bg-purple-50 w-5 h-5 justify-center text-[10px] font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10" title="Examinateurs">
                                                                    Ex
                                                                </span>
                                                                <span class="text-sm text-gray-900 truncate">{{ trim(str_replace('Examinateurs:', '', $juryLine)) }}</span>
                                                            </div>
                                                            <div class="sm:hidden absolute left-0 -top-8 bg-gray-900 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                                Examinateurs
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-500">{{ $juryLine }}</span>
                                                    @endif
                                                @endforeach
                                                @if(empty($entry['Jury']))
                                                    <span class="text-gray-500">Non assigné</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 pl-4 pr-3 text-sm sm:pl-6 text-center text-gray-500">
                                        Aucune soutenance planifiée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Non-planned projects section -->
            @if(count($nonPlannedProjects) > 0)
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <h3 class="text-xl font-semibold text-red-600 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Projets sans soutenance planifiée
                    </h3>
                    <span class="inline-flex items-center px-4 py-1.5 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ count($nonPlannedProjects) }} projets en attente
                    </span>
                </div>

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr class="bg-red-50">
                                    <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900 w-[40%]">Étudiant(s)</th>
                                    <th class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900 w-[60%]">Encadrement</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($nonPlannedProjects as $project)
                                <tr wire:key="nonplanned-{{ $project['id'] }}" class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="space-y-3">
                                            @forelse($project['students'] as $index => $student)
                                                <div class="min-w-0 flex-1 {{ $index > 0 ? 'border-t border-gray-100 pt-3' : '' }}">
                                                    <div class="flex flex-col gap-1.5">
                                                        <div class="flex items-center gap-1.5">
                                                            @if($index === 0)
                                                                <p class="font-medium text-gray-900">{{ $student['name'] }}</p>
                                                            @else
                                                                <div class="flex items-center gap-1">
                                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-600">
                                                                        Binôme {{ $index + 1 }}
                                                                    </span>
                                                                    <p class="font-medium text-gray-900">{{ $student['name'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-gray-500 text-xs font-medium">{{ $student['id_pfe'] ?? 'N/A' }}</span>
                                                            @if(isset($student['program']) && $student['program'] !== 'N/A')
                                                                <span class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-[10px] font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                                    {{ $student['program'] }}
                                                                </span>
                                                            @endif
                                                            @if(isset($student['exchange_partner']))
                                                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                                    <svg class="h-3 w-3 mr-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                                    </svg>
                                                                    {{ $student['exchange_partner'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($index === 0 && $project['organisation'] !== 'Non définie')
                                                        <div class="flex items-center gap-1.5 mt-1.5">
                                                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                {{ $project['organisation'] }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="text-gray-500">Non assigné</div>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-700 rounded-md ring-1 ring-inset ring-blue-700/10">
                                                    Encadrant
                                                </span>
                                                <span class="text-sm text-gray-900">{{ $project['supervisor'] }}</span>
                                            </div>

                                            <div class="border-t border-gray-100 pt-4">
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center justify-center bg-purple-50 px-2.5 py-1.5 text-xs font-medium text-purple-700 rounded-md ring-1 ring-inset ring-purple-700/10 min-w-[6rem]">
                                                            Examinateur 1
                                                        </span>
                                                        <span class="text-sm {{ $project['first_reviewer'] === 'Non assigné' ? 'text-yellow-600 italic' : 'text-gray-900' }}">
                                                            {{ $project['first_reviewer'] }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center justify-center bg-purple-50 px-2.5 py-1.5 text-xs font-medium text-purple-700 rounded-md ring-1 ring-inset ring-purple-700/10 min-w-[6rem]">
                                                            Examinateur 2
                                                        </span>
                                                        <span class="text-sm {{ $project['second_reviewer'] === 'Non assigné' ? 'text-yellow-600 italic' : 'text-gray-900' }}">
                                                            {{ $project['second_reviewer'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
