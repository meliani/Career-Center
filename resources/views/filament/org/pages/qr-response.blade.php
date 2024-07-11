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
                    <p class="text-2xl"><strong>Cette convention de stage est authentique</strong></p>
                    <div class="flex flex-col justify-center items-center">
                        <x-heroicon-o-check class="w-16 h-16 text-green-500" />
                    </div>
                    <p class="text-xl">{{ __('Full Name') }}: <strong>{{ $student->full_name }}</strong></p>
                    <p class="text-xl">{{ __('Program') }}: <strong>{{ $student->program->getDescription()
                            }}</strong></p>
                    <p class="text-xl">{{ __('Organization') }}: <strong>{{ $internship->organization->name }}</strong>
                    </p>
                    <p class="text-xl">{{ __('Internship Title') }}: <strong>{{ $internship->title }}</strong>
                    </p>
                    <p class="text-xl">{{ __('Starts At') }}: <strong>{{ $internship->starting_at->format('d/m/Y')
                            }}</strong></p>
                    <p class="text-xl">{{ __('Ends At') }}: <strong>{{ $internship->ending_at->format('d/m/Y')
                            }}</strong></p>
                </div>
                @else
                <div class="flex flex-col justify-center items-center text-red-500">
                    <div class="text-center text-xl mt-4">
                        <p><strong>Ce document n'est pas authentique</strong></p>
                        <div class="flex flex-col justify-center items-center">
                            <x-heroicon-o-x-mark class="w-16 h-16" />
                        </div>
                        <p>Il se peut que le document que vous avez soumis a subi une altération. Veuillez contacter
                            l'administration de
                            l'école.</p>
                    </div>
                </div>
                @endif
            </x-filament::card>
        </div>
    </div>
    @php
    \App\Models\LinkVerification::recordScan(request()->url(), $verification_code, $is_authentic, request()->ip(),
    request()->userAgent());
    @endphp
</x-layouts.public-layout>