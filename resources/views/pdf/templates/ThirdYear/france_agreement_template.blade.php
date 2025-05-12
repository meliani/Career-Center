<html lang="fr">

<head>
    <title>Internship Agreement</title>
    @include('pdf.components.stylesheet')
</head>

<body class="text-justify text-xs sm:text-sm md:text-base lg:text-lg">
    @include('pdf.components.header')
    
    @include('pdf.components.title-box', [
        'mainTitle' => 'CONVENTION DE STAGE'
    ])

    <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 1 : Parties signataires de la convention</h2>
    <div class="text-xs font-semibold mt-2">
        La présente convention règle les rapports entre : <strong>{{$internship->organization->name}}</strong>
        - {{$internship->organization->address}}
        - {{$internship->organization->city}},
        {{$internship->organization->country}}
    </div>
    <div class="text-xs">
        ci-après désignée par Entreprise, et représentée par <strong>{{$internship->parrain->formal_name}}</strong>,
        <strong>{{$internship->parrain->function}}</strong>
    </div>
    <div class="text-xs font-semibold mt-0 mb-2">
        Et
    </div>
    <div class="text-xs">
        L’Institut National des Postes et Télécommunications (INPT) représenté par <strong>Monsieur Ahmed KENSI,
            Directeur Adjoint des
            Stages et des Relations avec les Entreprises</strong> par intérim,
    </div>
    <div class="text-xs font-semibold mt-0 mb-2">
        Et désigné dans ce qui suit par l’INPT
    </div>

    <div class="text-xs mb-2">
        <p>Concernant le stage de Fin d’études de
            <strong>{{$internship->student->formal_name}}</strong>, élève ingénieur de la filière
            <strong>
                @if(is_a($internship->student->program, \App\Enums\Program::class))
                {{$internship->student->program->getDescription()}}
                @else
                {{$internship->student->program}}
                @endif
            </strong>.
        </p>


    </div>

    {{-- <div class="p-6 bg-white"> --}}
        <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 2 : Contenu et objectif du stage « Projet de Fin d’Etudes » (PFE) </h2>
        <div class="text-xs mt-2">
        Conformément au règlement intérieur de l’Institut National des Postes et Télécommunications (INPT), l’élève ingénieur est appelé à effectuer un stage de PFE obligatoire pour l’obtention du diplôme d'ingénieur en télécommunications. L'objectif poursuivi du stage de PFE est de donner à chaque étudiant l'occasion d'effectuer une recherche personnelle et approfondie sur un sujet proposé par une entreprise afin de s'immerger dans le monde du travail. Il portera sur le sujet suivant :
            <strong>{{$internship->title}}</strong>
        </div>
        <p class="text-xs mt-1"><strong>Contenu détaillé du stage</strong></p>
        <div class="text-xs mt-0">
            {{$internship->description}}
        </div>

        <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 3 : Modalités du stage</h2>
        <div class="text-xs mt-2">
            <p>
                <strong>Période de stage</strong>: Le stage aura lieu du <strong>{{$internship->starting_at->format('d/m/Y')}}</strong> au <strong>{{$internship->ending_at->format('d/m/Y')}}</strong>.
                </p>
