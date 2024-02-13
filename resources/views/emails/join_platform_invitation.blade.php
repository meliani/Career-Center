<x-mail::message>
<x-slot:emailSubject>
{{ $emailSubject }}
</x-slot::emailSubject>

## Cher(e) {{ $user->long_full_name }},

Nous espérons que ce message vous trouve en bonne santé.

Nous avons le plaisir de vous inviter à rejoindre INPT Entreprises, la plateforme dédiée au suivi de l'évolution des stages des étudiants jusqu'au jour de leur soutenance.

Pour acceder à la plateforme, veuillez cliquer sur le lien suivant : [Accéder à la plateforme](https://carrieres.inpt.ac.ma/backend) et utiliser votre email **{{ $user->email }}** comme identifiant, pour avoir un mot de passe il suffit de cliquer sur "Mot de passe oublié".

En nous rejoignant sur la plateforme, vous contribuez à coordonner efficacement les efforts et à assurer une expérience de stage optimale.

Pour plus d'informations ou pour obtenir votre accès à la plateforme, n'hésitez pas à nous contacter.

Nous vous prions d'agréer, cher(e) Professeur(e), l'expression de nos salutations distinguées.

Cordialement,
L'équipe d'INPT Entreprises
</x-mail::message>
