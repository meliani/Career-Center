<x-mail::message>
    <x-slot:emailSubject>
        {{ $emailSubject }}
        </x-slot::emailSubject>

        {!! $emailBody !!}

        <x-slot:footer>
            {{__('Email sent from')}} **{{ config('app.name') }}** {{__('by')}} **{{ $sender->long_full_name }}**
            ({{ $sender->email }})
            {{__('at')}}
            {{now()->format('Y-m-d H:i:s')}}
        </x-slot:footer>
</x-mail::message>