<p>Un avenant à la convention pourra éventuellement être établi en cas de prolongation de stage faite à la demande de l'entreprise et de l'étudiant stagiaire. Toutes prolongations seront soumises aux obligations du programme concerné.</p>
<p class="text-xs mt-1"><strong>Déroulement du stage</strong></p>
<p>La durée hebdomadaire maximale de présence du stagiaire dans l'entreprise sera de <strong>{{ $internship->workload }} heures/semaine</strong>.</p>
            </p>
        </div>
        <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 4 : Statut du stagiaire - Encadrement</h2>
        <div class="text-xs mt-2">
            <p>
                Pendant la durée de son stage, le stagiaire reste placé sous la responsabilité de l’entreprise d’accueil tout en demeurant étudiant de l’INPT. L'élève stagiaire pourra revenir à l'Ecole pendant la durée du stage, pour y suivre certains cours demandés explicitement par le programme,  participer à des réunions.. ; Le cas échéant, les dates seront portées à la connaissance de l'Entreprise par l'Ecole.
            </p>
            <p>
            Le règlement de l’INPT prévoit l'encadrement du stagiaire au cours de sa période de stage en entreprise. Cet encadrement doit être assuré par un enseignant de l’INPT et par un membre de l'entreprise chargé d'accueillir et d'accompagner le stagiaire.
            </p>
            <p class="text-xs mt-1"><strong>L’encadrement sera assuré par:</strong></p>
            <p><strong>Maître de stage :</strong> <strong>{{$internship->externalSupervisor->formal_name}}</strong>, {{$internship->externalSupervisor->phone}}, {{$internship->externalSupervisor->email}}</p>
            <p><strong>Tuteur pédagogique / Conseiller de stage :</strong> <strong>{{$internship->suggestedInternalSupervisor->formal_name}}</strong>, {{$internship->suggestedInternalSupervisor->email}}</p>
            <p class="text-xs mt-1">Lieu du stage (adresse précise, si différente de l’adresse de l’entreprise indiquée ci dessus): {{$internship->office_location}}</p>
        </div>


        @include('pdf.components.page_break')

        {{-- <div class="flex flex-col justify-between min-h-screen"> --}}
            <div>
        @include('pdf.components.header')

            </div>
            <div class="flex flex-col justify-start items-center">

                <div class="py-6 bg-white">
                    <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 5 : Discipline</h2>
                    <div class="text-xs mt-2 mb-2">
                        <p>Durant son stage, l'étudiant stagiaire est soumis à la discipline et au règlement intérieur de l'Entreprise, notamment en ce qui concerne les horaires, la réglementation du travail, les règles d’hygiène et de sécurité en vigueur dans l’entreprise.
                        </p>
                        <p>Toute sanction disciplinaire ne peut être décidée que par l’école. Dans ce cas, l’entreprise informe l’école des manquements et lui fournit éventuellement les éléments constitutifs.
                        </p>
                        <p>En cas de manquement à la discipline, l’entreprise, en accord avec le Directeur de l'INPT, peut mettre fin au stage de l'étudiant stagiaire, tout en respectant les dispositions fixées à l’article 9 de la présente convention.
                        </p>
                    </div>
                    <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 6 : Gratification – Avantages en nature - Remboursement de frais</h2>
                    <div class="text-xs mt-2 mb-2">
                        <p>L’étudiant stagiaire ne perçoit aucune rémunération. Toutefois il peut lui être alloué une gratification.
Lorsque la durée du stage est supérieure à trois mois consécutifs, celui-ci fait l’objet d’une gratification
Cette dernière est fixée à <strong>{{ $internship->remuneration }} {{ $internship->currency->getSymbol() }}</strong> par mois.
                        </p>
                        <p><strong>Modalités de versement de la gratification</strong> : virement</p>
                        <p>Si le stagiaire bénéficie d’avantages en nature (gratuité des repas par exemple), le montant représentant la valeur de ces avantages sera ajouté au montant de la gratification  mensuelle avant comparaison au produit de 12.5% du plafond horaire de la sécurité sociale par le nombre d’heures de stage effectuées au cours du mois considéré.
                        </p>
                        <p>Les frais de déplacement, d'hébergement et de restauration engagés par l’étudiant stagiaire à la demande de l'Entreprise, ainsi que les frais de formation éventuellement nécessités par le stage, seront intégralement pris en charge par celle-ci selon les modalités en vigueur dans l’entreprise.
                        </p>
