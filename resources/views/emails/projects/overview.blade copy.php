<x-mail::message>
    <x-slot:emailSubject>
        {{ $emailSubject }}
        </x-slot::emailSubject>
        Bonjour {{ $professor->long_full_name }},<br>

        Selon les dispositions du Règlement des Projets de Fin d’Études (PFE) adopté par le Conseil d’Établissement
        en date du 21-01-2021, nous vous adressons ci-dessous un résumé des sujets pour lesquels vous avez été
        désigné(e) Encadrant(e) interne.

        Nous tenons à vous informer qu'il est désormais possible d'accéder à la plateforme carrières via le lien suivant
        : [https://carrieres.inpt.ac.ma/]
        Vos identifiants de connexion sont les suivants :

        - Login : votre adresse e-mail INPT
        - Mot de passe : Veuillez réinitialiser votre mot de passe en cliquant sur le lien de redéfinition de MdP qui
        vous sera envoyé.

        Nous vous remercions par avance pour votre collaboration active dans la réussite de cette étape cruciale pour
        nos étudiants.
        N'hésitez pas à nous contacter si vous avez des questions

        DASRE

        <x-mail::table>
            | Id PFE | Titre du PFE | Rôle |
            |:---------:|:-------------:|:--------:|
            @foreach ($projects as $project)
            | **{{ $project->id_pfe }}** : @foreach ($project->students as $student) {{ $student->full_name }} (tél:{{
            $student->phone }}) @if (!$loop->last), @endif @endforeach | {{ $project->title }} | {{
            $project->pivot->jury_role->getLabel() }} |
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