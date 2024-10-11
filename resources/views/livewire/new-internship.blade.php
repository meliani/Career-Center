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
    <div class="flex justify-center py-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
            {{ __('Proposez un stage') }}
        </h1>
    </div>
    @if ($submitted)
    <x-filament::card class="text-center">
        <h2 class="text-2xl font-bold">{{ __('Internship Offer Submitted Successfully') }}</h2>
        <div class="mt-4 space-y-2">
            <x-filament::button wire:click="resetForm" color="primary">{{ __('Add More') }}</x-filament::button>
            <x-filament::button tag="a" href="{{ route('home') }}" color="primary">{{ __('Go to Home Screen') }}
            </x-filament::button>
            <x-filament::button tag="a" href="https://inpt.ac.ma" target="_blank" color="primary">{{ __('Visit INPT
                Website') }}</x-filament::button>
        </div>
    </x-filament::card>
    @elseif ($confirming)
    <x-filament::card class="text-center">
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><strong>{{ __('Organization name') }}:</strong> {{ $data['organization_name'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Country') }}:</strong> {{ $data['country'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Organization type') }}:</strong> {{ $data['organization_type'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Responsible name') }}:</strong> {{ $data['responsible_name'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Responsible occupation') }}:</strong> {{ $data['responsible_occupation'] ?? 'N/A' }}
                </p>
                <p><strong>{{ __('Responsible phone') }}:</strong> {{ $data['responsible_phone'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Responsible email') }}:</strong> {{ $data['responsible_email'] ?? 'N/A' }}</p>
            </div>
            <div>
                <p><strong>{{ __('Internship level') }}:</strong> {{ $data['internship_level'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Internship type') }}:</strong> {{ $data['internship_type'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Project title') }}:</strong> {{ $data['project_title'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Project details') }}:</strong> {!! nl2br(e($data['project_details'] ?? 'N/A')) !!}</p>
                <p><strong>{{ __('Tags') }}:</strong> {{ isset($data['tags']) ? implode(', ', $data['tags']) : 'N/A' }}
                </p>
                <p><strong>{{ __('Internship location') }}:</strong> {{ $data['internship_location'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Internship duration') }}:</strong> {{ $data['internship_duration'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Recruiting type') }}:</strong> {{ $data['recruting_type'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Application link') }}:</strong> {{ $data['application_link'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Application email') }}:</strong> {{ $data['application_email'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Currency') }}:</strong> {{ $data['currency'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Remuneration') }}:</strong> {{ $data['remuneration'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Workload') }}:</strong> {{ $data['workload'] ?? 'N/A' }}</p>
                <p><strong>{{ __('Application deadline') }}:</strong> {{ $data['expire_at'] ?? 'N/A' }}</p>
                {{-- <p><strong>{{ __('Attached file') }}:</strong>
                    @if(is_array($data['attached_file'] ?? null))
                    @foreach($data['attached_file'] as $file)
                    <a href="{{ $file }}" target="_blank">{{ $file }}</a><br>
                    @endforeach
                    @else
                    @if(!empty($data['attached_file']))
                    <a href="{{ $data['attached_file'] }}" target="_blank">{{ $data['attached_file'] }}</a>
                    @else
                    {{ 'N/A' }}
                    @endif
                    @endif
                </p> --}}
            </div>
        </div>
        <div class="mt-4 space-y-2">
            <x-filament::button wire:click="create" color="primary">{{ __('Confirm and Submit') }}</x-filament::button>
            <x-filament::button wire:click="$set('confirming', false)" color="primary">{{ __('Edit') }}
            </x-filament::button>
        </div>
    </x-filament::card>
    @else
    <form wire:submit.prevent="confirm">
        {{ $this->form }}
        <x-filament::button type="submit" color="primary">{{ __('Review and Confirm') }}</x-filament::button>
    </form>
    @endif
</div>