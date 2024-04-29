<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.5;
    }

    h1 {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    p {
        margin-bottom: 10px;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        margin-top: 10px;

    }

    .section-content {
        margin-left: 20px;
        text-align: justify;
    }

    p {
        text-align: justify;
    }

    table {
        border-collapse: collapse;
    }

    table p {
        margin: 0;
        text-align: center;
    }

    table,
    th,
    td {
        border: 1px solid gray;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    .header_logo {
        width: 130px;
        /* height: 100px; */
        margin-bottom: 20px;
    }

    .footer {
        width: 100%;
        text-align: left;
        /* position: absolute; */
        bottom: 0;
        left: 0;
        padding: 10px;
    }

    .footer p {
        margin: 0;
    }
</style>

{{-- platform logo left aligned --}}
<img src="{{ asset('svg/logo-colors.svg') }}" alt="INPT" class="header_logo">


<h1>CONVENTION DE STAGE</h1>

<div class="section-title">ARTICLE PREMIER :</div>
<p>La présente convention règle les rapports de : <strong>{{$internship->organization_name}}</strong>
    {{$internship->adresse}}</p>
<p>ci-après désignée par Entreprise, et représentée par <strong>{{$internship->parrain_name}}</strong> ,
    <strong>{{$internship->parrain_fonction}}</strong>,
</p>
<p>avec L’Institut National des Postes et Télécommunications (INPT) représenté par Monsieur Ahmed Tamtaoui, Directeur de
    l'Institut National des Postes et Télécommunications,</p>
<p>Concernant le stage de Fin d’études de <strong>{{($internship->student->long_full_name)}}</strong>, élève ingénieur
    de
    la ﬁlière :<strong>
        {{$internship->student->program->getDescription()}}
    </strong></p>
<p>Pour la période du <strong>{{$internship->starting_at->format('d/m/Y')}}</strong> au
    <strong>{{$internship->ending_at->format('d/m/Y')}}</strong>, sous
    la responsabilité de :
</p>
<ul class="section-content">
    <li>Encadrant Externe : <strong>{{$internship->encadrant_ext_name}}</strong></li>
    <li>Coordonnateur de la filière : <strong>Mr. Abdeslam EN-NOUAARY</strong></li>
</ul>
<p>Le stage portera sur le sujet suivant : <strong>{{$internship->title}}</strong></p>
<p>Descriptif détaillé :</p>
<ul class="section-content">
    <li>{{$internship->description}}</li>
</ul>
<p>Adresse du stage (adresse précise, si différente de l’adresse de l’entreprise indiquée ci-dessus) :</p>
{{$internship?->office_location}}
<div class="section-title">ARTICLE SECOND :</div>
<p>La présente convention garantit que le règlement des stages inscrit au verso a été porté à la connaissance de
    l’entreprise et de l’élève et que ceux-ci en ont approuvé expressément toutes les clauses.</p>
<p><strong>Document établi en quatre exemplaires</strong></p>


{{-- signature table with three centered columns --}}
<div class="section-title">Signatures</div>
<table style="width: 100%; margin-top: 20px;">
    <tr>
        <td style="text-align: center;">
            <p>Le Directeur de l’INPT</p>
            <p style="margin-top: 50px;"><strong>Mr. Ahmed TAMTAOUI</strong></p>
        </td>
        <td style="text-align: center;">
            <p>Le Parrain de l’Entreprise</p>
            <p style="margin-top: 50px;"><strong>{{$internship->parrain_name}}</strong></p>
        </td>
        <td style="text-align: center;">
            <p>L’Elève</p>
            <p style="margin-top: 50px;"><strong>{{$internship->student->long_full_name}}</strong></p>
        </td>
    </tr>
</table>

{{-- page foorter --}}
<hr>
<div class="footer">
    <p>Av. Allal El Fassi,</p>
    <p>Madinat Al Irfane,</p>
    <p>Rabat - Maroc</p>
    <p>Tél. : + 212 5 37 77 30 77</p>
    <p>Fax : + 212 5 37 77 30 44</p>
    <p>https://www.inpt.ac.ma</p>
</div>



{{-- second page --}}

<div style="page-break-before: always;"></div>
<img src="{{ asset('svg/logo-colors.svg') }}" alt="INPT" class="header_logo">

<h1>REGLEMENT DES STAGES EN ENTREPRISE</h1>


<p><strong>Art.1</strong> - l’élève ingénieur est appelé à eﬀectuer un stage de PFE obligatoire pour l’obtention du
    diplôme d’Ingénieur en
    Télécommunications et Technologies
    de l'Information. L’objectif poursuivi du stage de PFE est de donner à chaque étudiant l’occasion d’eﬀectuer une
    recherche personnelle et approfondie sur
    un sujet proposé par une entreprise afin de s’immerger dans le monde du travail.</p>
<p><strong>Art.2</strong> - Pendant la durée de son stage, le stagiaire reste placé sous la responsabilité de
    l’entreprise d’accueil tout
    en demeurant étudiant de l’INPT. L'élève
    stagiaire pourra revenir à l'Institut pendant la durée du stage, pour y suivre certains cours demandés
    explicitement
    par le programme, participer à des
    réunions; Le cas échéant, les dates seront portées à la connaissance de l'Entreprise par l'Etablissement.
    Le règlement de l’INPT prévoit l'encadrement du stagiaire au cours de sa période de stage en entreprise. Cet
    encadrement doit être assuré par un
    enseignant de l’INPT et par un membre de l'entreprise chargé d'accueillir et d'accompagner le stagiaire.</p>
<p><strong>Art.3</strong> - Durant son stage, l'étudiant stagiaire est soumis à la discipline et au règlement
    intérieur
    de l'Entreprise,
    notamment en ce qui concerne les horaires,
    la réglementation du travail, les règles d’hygiène et de sécurité en vigueur dans l’entreprise .
    Toute sanction disciplinaire ne peut être décidée que par l'Institut. Dans ce cas, l’entreprise informe
    l'Institut
    des manquements et lui fournit éventuellement
    les éléments constitutifs. L’entreprise, en accord avec le Directeur de l'INPT, peut mettre ﬁn au stage du
    stagiaire, tout en respectant les dispositions ﬁxées
    à l’article 4 ci-après.</p>
<p><strong>Art.4</strong> - Toute absence devra être signalée par l’Entreprise à l’établissement.
    Dans le cas d’une interruption, d’une semaine au moins, pour motif circonstancié ou contexte exceptionnel,
    autorisée
    par l’entreprise, un avenant à la
    présente convention devra être signé au préalable par les cocontractants.
    En cas de volonté d’une des trois parties (entreprise, INPT, étudiant(e)) d’interrompre définitivement le stage,
    celle-ci devra immédiatement en informer
    les deux autres parties par écrit. Les raisons invoquées seront examinées en étroite concertation. La décision
    déﬁnitive d’interruption du stage ne
    sera prise qu’à l’issue de cette phase de concertation.</p>
<p><strong>Art.5</strong> - Le stagiaire est couvert par l'assurance de l’INPT contre les accidents pouvant survenir
    au
    cours du stage
    dans la limite de garantie de son
    assurance.</p>
<p><strong>Art.6</strong> - A l’issue du stage, l’étudiant est tenu de présenter les résultats de son travail, tant
    par
    écrit dans son
    mémoire de ﬁn d’études, qu'oralement lors de
    sa soutenance devant un jury comprenant des représentants de l’entreprise accueillante et des enseignants de
    l’INPT.
    La présentation et le rapport
    devront avoir été validés par l’entreprise au préalable et par l’enseignant encadrant à l’INPT. L'étudiant
    s'engage
    à fournir un rapport à l'organisme
    d’accueil et à l'INPT au maximum une semaine avant la date de la soutenance.
    A la ﬁn du stage et avant la soutenance, le Directeur de stage mentionné dans la convention est prié de
    communiquer
    à l’école une évaluation du
    comportement du stagiaire et de la qualité du travail effectué.</p>
<p><strong>Art.7</strong> - Les étudiants stagiaires prennent l'engagement de n'utiliser en aucun cas les
    information
    recueillies ou
    obtenues par eux pour en faire l'objet de
    publication, communication à des tiers sans accord préalable de la Direction de l'Entreprise, y compris le
    rapport
    de stage. Cet engagement vaudra non
    seulement pour la durée du stage mais également après son expiration. L'étudiant s'engage à ne conserver,
    emporter,
    ou prendre copie d'aucun
    document ou logiciel, de quelque nature que ce soit, appartenant à l'Entreprise, sauf accord de cette dernière.
    L’Entreprise peut demander une restriction
    de la diﬀusion du rapport, voire le retrait de certains éléments très conﬁdentiels. Les personnes amenées à en
    connaître sont contraintes par le secret
    professionnel à n’utiliser ni ne divulguer les information du rapport.</p>
<p><strong>Art.8</strong> - S’il advenait qu’un contrat de travail prenant effet avant la date de fin du stage soit
    signé avec
    l’Entreprise, la présente convention deviendrait
    caduque ; l’étudiant stagiaire perdrait son statut d’étudiant et ne relèverait plus de la responsabilité de
    l’Ecole.
    Ce dernier devrait impérativement en être
    averti avant signature du contrat.</p>

{{-- page foorter --}}
<hr>
<div class="footer" style="position: relative; padding: 0px;">
    <p>Av. Allal El Fassi,</p>
    <p>Madinat Al Irfane,</p>
    <p>Rabat - Maroc</p>
    <p>Tél. : + 212 5 37 77 30 77</p>
    <p>Fax : + 212 5 37 77 30 44</p>
    <p>https://www.inpt.ac.ma</p>
</div>