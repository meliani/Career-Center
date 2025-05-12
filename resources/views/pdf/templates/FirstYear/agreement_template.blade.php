<html lang="fr">

<head>
    <title>Internship Agreement</title>
    @include('pdf.components.stylesheet')

</head>

<body class="text-justify">
    @include('pdf.components.header')

    <div class="border-2 border-gray-500 p-2 rounded-md mx-auto max-w-sm mt-10 mb-6">
        <h1 class="text-lg font-semibold text-center mb-2">
            CONVENTION DE STAGE "OUVRIER"
        </h1>
        <h2 class="text-xs text-center mx-auto max-w-sm">
            1<sup>ère</sup> Année du cycle Ingénieur (INE) de l’Institut National des Postes et
            Télécommunications (INPT-Rabat)
        </h2>
    </div>

    <div class="text-xs font-semibold">
        Entre : L’Institut National des Postes et Télécommunications (INPT-Rabat)
    </div>
    <div class="text-xs">
        Sis à Avenue Allal El Fassi, Madinat Al Irfane, Rabat - Maroc
    </div>
    <div class="text-xs">
        Représenté par Monsieur Ahmed Kensi,
        Directeur Adjoint des Stages et Relations avec les Entreprises,
    </div>
    <div class="text-xs font-semibold mt-0">
        Et désigné dans ce qui suit par l’INPT
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
        Et désigné dans ce qui suit par l’organisme d’accueil
    </div>



    <div class="p-6 bg-white">
        <h1 class="text-base font-semibold mb-0">Préambule</h1>
        <div class="text-xs mb-2">
            <p>L’INPT a pour mission la formation, la recherche et l’expertise. Il est chargé de la formation initiale
                et de la
                formation continue dans les domaines des télécommunications, des technologies de l’information et de la
                communication et
                disciplines connexes.</p>
            <p>La mise en situation professionnelle est une étape importante dans la formation d’ingénieurs, elle permet
                aux élèves de se
                confronter aux réalités techniques, scientifiques, économiques et sociales. C’est dans cette optique que
                l’INPT s’inscrit et marque sa
                volonté d’avoir dans ses programmes de formation, des stages de divers types orientés vers des objectifs
                globaux et spécifiques
                (Stage Ouvrier, Stage Technique et Stage de Projet de Fin d’Études).</p>
            <p>Durant le stage ouvrier dont la durée est de 4 semaines ou plus, l’élève ingénieur occupe généralement un
                poste où il se familiarise
                avec les conditions de travail et observe le fonctionnement de l’organisme d’accueil.</p>
        </div>
        <h2 class="text-base font-semibold mb-0">Article 1</h2>
        <div class="text-xs mb-2">
            <p>La présente convention règle les rapports entre l’organisme d’accueil d'une part, l’INPT et le stagiaire
                d'autre part.</p>
            <p>Cette convention concerne
                <strong>{{$internship->student->long_full_name}}</strong>, élève ingénieur en 1<sup>ère</sup> année du
                cycle INE
                de l’INPT, inscrit(e) en filière <strong>
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
            <p>L’étudiant(e) sera encadré(e) par un Responsable de stage désigné par l'organisme d'accueil, en
                l'occurence
                <strong>{{$internship->supervisor->full_name}}</strong>
            </p>
            <p>Le thème du stage est établi d'un commun accord entre l’organisme d’accueil et l’élève ingénieur.</p>
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
            Durant son stage l’élève ingénieur est soumis à la discipline de l’organisme d’accueil, notamment en ce qui
            concerne l'horaire et le respect du secret professionnel.
        </p>
    </div>



        {{-- second page --}}

        @include('pdf.components.page_break')
        <div class="p-6 bg-white">

            <h2 class="text-base font-semibold mb-0">Article 5</h2>
            <p class="text-xs mb-2">
                En cas de faute grave, de manquement à la discipline ou de tout autre problème, l’organisme d’accueil
                informera, aussitôt la
                direction de l’INPT pour convenir des mesures à prendre.
            </p>
            <h2 class="text-base font-semibold mb-0">Article 6</h2>
            <p class="text-xs mb-2">
                L'élève ingénieur continuera à bénéficier du régime d'assurance universitaire souscrite par l’INPT en sa
                faveur, durant la période de
                son stage.
            </p>
            <h2 class="text-base font-semibold mb-0">Article 7</h2>
            <div class="text-xs mb-2">
                <p>
                    A la fin du stage, l'organisme d’accueil délivre une attestation de stage mentionnant la période du
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
                L’élève ingénieur s'engage à fournir au terme de son stage, un rapport représentant les résultats de son
                travail à l'organisme
                d’accueil et à l'INPT avant la fin du mois d’octobre de l’année en cours.
            </p>
        </div>
        <div class="flex flex-row justify-between items-center mt-4 space-x-4 text-xs text-center">
            <div class="border-2 border-gray-300 w-full p-0 flex flex-col items-center min-h-[250px]">
                <div class="min-h-[100px] pt-2 text-gray-800">Date et Signature du représentant et
                    Cachet de l’organisme d'accueil
                </div>
                {{-- <div class="border-t-2 pt-2 text-gray-400 text-center">Cachet de l’organisme d'accueil</div> --}}
            </div>
            <div class="border-2 border-gray-300 w-full p-0 flex flex-col items-center min-h-[250px]">
                <div class="min-h-[100px] text-gray-800 pt-2">Date et Signature du stagiaire avec Mention manuscrite «
                    Lu et
                    approuvé »</div>
                {{-- <div class="border-t-2 pt-2 text-gray-400 text-center">Mention manuscrite « Lu et approuvé »</div>
                --}}
            </div>
            <div class="border-2 border-gray-300 w-full pt-0 flex flex-col justify-between min-h-[250px]">
                <div class="pt-2 text-gray-800 text-center">Le représentant de l’INPT</div>
            </div>
        </div>
        <div class="flex justify-center my-auto">
            {!! $qrCodeSvg !!}
        </div>
        <div class="text-center text-[0.60rem] italic mb-52">
            <p>Ce tag est pour la vérification</p>
            <p> de l'authenticité de ce document</p>
        </div>
        <div class="p-2 text-gray-500 fixed bottom-0 w-full border-t-2">
            <p class="text-xs mb-0">Av. Allal El Fassi,</p>
            <p class="text-xs mb-0">Madinat Al Irfane,</p>
            <p class="text-xs mb-1">Rabat - Maroc</p>
            <p class="text-xs mb-0">+ 212 538 002 700</p>
            <p class="text-xs mb-1">+ 212 538 002 860/765</p>
            <p class="text-xs mb-0">https://www.inpt.ac.ma</p>
        </div>
    </div>
</body>

</html>
