<x-mail::message>
# Bonjour!
Vous trouverez ci-dessous la liste des soutenances  assurées aujourdhui.

<x-mail::table>
    | N° | Id | Nom étudiant | Filière | Titre du PFE | Date et heure de soutenance | Ecadrant | Examinateurs |
    |:----:|:----:|:--------------:|:-------------:|:------------------:|:---------------:|:-----------------:|:-----------------:|
    @foreach ($projects as $project)
    | {{ $loop->iteration }} | **{{ $project->id_pfe }}** | **{{ $project->students_names }}** | **{{ $project->students_programs }}** | {{ $project->title }} | {{ $project->defense_plan }} | {{ $project->academic_supervisor }} {{ $project->academic_supervisor_presence }}  | {{ $project->reviewer1 }} {{ $project->reviewer1_presence }} & {{ $project->reviewer2 }} {{ $project->reviewer2_presence }} |
    @endforeach
</x-mail::table>

Vous pouvez consulter les détails de chaque soutenance sur la plateforme Carrières INPT.

<x-mail::button :url="config('app.url')">
    Consultez les soutenances
</x-mail::button>

Cordialement,<br>
La DASRE

<x-slot:footer>
    {{__('Email sent from')}} **{{ __(config('app.name')) }}**
    {{__('at')}}
    {{now()->format('Y-m-d H:i:s')}}
</x-slot:footer>

</x-mail::message>