<h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 7 : Protection sociale</h2>
<div class="text-xs mt-2 mb-2">
    <p>Le stagiaire est couvert par l'assurance de l'INPT contre les accidents pouvant survenir au cours du stage dans la limite de garantie de son assurance. L'élève doit contracter lui-même une assurance le couvrant à l'extérieur de l'INPT durant toute la durée de son stage.</p>
    <p>Pendant la durée du stage et sous réserve des dispositions de l'article 7.2 de la présente convention, l'étudiant stagiaire continue à percevoir les prestations du régime social étudiant.</p>
    <p>Quel que soit le montant de la gratification versée, l'étudiant stagiaire conserve son statut d'étudiant ; il ne compte pas dans les effectifs salariés de l'Entreprise et ne peut prétendre bénéficier des avantages particuliers valables pour le personnel de l'Entreprise.</p>

    <p class="text-xs mt-2"><strong>7.1 : En cas de gratification inférieure ou égale à 15% du plafond horaire de la sécurité sociale</strong></p>
    <p>(soit 554.40 euros en 2016 pour une durée légale de travail hebdomadaire de 35 heures) avantages en nature inclus:</p>
    <p>Dans ce cas, conformément à la législation en vigueur, la gratification de stage n'est pas soumise à cotisation sociale.</p>
    <p>L'étudiant stagiaire continue à bénéficier de la législation sur les accidents du travail au titre de l'article L 412-8-2 du code de la Sécurité Sociale, régime étudiant.</p>
    <p>En cas d'accident survenant à l'étudiant stagiaire, soit au cours des travaux dans l'Entreprise, soit au cours du trajet, soit sur des lieux rendus utiles pour les besoins de son stage, l'Entreprise s'engage à faire parvenir sous 48 heures toutes les informations utiles à l'Ecole pour que cette dernière puisse établir la déclaration d'accident.</p>

    <p class="text-xs mt-2"><strong>7.2 : En cas de gratification supérieure à 15% du plafond horaire de la sécurité sociale</strong></p>
    <p>(soit 554.40 euros en 2016 pour une durée légale de travail hebdomadaire de 35 heures) :</p>
    <p>Les sommes versées prennent alors le caractère d'une rémunération.</p>
    <p>Les cotisations sociales sont calculées sur le différentiel entre le montant de la gratification et 15% du plafond horaire de la sécurité sociale multipliée par le nombre d'heure de stage effectué dans le mois.</p>
    <p>L'élève stagiaire bénéficie de la couverture légale en application des dispositions des articles L 411-1 et suivants du code de la Sécurité Sociale. En cas d'accident survenant à l'étudiant stagiaire, soit au cours des travaux dans l'Entreprise, soit au cours du trajet, soit sur des lieux rendus utiles pour les besoins de son stage, l'Entreprise effectue toutes les démarches nécessaires auprès de la Caisse Primaire d'Assurance Maladie et informe l'Ecole dans les meilleurs délais.</p>

</div>
        @include('pdf.components.page_break')
            <div>
        @include('pdf.components.header')


    <p class="text-xs mt-2"><strong>7.3 : Déplacements</strong></p>
    <p>En cas de déplacement, il appartient à l'entreprise d'établir, dans tous les cas, un descriptif nominatif de la nature du déplacement et d'en informer l'Ecole.</p>
    <p>De plus, en cas de déplacements à l'étranger, ceux-ci doivent impérativement être signalés par écrit à l'école au moins quinze jours avant la date prévue de départ. L'école doit signaler ces déplacements à la sécurité sociale.</p>
    <p>Lorsque ces conditions ne sont pas remplies, l'entreprise s'engage à cotiser pour la protection de l'élève stagiaire et à faire les déclarations nécessaires en cas d'accident du travail.</p>
        <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 8 : Responsabilité civile et assurances</h2>
        <div class="text-xs mt-2 mb-2">
            <p>Chacune des trois parties (entreprise, école, élève stagiaire) déclare être garantie au titre de la responsabilité civile.</p>
            <p>Lorsque l'Entreprise met un véhicule à la disposition du stagiaire, il lui incombe de vérifier préalablement que la police d'assurance du véhicule couvre son utilisation par un élève stagiaire.</p>
            <p>Lorsque, dans le cadre de son stage, l'étudiant stagiaire utilise son propre véhicule ou un véhicule prêté par un tiers, il déclare expressément à l'assureur dudit véhicule cette utilisation qu'il est amené à faire et le cas échéant s'acquitte de la prime y afférente.</p>
        </div>
