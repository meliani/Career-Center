<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ isset($emailSubject) ? $emailSubject : '' }}
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
    <x-slot:subcopy>
        <x-mail::subcopy>
            {{ $subcopy }}
        </x-mail::subcopy>
    </x-slot:subcopy>
    @endisset

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            @isset($footer)
            {{ $footer}}
            @else
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
            @endisset
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>