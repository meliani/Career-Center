<x-mail::message>

# Bonjour!

Vous trouverez ci-dessous la liste des soutenances assurées aujourdhui.

<x-mail::table>
    | N° | Nom étudiant | Filière [ID] | Titre du PFE | Date et heure de soutenance | Encadrant | Examinateurs |
    |:---:|:---:|:---------:|:---------:|:---------------:|:-----------------|:-------------------------------- |
    @foreach ($projects as $project)
    | **{{ $loop->iteration }}** | **{{ $project->students_names }}** | **{{ $project->students_programs }}** [**{{ $project->id_pfe }}**] | {{ $project->title }} | {{ $project->defense_plan }} | `{{ $project->academic_supervisor_presence }}{{ $project->academic_supervisor_name }}` | `{{ $project->reviewer1_presence }}{{ $project->reviewer1 }}` <br/>  `{{ $project->reviewer2_presence }}{{ $project->reviewer2 }}` |
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