<h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 9 : Absence et Interruption du stage</h2>
<div class="text-xs mt-2 mb-2">
    <p><strong>9.1 : Interruption temporaire</strong></p>
    <p>Toute absence devra être signalée par l'Entreprise à l'établissement.</p>
    <p>Dans le cas d'une interruption, d'une semaine au moins, pour motif circonstancié ou contexte exceptionnel, autorisée par l'entreprise, un avenant à la présente convention devra être signé au préalable par les cocontractants.</p>

    <p><strong>9.2 : Interruption définitive</strong></p>
    <p>En cas de volonté d'une des trois parties (entreprise, école, étudiant(e)) d'interrompre définitivement le stage, celle-ci devra immédiatement en informer les deux autres parties par écrit. Les raisons invoquées seront examinées en étroite concertation. La décision définitive d'interruption du stage ne sera prise qu'à l'issue de cette phase de concertation.</p>
</div>

<h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 10 : Fin du stage – Rapport – Soutenance - Evaluation</h2>
<div class="text-xs mt-2 mb-2">
    <p>A l'issue du stage, l'Entreprise délivre au stagiaire une attestation de stage et remplit une fiche d'évaluation qu'elle retourne à l'Ecole. De son coté, l'étudiant est tenu de présenter les résultats de son travail, tant par écrit dans son mémoire de fin d'études, qu'oralement lors de sa soutenance devant un jury comprenant des responsables de l'entreprise accueillante ou autres et aussi des enseignants de l'INPT.</p>

    <p><strong>10.1 : Soutenance de stage</strong></p>
    <p>Le stagiaire est autorisé à faire une présentation de son travail devant un jury de stage. La présentation et le rapport devront avoir été validés par l'entreprise au préalable et par l'enseignant encadrant à l'INPT.</p>
    <p>L'étudiant s'engage à fournir au terme de son stage un rapport (dernière version) à l'organisme d'accueil et à l'INPT au maximum une semaine avant la date de la soutenance.</p>
    <p>La semaine des soutenances du Projet de Fin d'Etudes (PFE) se déroule à l'INPT la deuxième quinzaine du mois de Juin.</p>

    <p><strong>10.2 : Evaluation du stage</strong></p>
    <p>À l'issue de chaque stage, une évaluation "à chaud" est faite. Une synthèse d'évaluation pour chaque stagiaire est adressée par les encadrants.</p>
    <p>A la fin du stage et avant la soutenance, le Parrain de l'organisme d'accueil (ou son délégué) est prié d'établir une évaluation du comportement du stagiaire et de la qualité du travail effectué. Cette lettre sera envoyée par courrier sous enveloppe cachetée à l'INPT à l'adresse suivante : INPT. Direction Adjointe des Stages et Relations Entreprises. Service des Stages. Avenue Allal Al Fassi. Madinat Al Irfane Rabat. Maroc</p>
</div>

<h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 11 : Devoir de réserve et confidentialité</h2>
<div class="text-xs mt-2 mb-2">
    <p>Le devoir de réserve est de rigueur absolue. Les étudiants stagiaires prennent donc l'engagement de n'utiliser en aucun cas les informations recueillies ou obtenues par eux pour en faire l'objet de publication, communication à des tiers sans accord préalable de la Direction de l'Entreprise, y compris le rapport de stage. Cet engagement vaudra non seulement pour la durée du stage mais également après son expiration. L'étudiant s'engage à ne conserver, emporter, ou prendre copie d'aucun document ou logiciel, de quelque nature que ce soit, appartenant à l'Entreprise, sauf accord de cette dernière.</p>
    <p>Nota : Dans le cadre de la confidentialité des informations contenues dans le rapport, l'Entreprise peut demander une restriction de la diffusion du rapport, voire le retrait de certains éléments très confidentiels. Les personnes amenées à en connaître sont contraintes par le secret professionnel à n'utiliser ni ne divulguer les informations du rapport.</p>
</div>
        @include('pdf.components.signature_boxes')
        @include('pdf.components.qr_verification')
        @include('pdf.components.footer')
</body>

</html>
