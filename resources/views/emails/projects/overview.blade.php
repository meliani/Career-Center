<x-mail::message>

    Bonjour {{ $professor->long_full_name }},<br>
    Voici un aperçu de vos projets de fin d'études pour cette année jusqu'à la date d'envoi de ce courriel.
    <x-slot:emailSubject>
        {{ $emailSubject }}
        </x-slot::emailSubject>
        <x-mail::table>
            | Id PFE | Titre du PFE | Attribution |
            |:---------:|:-------------:|:----------:|
            @foreach ($projects as $project)
            | {{ $project->id_pfe }} | {{ $project->title }} | {{ $project->pivot->jury_role->getLabel() }} |
            @endforeach
        </x-mail::table>
        Vous pouvez consulter les détails de chaque projet sur la plateforme Carrières INPT.
        <x-mail::button :url="config('app.url')">
            Consultez vos projets
        </x-mail::button>
        Cordialement,<br>
        La DASRE

        <x-slot:footer>
            {{__('Email sent from')}} **{{ __(config('app.name')) }}**
            {{__('at')}}
            {{now()->format('Y-m-d H:i:s')}}
        </x-slot:footer>
</x-mail::message>