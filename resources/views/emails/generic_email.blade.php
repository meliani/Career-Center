<x-mail::message>
    {{-- email header will be emailSubject --}}
    <x-slot:emailSubject>
        {{ $emailSubject }}
        </x-slot::emailSubject>
        {{-- # Bonjour {{ $user->full_name }}, --}}

        {!! $emailBody !!}

        ---
        Email sent From INPT-Entreprises platform.
</x-mail::message>