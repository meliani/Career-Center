<html lang="fr">

<head>
    <title>Internship Agreement</title>
    @include('pdf.components.stylesheet')
</head>

<body class="text-justify">
    @include('pdf.components.header')

    @include('pdf.components.title-box',
    [
        'mainTitle' => 'CONVENTION DE STAGE TECHNIQUE',
        'subtitle' => '2<sup>ème</sup> Année du cycle Ingénieur (INE) de l\'Institut National des Postes et Télécommunications (INPT-Rabat)'
    ])

    <div class="p-6 bg-white">
        <div class="text-base font-semibold">Article 1 : Désignation des parties</div>
        <div class="text-xs mb-4">
            <p>La présente convention règle les rapports <strong>entre</strong> :</p>
            
            <div class="mb-2">
                <p><strong>L'Institut National des Postes et Télécommunications (INPT-RABAT),</strong> établissement d'enseignement 
                supérieur rattaché à l'ANRT en vertu de l'article 107 de la loi n°24-96 relative à la poste et 
                aux télécommunications, sis à Avenue Allal Al Fassi, Madinat Al Irfane à Rabat, représenté par 
                Monsieur <strong>Ahmed Kensi</strong>, Directeur Adjoint des Stages et des Relations avec les Entreprises.</p>
                <p class="text-right italic font-semibold">Ci-après désigné l'Institut.</p>
            </div>
            
            <div class="mb-2">
                <p>Et l'organisme d'accueil : <strong>{{$internship->organization->name}}</strong><br>
                Adresse : <strong>{{$internship->organization->address}}, {{$internship->organization->city}}, {{$internship->organization->country}}</strong><br>
                Représenté par : <strong>{{$internship->parrain->full_name}}</strong><br>
                Fonction : <strong>{{$internship->parrain->function}}</strong></p>
                <table class="w-full mb-1 text-xs">
                    <tr>
                        <td class="w-1/3"><strong>Téléphone</strong> : {{$internship->parrain->phone}}</td>
                        <td class="w-2/3"><strong>Email</strong> : {{$internship->parrain->email}}</td>
                    </tr>
                </table>
                <p class="text-right italic font-semibold">Ci-après désigné organisme d'accueil.</p>
            </div>
            
            <div class="mb-2">
                <p>Concernant le stage <strong>technique</strong> d'une durée de <strong>4 à 8 semaines</strong> et effectué par l'élève Ingénieur :</p>
                <table class="w-full mb-1 text-xs">
                    <tr>
                        <td class="w-1/3"><strong>Nom</strong> : {{$internship->student->last_name}}</td>
                        <td class="w-2/3"><strong>Prénom</strong> : {{$internship->student->first_name}}</td>
                    </tr>
                    <tr>
                        <td><strong>Tél</strong> : {{$internship->student->phone}}</td>
                        <td><strong>Adresse Email</strong> : {{$internship->student->email}}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Filière</strong> : 
                            @if(is_a($internship->student->program, \App\Enums\Program::class))
                            {{$internship->student->program->getDescription()}}
                            @else
                            {{$internship->student->program}}
                            @endif
                        </td>
                    </tr>
                </table>
                <p class="text-right italic font-semibold">Ci-après désigné le stagiaire.</p>
            </div>
        </div>

        <div class="text-base font-semibold">Article 2 : Objet de la convention</div>
        <div class="text-xs mb-4">
            <p>La présente convention a pour objet la mise en œuvre, au profit de l'élève ingénieur stagiaire de l'Institut, un stage en milieu professionnel chez l'organisme d'accueil dans le cadre de sa formation du cycle Ingénieur l'INPT.</p>
        </div>

        <div class="text-base font-semibold">Article 3 : Finalité du stage technique</div>
        <div class="text-xs mb-4">
            <p>Le stage a pour objectif l'immersion de l'étudiant dans le monde industriel, sa sensibilisation au travail en équipe, sa familiarisation aux processus de l'entreprise et à l'ensemble des exigences de la profession d'ingénieur. Il permet à l'élève ingénieur de mener un travail de recherche individuel et de résoudre un problème scientifique ou technique dans l'environnement de l'entreprise. Il est d'une durée de <strong>4 à 8 semaines</strong> et a lieu en général après la fin des cours de deuxième année.</p>
        </div>

        <div class="text-base font-semibold">Article 4 : Thématique et période de stage</div>
        <div class="text-xs mb-4">
            <p>L'élève stagiaire va travailler pendant la période de stage sur le thème détaillé ci-après :<br>
            <strong>{{$internship->title}}</strong></p>
            <p>
                La durée du stage est fixée à <strong>{{$internship->duration_in_weeks}} semaines</strong>, du
                @if($internship->starting_at instanceof \Carbon\Carbon)
                <strong>{{$internship->starting_at->format('d/m/Y')}}</strong>
                au
                <strong>{{$internship->ending_at->format('d/m/Y')}}</strong>.
                @else
                ..................... au .....................
                @endif
            </p>
            <p>Le présent stage sera effectué en mode: 
                <span class="ml-2 mr-4">{{ $internship->internship_type?->value === \App\Enums\InternshipType::OnSite->value ? '☑' : '☐' }} Présentiel</span>
                <span class="mr-4">{{ $internship->internship_type?->value === \App\Enums\InternshipType::Remote->value ? '☑' : '☐' }} À distance</span>
                <span>{{ $internship->internship_type?->value === \App\Enums\InternshipType::Hybrid->value ? '☑' : '☐' }} En hybride</span>
            </p>
        </div>
    </div>
    
    {{-- QR Code verification component --}}
    @include('pdf.components.qr_verification')
    
    @include('pdf.components.footer')

    {{-- second page --}}
    @include('pdf.components.page_break')
    
    <div class="p-6 bg-white">
        <div class="text-base font-semibold">Article 5 : Déroulement du stage</div>
        <div class="text-xs mb-4">
            <p>Pendant la durée du stage, le stagiaire est soumis aux règles générales en vigueur dans l'organisme d'accueil, notamment en matière d'horaires, de congés et de discipline.</p>
            <p>Le Directeur de l'Institut et le représentant de l'organisme d'accueil se tiennent mutuellement informés des difficultés qui pourraient être rencontrées au cours du stage. Le cas échéant, ils prendront, d'un commun accord et en liaison avec le coordonnateur de la filière concernée à l'INPT, les dispositions pour résoudre les problèmes d'absentéisme ou d'indiscipline. Au besoin, ils étudieront ensemble les modalités de suspension ou de résiliation du stage.</p>
        </div>

        <div class="text-base font-semibold">Article 6 : Assurance responsabilité civile</div>
        <div class="text-xs mb-4">
            <p>Le stagiaire est assuré pendant sa présence dans l'organisme d'accueil selon les lois en vigueur en matière d'accueil de stagiaires.</p>
        </div>

        <div class="text-base font-semibold">Article 7 : Gratification</div>
        <div class="text-xs mb-4">
            <p>L'organisme d'accueil peut verser au stagiaire une gratification ou une rémunération.</p>
        </div>

        <div class="text-base font-semibold">Article 8 : Rapport de stage</div>
        <div class="text-xs mb-4">
            <p>À L'issue de son stage, le stagiaire remet des exemplaires de son rapport de stage (sur support papier et informatique) à l'organisme d'accueil et à l'institut. Un exemplaire, après sa validation, est conservé dans la bibliothèque de l'institut.</p>
        </div>

        <div class="text-base font-semibold">Article 9 : Appréciation du stage</div>
        <div class="text-xs mb-4">
            <p>À la fin du stage, l'organisme d'accueil délivre une attestation de stage mentionnant la période du stage et une fiche d'appréciation de l'encadrant sur le travail et le comportement de l'élève ingénieur stagiaire selon le modèle en annexe à cette convention.</p>
            <p>Ces documents sont à communiquer à la Direction Adjointe des Stages et des Relations avec les Entreprises de l'INPT par email sur <a href="mailto:entreprises@inpt.ac.ma"><strong>entreprises@inpt.ac.ma</strong></a> / <a href="mailto:dasre@inpt.ac.ma"><strong>dasre@inpt.ac.ma</strong></a></p>
        </div>

        <div class="text-base font-semibold">Article 10 : Confidentialité</div>
        <div class="text-xs mb-4">
            <p>Le stagiaire est tenu d'observer une entière discrétion sur l'ensemble des renseignements qu'il pourra recueillir dans l'organisme d'accueil. Il s'engage à ne pas faire figurer sur son rapport ou communiquer à des tiers aucune information considérée confidentielle par l'organisme d'accueil.</p>
        </div>

        <div class="text-xs mb-4 mt-8">
            <p>Fait en trois (2) originaux de deux (2) pages de dix (10) articles, le ...........................................</p>
        </div>
    </div>
    
    @include('pdf.components.signature_boxes')

    @include('pdf.components.page_break')

    <div class="p-6 bg-white">
        <h1 class="text-lg font-bold text-center mb-6">Annexe 1 : Fiche d'appréciation de stage</h1>

        <div class="text-[10px] mb-4">
            <table class="w-full border border-black mb-6">
                <tr class="border border-black">
                    <td class="border border-black p-1 w-1/3">Organisme</td>
                    <td class="border border-black p-1 w-2/3">{{$internship->organization->name}}</td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Étudiant – Stagiaire</td>
                    <td class="border border-black p-1">{{$internship->student->long_full_name}}</td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Sujet</td>
                    <td class="border border-black p-1">{{$internship->title}}</td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Responsable de stage<br>Fonction :<br>Signature et cachet</td>
                    <td class="border border-black p-1 h-24">{{$internship->supervisor->full_name}}<br>{{$internship->supervisor->function}}</td>
                </tr>
            </table>

            <table class="w-full border border-black">
                <tr class="border border-black">
                    <td class="border border-black p-1 w-1/3 font-semibold">Travail Fourni et Apport Personnel</td>
                    <td class="border border-black p-1 w-1/6 text-center font-semibold">Note</td>
                    <td class="border border-black p-1 w-1/3 font-semibold">Compétences</td>
                    <td class="border border-black p-1 w-1/6 text-center font-semibold">Note</td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Réalisation des objectifs</td>
                    <td class="border border-black p-1"></td>
                    <td class="border border-black p-1">Pertinence des propositions</td>
                    <td class="border border-black p-1"></td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Qualité du travail fourni</td>
                    <td class="border border-black p-1"></td>
                    <td class="border border-black p-1">Fiabilité des résultats</td>
                    <td class="border border-black p-1"></td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Investissement personnel</td>
                    <td class="border border-black p-1"></td>
                    <td class="border border-black p-1">Maîtrise technologique, méthodologie</td>
                    <td class="border border-black p-1"></td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1">Adaptabilité à l'entreprise</td>
                    <td class="border border-black p-1"></td>
                    <td class="border border-black p-1">Organisation, conduite de projet, coordination</td>
                    <td class="border border-black p-1"></td>
                </tr>
                <tr class="border border-black">
                    <td class="border border-black p-1 font-semibold">Moyenne 1</td>
                    <td class="border border-black p-1"></td>
                    <td class="border border-black p-1 font-semibold">Moyenne 2</td>
                    <td class="border border-black p-1"></td>
                </tr>
                <tr class="border border-black">
                    <td colspan="2" class="border border-black p-1 font-semibold">Note Finale/20 = (Moyenne1+Moyenne2)/2</td>
                    <td colspan="2" class="border border-black p-1"></td>
                </tr>
            </table>

            <div class="mt-6">
                <p class="font-semibold">Commentaires :</p>
                <div class="min-h-[250px] border border-black p-2"></div>
            </div>

            <p class="mt-4">
                <strong>NB :</strong> La présente fiche d'évaluation est à remplir et envoyer par mail à
                <a href="mailto:entreprises@inpt.ac.ma">entreprises@inpt.ac.ma</a> et
                <a href="mailto:dasre@inpt.ac.ma">dasre@inpt.ac.ma</a>
            </p>
        </div>
    </div>

</body>

</html>
