<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Form Section -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center rounded-full bg-gray-50 p-2 dark:bg-gray-800">
                        <x-heroicon-o-cog-6-tooth class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    </div>
                    <div class="grid flex-1 gap-1">
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Configuration
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            Configure the parameters for your project statistics integrity check
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content px-6 py-4">
                {{ $this->form }}
            </div>
            
            <div class="fi-section-footer px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap gap-3">
                    @foreach ($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Results Section -->
        @if($checkResults)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center rounded-full p-2 {{ $checkResults['success'] ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900' }}">
                        @if($checkResults['success'])
                            <x-heroicon-o-check-circle class="h-5 w-5 text-green-500 dark:text-green-400" />
                        @else
                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-500 dark:text-red-400" />
                        @endif
                    </div>
                    <div class="grid flex-1 gap-1">
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Integrity Check Results
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            Last run: {{ $checkResults['timestamp'] }}
                            @if($checkResults['success'])
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20 ml-2">
                                    ✓ All checks passed
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20 ml-2">
                                    ⚠ Issues found
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content px-6 py-4">
                <div class="space-y-4">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-document-text class="h-6 w-6 text-gray-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Exit Code</p>
                                    <p class="text-lg font-semibold {{ $checkResults['exit_code'] === 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $checkResults['exit_code'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-clock class="h-6 w-6 text-gray-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Execution Time</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $checkResults['timestamp'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-cog-6-tooth class="h-6 w-6 text-gray-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Options Used</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ count($checkResults['options_used'] ?? []) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options Used -->
                    @if(!empty($checkResults['options_used']))
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Command Options Used:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($checkResults['options_used'] as $option => $value)
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                                    {{ $option }}{{ $value !== true ? ': ' . $value : '' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Command Output -->
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Command Output:</h4>
                        <div class="max-h-96 overflow-y-auto">
                            <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap font-mono bg-white dark:bg-gray-900 p-3 rounded border">{{ $checkResults['output'] }}</pre>
                        </div>
                    </div>

                    @if(isset($checkResults['error']))
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700 dark:bg-red-800">
                        <h4 class="text-sm font-medium text-red-900 dark:text-red-100 mb-2">Error Details:</h4>
                        <p class="text-sm text-red-800 dark:text-red-200">{{ $checkResults['error'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Help Section -->
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid place-items-center rounded-full bg-blue-50 p-2 dark:bg-blue-900">
                        <x-heroicon-o-information-circle class="h-5 w-5 text-blue-500 dark:text-blue-400" />
                    </div>
                    <div class="grid flex-1 gap-1">
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            About This Tool
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            Information about the project statistics integrity check
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="fi-section-content px-6 py-4">
                <div class="prose prose-sm max-w-none dark:prose-invert">
                    <p>The Project Statistics Integrity Check verifies the consistency and accuracy of your project data. It performs the following checks:</p>
                    
                    <ul class="mt-4 space-y-2">
                        <li><strong>Project-Agreement Relationships:</strong> Ensures all projects have proper internship agreements</li>
                        <li><strong>Professor Assignments:</strong> Validates supervisor and reviewer assignments</li>
                        <li><strong>Department Distribution:</strong> Checks consistency between assigned departments and professor departments</li>
                        <li><strong>Mentoring Statistics:</strong> Calculates and validates mentoring averages per department</li>
                        <li><strong>Orphaned Relationships:</strong> Identifies database records that reference non-existent entities</li>
                        <li><strong>Data Consistency:</strong> Validates date ranges, department assignments, and other data integrity rules</li>
                    </ul>

                    <div class="mt-6 rounded-lg bg-amber-50 p-4 dark:bg-amber-900/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-400" />
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">Important Notes</h4>
                                <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>The "Fix Orphaned Relationships" option will attempt to automatically repair certain data inconsistencies</li>
                                        <li>Always backup your database before using the fix option in production</li>
                                        <li>Export results to keep a record of integrity check findings</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        @if($isRunning)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                <div class="flex items-center space-x-3">
                    <svg class="h-6 w-6 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg font-medium text-gray-900 dark:text-white">Running integrity check...</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Please wait while we analyze your project data.</p>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
