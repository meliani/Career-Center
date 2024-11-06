<div>
    <div class="flex justify-center py-4 bg-gray-100 dark:bg-gray-800">
        <div x-data="{ mode: 'light' }" x-on:dark-mode-toggled.window="mode = $event.detail">
            <span x-show="mode === 'light'" class="block">
                <img src="{{ asset('/svg/logo-colors.svg') }}" alt="Logo" class="h-20 mx-auto">
            </span>

            <span x-show="mode === 'dark'" class="block">
                <img src="{{ asset('/svg/logo-white.svg') }}" alt="Logo" class="h-20 mx-auto">
            </span>
        </div>
    </div>
    {{-- a big page title --}}
    @if ($submitted)
<x-filament::card class="max-w-2xl mx-auto my-12 p-8">
    <div class="space-y-6">
        <!-- Success Icon -->
        <div class="flex justify-center">
            <div class="rounded-full bg-success-100 p-3">
                <x-heroicon-o-check-circle class="w-16 h-16 text-success-500" />
            </div>
        </div>

        <!-- Success Message -->
        <div class="text-center space-y-4">
            <h2 class="text-3xl font-bold text-gray-900">
                {{ __('Internship Offer Submitted Successfully') }}
            </h2>
            <p class="text-gray-600 text-lg">
                {{ __('Your internship offer has been successfully submitted and will be reviewed by the administration.') }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-8">
            <x-filament::button
                wire:click="resetForm"
                color="success"
                class="w-full sm:w-auto text-lg py-2 px-6"
                icon="heroicon-m-plus"
            >
                {{ __('Add More') }}
            </x-filament::button>

            <x-filament::button
                tag="a"
                href="{{ route('home') }}"
                color="primary"
                class="w-full sm:w-auto text-lg py-2 px-6"
                icon="heroicon-m-home"
            >
                {{ __('Go to Home Screen') }}
            </x-filament::button>

            <x-filament::button
                tag="a"
                href="https://www.inpt.ac.ma"
                target="_blank"
                color="primary"
                class="w-full sm:w-auto text-lg py-2 px-6"
                icon="heroicon-m-arrow-top-right-on-square"
            >
                {{ __('Visit INPT Website') }}
            </x-filament::button>
        </div>
    </div>
</x-filament::card>
    @elseif ($confirming)
    <x-filament::card>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Preview Your Internship Offer') }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ __('Please review your information before submitting') }}</p>
        </div>

        <div class="space-y-8">
            <!-- Organization Information -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-building-office class="w-5 h-5 mr-2" />
                    {{ __('Organization Details') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Organization') }}:</span>
                            @if($internshipOffer->organization_name ?? false)
                                {{ $internshipOffer->organization_name }}
                            @else
                                <span class="text-warning-600 italic">{{ __('Please provide organization name') }}</span>
                            @endif
                        </p>
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Country') }}:</span>
                            {{ $internshipOffer->country ?? __('Please select a country') }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Location') }}:</span>
                            {{ $internshipOffer->internship_location ?? __('Location details missing') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Person -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-user class="w-5 h-5 mr-2" />
                    {{ __('Contact Person') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Name') }}:</span>
                            {{ $internshipOffer->responsible_name ?? __('Contact name required') }}
                        </p>
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Role') }}:</span>
                            {{ $internshipOffer->responsible_occupation ?? __('Role not specified') }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Phone') }}:</span>
                            {{ $internshipOffer->responsible_phone ?? __('Phone number missing') }}
                        </p>
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Email') }}:</span>
                            {{ $internshipOffer->responsible_email ?? __('Email address required') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Internship Details -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-academic-cap class="w-5 h-5 mr-2" />
                    {{ __('Internship Details') }}
                </h3>
                <div class="space-y-4">
                    <div class="bg-white dark:bg-gray-700 rounded p-4">
                        <h4 class="font-semibold text-lg mb-2">
                            {{ $internshipOffer->project_title ?? __('Please provide a project title') }}
                        </h4>
                        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">
                            {{ $internshipOffer->project_details ?? __('Project details need to be filled') }}
                        </p>
                        @if($internshipOffer->expertise_field_id)
                            <p class="mt-2 text-primary-600 dark:text-primary-400">
                                <span class="font-medium">{{ __('Expertise field') }}:</span>
                                {{ $internshipOffer->expertiseField->name }}
                            </p>
                        @else
                            <p class="mt-2 text-warning-600 italic">{{ __('Please select an expertise field') }}</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Duration') }}:</span>
                            {{ isset($internshipOffer->internship_duration) ? $internshipOffer->internship_duration . ' ' . __('months') : __('Duration not set') }}
                        </p>
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Positions') }}:</span>
                            {{ isset($internshipOffer->number_of_students_requested) ? $internshipOffer->number_of_students_requested . ' ' . __('student(s)') : __('Number of positions required') }}
                        </p>
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Workload') }}:</span>
                            {{ $internshipOffer->workload ?? __('Workload not specified') }}
                        </p>
                    </div>
                    @if(isset($internshipOffer->tags) && count($internshipOffer->tags))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($internshipOffer->tags as $tag)
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300 rounded-full text-sm">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-warning-600 italic">{{ __('Consider adding relevant tags') }}</p>
                    @endif
                </div>
            </div>

            <!-- Application Information -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-document-text class="w-5 h-5 mr-2" />
                    {{ __('Application Information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if ($internshipOffer->recruting_type == \App\Enums\RecrutingType::RecruiterManaged)
                        <div class="space-y-2">
                            <p class="flex items-center">
                                <span class="font-medium mr-2">{{ __('Apply via') }}:</span>
                                {{ $internshipOffer->application_link ?? $internshipOffer->application_email ?? __('Application method required') }}
                            </p>
                        </div>
                    @elseif ($internshipOffer->recruting_type == \App\Enums\RecrutingType::SchoolManaged)
                        <div class="space-y-2">
                            <p class="flex items-center">
                                <span class="font-medium mr-2">{{ __('Remuneration') }}:</span>
                                @if ($internshipOffer->remuneration)
                                    {{ $internshipOffer->remuneration . ' ' . $internshipOffer->currency?->getSymbol() }}
                                @else
                                    <span class="text-warning-600 italic">{{ __('Remuneration details missing') }}</span>
                                @endif
                            </p>
                            <p class="flex items-center">
                                <span class="font-medium mr-2">{{ __('Workload') }}:</span>
                                @if ($internshipOffer->workload)
                                    {{ $internshipOffer->workload . ' ' . __('hours per week') }}
                                @else
                                    <span class="text-warning-600 italic">{{ __('Workload details missing') }}</span>
                                @endif
                            </p>
                        </div>
                    @endif
                    <div class="space-y-2">
                        <p class="flex items-center">
                            <span class="font-medium mr-2">{{ __('Deadline') }}:</span>
                            {{ $internshipOffer->expire_at?->format('d/m/Y') ?? __('Application deadline needed') }}
                        </p>

                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-center space-x-4">
            <x-filament::button wire:click="create" color="success" class="px-6" icon="heroicon-m-check">
                {{ __('Confirm and Submit') }}
            </x-filament::button>
            <x-filament::button wire:click="$set('confirming', false)" color="warning" class="px-6" icon="heroicon-m-pencil">
                {{ __('Edit') }}
            </x-filament::button>
        </div>
    </x-filament::card>
    @else
    <x-filament::card class="bg-gradient-to-tr from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                <x-heroicon-o-briefcase class="w-8 h-8 inline-block mb-1 text-primary-600" />
                {{ __('New Internship Offer') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mt-3">
                {{ __('Fill in the details of your internship offer') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-sm">
            {{ $this->form }}
        </div>

        <div class="flex justify-center">
            <x-filament::button
                icon="heroicon-m-sparkles"
                color="primary"
                wire:click='confirm'
                class="px-8 py-3 text-lg font-semibold shadow-md transform hover:-translate-y-1 transition-transform duration-300"
            >
                {{ __('Review and Confirm') }}
            </x-filament::button>
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                {{ __('Please review all information before submitting.') }}
            </p>
        </div>
    </x-filament::card>
    @endif
</div>
