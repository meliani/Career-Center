<x-mail::message>
<x-slot:emailSubject>
{{ $emailSubject }}
</x-slot::emailSubject>

## Cher(e) {{ $user->long_full_name }},


Bonjour Mme/M  [Nom du Professeur] 


Nous vous informons que [Nom de l'étudiant] de la Filière [Nom filière t] sera placé sous votre encadrement pour son stage PFE.



Cordialement,



La DASRE
</x-mail::message>
