<x-mail::message>
# Introduction
{{$emailSubject}}
Bonjour {{$student->full_name}},
On vous envoi ce mail pour vous rappeler les informations de votre stage, qui commence le {{$student?->internship?->starting_at}} et se termine le {{$student?->internship?->ending_at}}.
Vous pouvez retrouver ces informations sur votre profil sur la platforme carrieres.inpt.ac.ma.
# Informations de stage
## Entreprise
{{-- {{$student->internship->organization}} --}}
## Encadrant
{{-- {{$student->internship->supervisor}} --}}
## Sujet
{{-- {{$student->internship->subject}} --}}
## Description
{{$student->internship?->description}}
## Adresse
{{$student->internship?->address}}
{{-- ## Ville
{{$student->internship->city}}
## Pays
{{$student->internship->country}}
## Téléphone
{{$student->internship->phone}}
## Email
{{$student->internship->email}}
## Site web
{{$student->internship->website}}
## Date de début
{{$student->internship->start_date}}
## Date de fin
{{$student->internship->end_date}}
## Durée
{{$student->internship->duration}}
## Rémunération
{{$student->internship->remuneration}}
## Transport
{{$student->internship->transport}}
## Hébergement
{{$student->internship->accommodation}}
## Restauration
{{$student->internship->restoration}}
## Autres avantages
{{$student->internship->other_benefits}}
## Autres informations
{{$student->internship->other_information}}
## Convention de stage
{{$student->internship->internship_agreement}}
## Attestation de stage
{{$student->internship->internship_certificate}}
## Rapport de stage
{{$student->internship->internship_report}}
## Fiche d'évaluation
{{$student->internship->internship_evaluation}}
## Fiche d'évaluation de l'encadrant
{{$student->internship->supervisor_evaluation}}
## Fiche d'évaluation de l'entreprise
{{$student->internship->company_evaluation}}
## Fiche d'évaluation de l'enseignant
{{$student->internship->teacher_evaluation}}
## Fiche d'évaluation de l'étudiant
{{$student->internship->student_evaluation}}
## Fiche d'évaluation de l'administration
{{$student->internship->administration_evaluation}} --}}

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
