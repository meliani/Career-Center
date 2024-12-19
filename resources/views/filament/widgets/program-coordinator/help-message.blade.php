<div class="rounded-xl bg-gradient-to-br from-primary-50/90 to-primary-50 dark:from-primary-900/10 dark:to-primary-800/5 p-4 mb-4 border border-primary-100 dark:border-primary-800/20 backdrop-blur-sm">
    <button 
        type="button" 
        class="w-full focus:outline-none"
        x-data="{ expanded: false }"
        @click="expanded = !expanded"
    >
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <x-heroicon-o-information-circle 
                    class="h-5 w-5 text-primary-500 dark:text-primary-400 animate-pulse" 
                />
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-primary-700 dark:text-primary-300">
                        {{ __('Quick Guide') }}
                    </h3>
                    <x-heroicon-s-chevron-down 
                        class="h-4 w-4 text-primary-400 transition-all duration-200 ease-out"
                        x-bind:class="{ '-rotate-180': expanded }"
                    />
                </div>
                
                <div 
                    class="mt-3 text-sm text-primary-600 dark:text-primary-400 overflow-hidden"
                    x-show="expanded"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    x-cloak
                >
                    <ul class="space-y-2 pl-4 border-l-2 border-primary-100 dark:border-primary-700/30">
                        <li class="relative pl-2 flex items-start gap-2 before:absolute before:w-1.5 before:h-1.5 before:bg-primary-400 before:rounded-full before:left-0 before:top-2 before:-translate-x-[4.5px]">
                            <span class="text-primary-400 inline-block w-4">•</span>
                            {{ __('Click on the stats cards above to filter agreements') }}
                        </li>
                        <li class="relative pl-2 flex items-start gap-2 before:absolute before:w-1.5 before:h-1.5 before:bg-primary-400 before:rounded-full before:left-0 before:top-2 before:-translate-x-[4.5px]">
                            <span class="text-primary-400 inline-block w-4">•</span>
                            {{ __('Assign departments using the dropdown in each row') }}
                        </li>
                        <li class="relative pl-2 flex items-start gap-2 before:absolute before:w-1.5 before:h-1.5 before:bg-primary-400 before:rounded-full before:left-0 before:top-2 before:-translate-x-[4.5px]">
                            <span class="text-primary-400 inline-block w-4">•</span>
                            <span class="flex items-center gap-1.5">
                                {{ __('Click on Preview') }}
                                <x-heroicon-o-magnifying-glass class="h-4 w-4 text-primary-400" />
                                {{ __('or the agreement to view details') }}
                            </span>
                        </li>

                        <li class="relative pl-2 flex items-start gap-2 before:absolute before:w-1.5 before:h-1.5 before:bg-primary-400 before:rounded-full before:left-0 before:top-2 before:-translate-x-[4.5px]">
                            <span class="text-primary-400 inline-block w-4">•</span>
                            <span class="flex items center gap-1.5">
                                {{ __('You can download all your projects details in the projects section') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </button>
</div>
