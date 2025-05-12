<html lang="fr">

<head>
    <title>Internship Agreement</title>
    @include('pdf.components.stylesheet')
</head>

<body class="text-justify text-xs sm:text-sm md:text-base lg:text-lg">
    @include('pdf.components.header')
    
    @include('pdf.components.title-box', [
        'mainTitle' => 'CONVENTION DE STAGE
PROJET DE FIN D\'ETUDES'
    ])

    <h2 class="text-base font-semibold mt-2 mb-0">Préambule :</h2>
    <div class="text-xs">
        Le présent stage de Projet de Fin d'Études (PFE) vise à doter l'élève ingénieur des
        compétences et aptitudes
        nécessaires
        à son intégration dans un environnement professionnel, en ce compris, sans s'y limiter, les éléments suivants :
        <ul class="list-disc list-inside ml-4 mt-2 mb-2 space-y-0">
            <li>L'aptitude à s'intégrer dans un environnement professionnel, en observant les codes et usages en
                vigueur;</li>
            <li>Le développement du sens du contact, favorisant les échanges et la collaboration avec les différents
                acteurs
                du milieu
                professionnel;</li>
            <li>L'acquisition d'une première expérience professionnelle significative;</li>
            <li>La capacité à organiser et gérer efficacement son temps en vue d'achever les travaux qui lui sont
                confiés dans
                les
                délais impartis;</li>
            <li>Le travail en équipe, dans une logique de collaboration et d'échange de savoir-faire;</li>
            <li>Le développement de la créativité et de l'innovation dans la conduite de projets;</li>
            <li>La rédaction d'un mémoire de qualité, conforme aux standards académiques et professionnels;</li>
            <li>La présentation des résultats obtenus devant un jury compétent, en vue de leur évaluation finale.</li>
        </ul>
        Les obligations et engagements découlant de ce stage s'inscrivent dans le cadre des exigences de la formation
        dispensée,
        ainsi que des normes professionnelles en vigueur.
    </div>
    <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 1 : Les soussignés</h2>
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
        L'Institut National des Postes et Télécommunications (INPT) représenté par <strong>Monsieur Ahmed KENSI,
            Directeur Adjoint des
            Stages et des Relations avec les Entreprises</strong> par intérim,
    </div>
    <div class="text-xs font-semibold mt-0 mb-2">
        Et désigné dans ce qui suit par l'INPT
    </div>

    <div class="text-xs mb-2">
        <p>Concernant le stage de Fin d'études de
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

    <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 2 : Durée et sujet du stage</h2>
    <div class="text-xs mt-2">
        Le stage commencera le <strong>{{$internship->starting_at->format('d/m/Y')}}</strong>
        et prendra fin le <strong>{{$internship->ending_at->format('d/m/Y')}}</strong>,
        sous la responsabilité de :
    </div>
    <div class="text-xs px-6">
        <ul class="list-disc list-inside ml-4 mt-2 mb-2 space-y-0">
            <li>
                Encadrant Externe :
                <strong>{{$internship->externalSupervisor->formal_name}}</strong>,
                {{$internship->externalSupervisor->function}}, {{$internship->externalSupervisor->phone}},
                {{$internship->externalSupervisor->email}}
            </li>
        </ul>
        <p class="mt-1">
            et
        </p>
        <ul class="list-disc list-inside ml-4 mt-2 mb-2 space-y-0">
            <li>
                Coordonnateur de la filière :
                <strong>{{$internship->student?->getProgramCoordinator()->formal_name}}</strong>
            </li>
        </ul>
    </div>
    <p class="mt-2">
        Le stage portera sur le sujet suivant :
        <strong>{{$internship->title}}</strong>
    </p>
    @php
    $wordCount = str_word_count(strip_tags($internship->description));
    @endphp
    <p class="mt-2">
    Descriptif détaillé :
    </p>
    <span class="mt-2" style="{{ $wordCount > 100 ? 'font-size:0.6rem;word-wrap:break-word;' : '' }}">
     <strong>{{ $internship->description }}</strong>
    </span>
    <p class="mt-2">
        Adresse du stage (adresse précise, si différente de l'adresse de l'entreprise indiquée ci dessus) :
    </p>
    <strong>{{$internship->office_location}}</strong>

    @include('pdf.components.page_break')

    <div class="flex flex-col justify-start items-center">
        <div class="py-6 bg-white">
            <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 3 : Protection des données à caractère
                personnel</h2>
            <div class="text-xs mt-2 mb-2">
                <p>Dans le cadre du stage, le stagiaire pourra être amené à accéder, manipuler ou traiter des
                    données à
                    caractère personnel
                    conformément aux missions qui lui sont confiées. À ce titre, les parties s'engagent à
                    respecter les
                    dispositions de la
                    loi n° 09-08 relative à la protection des personnes physiques à l'égard du traitement des
                    données à
                    caractère personnel,
                    ainsi que celles du Règlement Général sur la Protection des Données (RGPD) lorsque
                    applicable.
                </p>
            </div>
            <h2 class="text-base font-semibold mt-2 mb-0">ARTICLE 4 : Engagement</h2>
            <div class="text-xs mt-2 mb-2">
                <p>La présente convention garantit que la charte des stages inscrite au verso a été portée à la
                    connaissance
                    de
                    l'entreprise et de l'élève et que ceux-ci en ont approuvé expressément toutes les clauses.
                </p>
                <p class="mt-8 mb-8">
                    <strong>Document établi en quatre exemplaires</strong>
                </p>
            </div>
            
            @include('pdf.components.signature_boxes')
            @include('pdf.components.qr_verification')
        </div>
    </div>

    {{-- second page --}}
    @include('pdf.components.page_break')

    <div class="flex flex-col justify-start items-center">
        <div class="py-6 bg-white">
            <div class="mt-2">
                <h2 class="text-center text-lg font-semibold mb-0">CHARTE DES STAGES EN ENTREPRISE</h2>
                <h3 class="text-sm font-semibold mb-0 inline">Art.1</h3>
                <p class="text-xs inline">
                    L'élève ingénieur est appelé à eﬀectuer un stage de PFE obligatoire pour l'obtention du
                    diplôme
                    d'Ingénieur en
                    Télécommunications et Technologies
                    de l'Information. L'objectif poursuivi du stage de PFE est de donner à chaque étudiant
                    l'occasion
                    d'eﬀectuer une
                    recherche personnelle et approfondie sur
                    un sujet proposé par une entreprise afin de s'immerger dans le monde du travail.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.2</h3>
                <p class="text-xs mb-2 inline">
                    Pendant la durée de son stage, le stagiaire reste placé sous la responsabilité de
                    l'entreprise
                    d'accueil
                    tout en
                    demeurant étudiant de l'INPT. L'élève
                    stagiaire pourra revenir à l'Institut pendant la durée du stage, pour y suivre certains
                    cours
                    demandés
                    explicitement par
                    le programme, participer à des
                    réunions; Le cas échéant, les dates seront portées à la connaissance de l'Entreprise par
                    l'Etablissement.
                </p>
                <p class="text-xs mt-1">
                    Le règlement de l'INPT prévoit l'encadrement du stagiaire au cours de sa période de stage en
                    entreprise.
                    Cet encadrement
                    doit être assuré par un
                    enseignant de l'INPT et par un membre de l'entreprise chargé d'accueillir et d'accompagner
                    le
                    stagiaire.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.3</h3>
                <p class="text-xs inline">
                    Durant son stage, l'étudiant stagiaire est soumis à la discipline et au règlement intérieur
                    de
                    l'Entreprise, notamment
                    en ce qui concerne les horaires,
                    <strong>la réglementation du travail, les règles d'hygiène et de sécurité en vigueur dans
                        l'entreprise.</strong>
                </p>
                <p class="text-xs mt-1">
                    Toute sanction disciplinaire ne peut être décidée que par l'Institut. Dans ce cas,
                    l'entreprise
                    informe l'Institut des
                    manquements et lui fournit éventuellement
                    les éléments constitutifs. L'entreprise, en accord avec le Directeur de l'INPT, peut mettre
                    ﬁn
                    au
                    stage du stagiaire,
                    tout en respectant les dispositions ﬁxées
                    à l'article 4 ci-après.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.4</h3>
                <p class="text-xs inline">
                    Toute absence devra être signalée par l'Entreprise à l'établissement.
                </p>
                <p class="text-xs mt-1">
                    Dans le cas d'une interruption, d'une semaine au moins, pour motif circonstancié ou contexte
                    exceptionnel, autorisée par
                    l'entreprise, un avenant à la
                    présente convention devra être signé au préalable par les cocontractants.
                </p>
                <p class="text-xs mt-1">
                    En cas de volonté d'une des trois parties (entreprise, INPT, étudiant(e)) d'interrompre
                    définitivement
                    le stage,
                    <strong>celle-ci devra immédiatement en informer
                        les deux autres parties par écrit. Les raisons invoquées seront examinées en étroite
                        concertation.</strong>
                    La
                    décision déﬁnitive
                    d'interruption du stage ne
                    sera prise qu'à l'issue de cette phase de concertation.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.5</h3>
                <p class="text-xs inline">
                    Le stagiaire est couvert par l'assurance de l'INPT contre les accidents pouvant survenir au
                    cours du
                    stage dans la limite de garantie de son
                    assurance.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.6</h3>
                <p class="text-xs inline">
                    A l'issue du stage, l'étudiant est tenu de présenter les résultats de son travail, tant par
                    écrit
                    dans
                    son mémoire de ﬁn
                    d'études, qu'oralement lors de
                    sa soutenance devant un jury comprenant des représentants de l'entreprise accueillante et
                    des
                    enseignants de l'INPT. La
                    présentation et le rapport
                    devront avoir été validés par l'entreprise au préalable et par l'enseignant encadrant à
                    l'INPT.
                    L'étudiant s'engage à
                    fournir un rapport à l'organisme
                    d'accueil et à l'INPT au maximum une semaine avant la date de la soutenance.
                </p>
                <p class="text-xs mt-1">
                    A la ﬁn du stage et avant la soutenance, le Directeur de stage mentionné dans la convention
                    est
                    prié
                    de
                    communiquer à
                    l'école une évaluation du
                    comportement du stagiaire et de la qualité du travail effectué.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.7</h3>
                <p class="text-xs inline">
                    Les étudiants stagiaires prennent l'engagement de n'utiliser en aucun cas les informations
                    recueillies
                    ou obtenues par
                    eux pour en faire l'objet de
                    publication, communication à des tiers sans accord préalable de la Direction de
                    l'Entreprise, y
                    compris
                    le rapport de
                    stage. Cet engagement vaudra non
                    seulement pour la durée du stage mais également après son expiration. L'étudiant s'engage à
                    ne
                    conserver, emporter, ou
                    prendre copie d'aucun
                    document ou logiciel, de quelque nature que ce soit, appartenant à l'Entreprise, sauf accord
                    de
                    cette
                    dernière.
                    L'Entreprise peut demander une restriction
                    de la diﬀusion du rapport, voire le retrait de certains éléments très conﬁdentiels. Les
                    personnes
                    amenées à en connaître
                    sont contraintes par le secret
                    professionnel à n'utiliser ni ne divulguer les informations du rapport.
                </p>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-semibold mb-0 inline">Art.8</h3>
                <p class="text-xs inline">
                    S'il advenait qu'un contrat de travail prenant effet avant la date de fin du stage soit
                    signé
                    avec
                    l'Entreprise, la présente convention deviendrait
                    caduque; l'étudiant stagiaire perdrait son statut d'étudiant et ne relèverait plus de la
                    responsabilité
                    de l'Ecole. Ce dernier devrait impérativement en être
                    averti avant signature du contrat.
                </p>
            </div>
        </div>
    </div>
    
    @include('pdf.components.footer')
</body>

</html>