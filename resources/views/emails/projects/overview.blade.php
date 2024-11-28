<x-mail::message>
<x-slot:emailSubject>
{{ $emailSubject }}
</x-slot::emailSubject>
Bonjour,<br>

Selon les dispositions du Règlement des Projets de Fin d'Études (PFE) adopté par le Conseil d'Établissement en
date du 21-01-2021, nous vous adressons ci-dessous un résumé des sujets pour lesquels vous avez été désigné(e)
Encadrant(e) interne.

Nous tenons à vous informer qu'il est désormais possible d'accéder à la plateforme carrières via le lien suivant
: [https://carrieres.inpt.ac.ma](https://carrieres.inpt.ac.ma/)

Vos identifiants de connexion sont les suivants :
- Login : {{ $professor?->email }}
- Mot de passe : Veuillez [réinitialiser](https://carrieres.inpt.ac.ma/backend/password-reset/request) votre mot de passe en cliquant sur le lien de redéfinition de MdP qui vous sera envoyé.

Nous vous remercions par avance pour votre collaboration active dans la réussite de cette étape cruciale pour nos étudiants.

N'hésitez pas à nous contacter si vous avez des questions.

DASRE
<x-mail::table>
| Id & Contact étudiant | Titre du PFE | Contact encadrant externe |
| --------- | ------------- | -------- |
@if ($projects->isEmpty())
| Aucun projet n'est disponible |
@else
@foreach ($projects as $project)
| **{{ $project->id_pfe }}** : @foreach ($project->students as $student) {{ $student->full_name }}, {{ str_replace(' ', '',$student->phone) }} @if (!$loop->last) / @endif @endforeach | {!! str_replace(["\r\n", "\n", "\r"], '<br>',$project->title) !!} | @foreach ($project->final_year_internship_agreements as $internship_agreement) **{{  $internship_agreement->encadrant_ext_name }}**: {{ $internship_agreement->encadrant_ext_mail }}, {{ str_replace(' ', '',$internship_agreement->encadrant_ext_tel) }} @if (!$loop->last) / @endif  @endforeach|
@endforeach
@endif
</x-mail::table>
<x-slot:footer>
{{__('Email sent from')}} **{{ __(config('app.name')) }}**
{{__('at')}}
{{now()->format('Y-m-d H:i:s')}}
</x-slot:footer>
</x-mail::message>
