<x-layouts.public-layout>
    <div
        class="{{ $is_authentic ? 'bg-green-600' : 'bg-red-300' }} flex flex-col justify-center items-center min-h-screen py-4">
        <div class="mt-8 w-full">
            <x-filament::card>
                <div class="py-4">
                    <span class="block">
                        <img src="{{ asset('/svg/logo-colors.svg') }}" alt="Logo" class="h-16 mx-auto">
                    </span>
                </div>
                @if($is_authentic)

                <div class="text-center mt-4">
                    @if($message)
                    <p class="text-2xl mb-5"><strong>{{ $message }}</strong></p>
                    @endif
                    <p class="text-xl mb-1">{{ __('Internship Title') }}: <strong>{{ $payload->title
                            }}</strong></p>
                    <p class="text-xl mb-1">{{ __('Internship Period') }}: <strong>{{ $payload->internship_period
                            }}</strong></p>
                    <p class="text-xl mb-1">{{ __('Full Name') }}: <strong>{{ $payload->student->full_name }}</strong>
                    </p>
                    <p class="text-xl mb-6">{{ __('Program') }}: <strong>{{ $payload->student->program->getDescription()
                            }}</strong></p>
                    <p class="text-xl mb-2">{{ __('Organization') }}: <strong>{{ $payload->organization->name
                            }}</strong>
                    </p>

                </div>
                @else
                <div class="flex flex-col justify-center items-center text-red-500">
                    <div class="text-center text-xl mt-4">
                        @if($message)
                        <p><strong>{{ $message }}</strong></p>
                        @endif
                        <div class="flex flex-col justify-center items-center">
                            <x-heroicon-o-x-mark class="w-16 h-16" />
                        </div>
                        <p>Please contact the administration of INPT if needed.</p>
                    </div>
                </div>
                @endif
            </x-filament::card>
        </div>
    </div>
    @php
    \App\Models\LinkVerification::recordScan(
    request()->url(),
    $verification_code,
    $is_authentic,
    request()->ip(),
    request()->userAgent()
    );
    @endphp
</x-layouts.public-layout>