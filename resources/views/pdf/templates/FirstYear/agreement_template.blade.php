<html lang="fr">

<head>
    <title>Internship Agreement</title>
    @include('pdf.components.stylesheet')
</head>

<body class="text-justify">
    @include('pdf.components.header')

    @include('pdf.components.title-box', [
        'mainTitle' => 'CONVENTION DE STAGE "OUVRIER"',
        'subtitle' => '1<sup>ère</sup> Année du cycle Ingénieur (INE) de l\'Institut National des Postes et Télécommunications (INPT-Rabat)'
    ])

    <div class="text-xs font-semibold">
        Entre : L'Institut National des Postes et Télécommunications (INPT-Rabat)
    </div>
    <div class="text-xs">
        Sis à Avenue Allal El Fassi, Madinat Al Irfane, Rabat - Maroc
    </div>
    <div class="text-xs">
        Représenté par Monsieur Ahmed Kensi,
        Directeur Adjoint des Stages et Relations avec les Entreprises,
    </div>
    <div class="text-xs font-semibold mt-0">
        Et désigné dans ce qui suit par l'INPT
    </div>
    <div class="text-xs font-semibold mt-2">
        Et : <strong>{{$internship->organization->name}}</strong>
    </div>
    <div class="text-xs">
        Sise à l'addresse : <strong>{{$internship->organization->address}}, {{$internship->organization->city}},
            {{$internship->organization->country}}</strong>
    </div>
    <div class="text-xs">
        Représenté par <strong>{{$internship->parrain->full_name}}</strong>,
        <strong>{{$internship->parrain->function}}</strong>
    </div>
    <div class="text-xs font-semibold mt-0">
        Et désigné dans ce qui suit par l'organisme d'accueil
    </div>

    <div class="p-6 bg-white">
        <h1 class="text-base font-semibold mb-0">Préambule</h1>
        <div class="text-xs mb-2">
            <p>L'INPT a pour mission la formation, la recherche et l'expertise. Il est chargé de la formation initiale
                et de la
                formation continue dans les domaines des télécommunications, des technologies de l'information et de la
                communication et
                disciplines connexes.</p>
            <p>La mise en situation professionnelle est une étape importante dans la formation d'ingénieurs, elle permet
                aux élèves de se
                confronter aux réalités techniques, scientifiques, économiques et sociales. C'est dans cette optique que
                l'INPT s'inscrit et marque sa
                volonté d'avoir dans ses programmes de formation, des stages de divers types orientés vers des objectifs
                globaux et spécifiques
                (Stage Ouvrier, Stage Technique et Stage de Projet de Fin d'Études).</p>
            <p>Durant le stage ouvrier dont la durée est de 4 semaines ou plus, l'élève ingénieur occupe généralement un
                poste où il se familiarise
                avec les conditions de travail et observe le fonctionnement de l'organisme d'accueil.</p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 1</h2>
        <div class="text-xs mb-2">
            <p>La présente convention règle les rapports entre l'organisme d'accueil d'une part, l'INPT et le stagiaire
                d'autre part.</p>
            <p>Cette convention concerne
                <strong>{{$internship->student->long_full_name}}</strong>, élève ingénieur en 1<sup>ère</sup> année du
                cycle INE
                de l'INPT, inscrit(e) en filière <strong>
                    @if(is_a($internship->student->program, \App\Enums\Program::class))
                    {{$internship->student->program->getDescription()}}
                    @else
                    {{$internship->student->program}}
                    @endif
                </strong>.
            </p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 2</h2>
        <div class="text-xs mb-2">
            <p>L'étudiant(e) sera encadré(e) par un Responsable de stage désigné par l'organisme d'accueil, en
                l'occurence
                <strong>{{$internship->supervisor->full_name}}</strong>
            </p>
            <p>Le thème du stage est établi d'un commun accord entre l'organisme d'accueil et l'élève ingénieur.</p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 3</h2>
        <div class="text-xs mb-2">
            <p>
            <p>Thème du stage : <strong>{{$internship->title}}</strong></p>
            <p>
                La durée du stage est fixée à <strong>{{$internship->duration_in_weeks}} semaines</strong>, du
                @if($internship->starting_at instanceof \Carbon\Carbon)
                <strong>{{$internship->starting_at->format('d/m/Y')}}</strong>
                au
                <strong>{{$internship->ending_at->format('d/m/Y')}}</strong>
                .
                @else
                ..................... au .....................
                @endif
            </p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 4</h2>
        <p class="text-xs mb-2">
            Durant son stage l'élève ingénieur est soumis à la discipline de l'organisme d'accueil, notamment en ce qui
            concerne l'horaire et le respect du secret professionnel.
        </p>
    </div>

    {{-- second page --}}
    @include('pdf.components.page_break')
    
    <div class="p-6 bg-white">
        <h2 class="text-base font-semibold mb-0">Article 5</h2>
        <p class="text-xs mb-2">
            En cas de faute grave, de manquement à la discipline ou de tout autre problème, l'organisme d'accueil
            informera, aussitôt la
            direction de l'INPT pour convenir des mesures à prendre.
        </p>
        <h2 class="text-base font-semibold mb-0">Article 6</h2>
        <p class="text-xs mb-2">
            L'élève ingénieur continuera à bénéficier du régime d'assurance universitaire souscrite par l'INPT en sa
            faveur, durant la période de
            son stage.
        </p>
        <h2 class="text-base font-semibold mb-0">Article 7</h2>
        <div class="text-xs mb-2">
            <p>
                A la fin du stage, l'organisme d'accueil délivre une attestation de stage mentionnant la période du
                stage et une fiche d'appreciation de l'encadrant sur le travail et le comportement de l'élève
                ingénieur
                stagiaire.
            </p>
            <p>
                Ces documents sont à communiquer à la direction adjointe des stages et relataions avec les
                entreprises
                de l'INPT par email sur <strong>entreprises@inpt.ac.ma</strong>.
            </p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 8</h2>
        <p class="text-xs mb-2">
            L'élève ingénieur s'engage à fournir au terme de son stage, un rapport représentant les résultats de son
            travail à l'organisme
            d'accueil et à l'INPT avant la fin du mois d'octobre de l'année en cours.
        </p>
    </div>
    
    @include('pdf.components.signature_boxes')
    @include('pdf.components.qr_verification')
    @include('pdf.components.footer')
</body>

</html>
