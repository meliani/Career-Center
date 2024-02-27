<x-mail::message>
    <x-slot:emailSubject>
        {{ $emailSubject }}
        </x-slot::emailSubject>

        ## Cher(e) {{ $user->long_full_name }},

        Cher(e) étudiant (e),



        Nous vous informons que votre encadrant pour votre stage de Projet de Fin d'Études est Mme/M [nom de
        l'encadrant] .

        N'hésitez pas à contacter Mme/M.[nom de l'encadrant] pour convenir d'un premier rendez-vous et établir ensemble
        les modalités de travail.



        Cordialement,



        La DASRE
</x-mail::message>