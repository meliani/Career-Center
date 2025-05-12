<x-filament::section>
    <div class="space-y-6">
        <h3 class="text-lg font-medium">{{ __('Apprenticeship Agreement Process') }}</h3>
        
        <!-- Progress Steps -->
        <div class="relative">
            <div class="grid grid-cols-{{ count($this->getSteps()) }} gap-4">
                @php
                    $steps = $this->getSteps();
                    $i = 0;
                @endphp
                
                @foreach ($steps as $key => $step)
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            @if ($step['complete'])
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-{{ $step['color'] ?? 'success' }}-500">
                                    <x-dynamic-component :component="$step['icon']" class="w-5 h-5 text-white" />
                                </div>
                            @elseif ($step['active'])
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-{{ $step['color'] ?? 'primary' }}-500 bg-{{ $step['color'] ?? 'primary' }}-50">
                                    <x-dynamic-component :component="$step['icon']" class="w-5 h-5 text-{{ $step['color'] ?? 'primary' }}-500" />
                                </div>
                            @else
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white">
                                    <x-dynamic-component :component="$step['icon']" class="w-5 h-5 text-gray-500" />
                                </div>
                            @endif
                            
                            @if ($i < count($steps) - 1)
                                <div class="absolute top-4 left-10 h-0.5 w-full {{ $step['complete'] ? 'bg-success-500' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>
                        
                        <div class="mt-3 text-center">
                            <h4 class="text-sm font-medium {{ $step['active'] ? 'text-' . ($step['color'] ?? 'primary') . '-600 font-bold' : '' }}">
                                {{ $step['label'] }}
                            </h4>
                            <p class="mt-1 text-xs text-gray-500">{{ $step['description'] }}</p>
                        </div>
                    </div>
                    
                    @php
                        $i++;
                    @endphp
                @endforeach
            </div>
        </div>
        
        <!-- Administrative Dates -->
        <div class="mt-8">
            <h4 class="text-md font-medium mb-4">{{ __('Administrative Timeline') }}</h4>
            
            <div class="grid grid-cols-4 gap-4">
                @foreach ($this->getAdminDates() as $key => $date)
                    <div class="border rounded-lg p-4 {{ $date['date'] ? 'bg-success-50 border-success-200' : 'bg-gray-50' }}">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component :component="$date['icon']" class="w-5 h-5 {{ $date['date'] ? 'text-success-500' : 'text-gray-400' }}" />
                            <span class="text-sm font-medium">{{ $date['label'] }}</span>
                        </div>
                        <div class="mt-2 text-sm {{ $date['date'] ? 'text-success-700' : 'text-gray-400' }}">
                            @if ($date['date'])
                                {{ $date['date']->format('M d, Y') }}
                            @else
                                {{ __('Pending') }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Current Status -->
        <div class="mt-6 flex items-center justify-center">
            <div class="px-4 py-2 rounded-full bg-{{ $this->getStatusColor() }}-100 text-{{ $this->getStatusColor() }}-800 flex items-center gap-2">
                <x-dynamic-component :component="$this->getStatusIcon()" class="w-5 h-5" />
                <span class="font-medium">{{ __('Current Status') }}: {{ $this->getStatus() }}</span>
            </div>
        </div>
    </div>
</x-filament::section>
