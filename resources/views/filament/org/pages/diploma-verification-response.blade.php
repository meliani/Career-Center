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
                    <p class="text-2xl"><strong>Ce Document est authentique</strong></p>
                    <div class="flex flex-col justify-center items-center">
                        <x-heroicon-o-check class="w-16 h-16 text-green-500" />
                    </div>
                    <p class="text-xl">{{ __('Full Name') }}: <strong>{{ $payload->full_name }}</strong></p>
                    <p class="text-xl">{{ __('Program') }}: <strong>{{ $payload->assigned_program
                            }}</strong></p>
                    <p class="text-xl">{{ __('Organization') }}: <strong>INPT-Rabat</strong>
                    </p>
                    <p class="text-xl">{{ __('Conseil du') }}: <strong>{{ $payload->council }}</strong>
                    </p>
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
</x-layouts.public-layout>