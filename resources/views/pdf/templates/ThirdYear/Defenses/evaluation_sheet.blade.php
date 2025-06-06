<html lang="fr">

<head>
    <title>Fiche d'Evaluation PFE</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="text-justify">
{{-- we'll start iterating by the students --}}
@foreach ($project->agreements as $agreement)
<div class="container mx-auto">
    @include('pdf.components.header')
    <div class="border-2 border-gray-900 p-2 mx-auto max-w-sm mt-1 mb-4">
        <h1 class="text-lg font-semibold text-center mb-2">
            Fiche d'Evaluation PFE
        </h1>
        <h2 class="text-sm text-center mx-auto max-w-sm">
            Année universitaire {{ \App\Models\Year::current()->title }}
        </h2>
    </div>
    <div class="grid grid-cols-1 gap-4 text-xs">
        <fieldset class="border border-black p-2">
            <legend class="font-bold">Référent Administratif, Date & Lieu</legend>
            <p class="mb-3"><strong>{{ $agreement->agreeable->student->administrative_supervisor->long_full_name}}</strong></p>
            <p class="text-left">{{ $project->defense_plan}}</p>
        </fieldset>
        <fieldset class="border border-black p-2 min-w-[300px]">
            <legend class="font-bold">Elève & Entreprise</legend>
            <p>
                <strong>
                    {{ $project->students_names }}
                </strong>
            </p>
            <p><strong>ID PFE:</strong> {{ $agreement->agreeable->student->id_pfe }}</p>
            <p><strong>Filière:</strong>
                {{ $agreement->agreeable->student->program->getLabel() }}
            </p>
            <p><strong>Entreprise:</strong> {{ $agreement->agreeable->organization->name }}</p>
        </fieldset>

        <fieldset class="col-span-2 border border-black p-2">
            <legend class="font-bold">Intitulé</legend>
            <p>{{ $project->title }}
            </p>
        </fieldset>

        <fieldset class="col-span-2 border border-black p-2">
            <legend class="font-bold">Jury</legend>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="mb-16"><strong>Encadrant:</strong> {{ $project->academic_supervisor_name }}</p>
                    <p class="mb-16"><strong>Examinateur 1:</strong> {{ $project->reviewer1 }}</p>
                </div>
                <div>
                    <p class="mb-16"><strong>Examinateur 2:</strong> {{ $project->reviewer2 }}</p>
                    <p class="mb-16"><strong>Examinateur 3 (Entreprise):</strong> {{ $project->external_supervisor_name
                        }}</p>
                </div>
            </div>
        </fieldset>
        <fieldset class="col-span-2 border border-black p-2">
            <legend class="font-bold">Observations sur le déroulement du PFE (Esprit d’analyse, de synthèse, créativité,
                autonomie, assiduité….)</legend>
            <div class="space-y-5 mt-4 mb-4">
                @for ($i = 0; $i < 5; $i++) <div class="border-b border-dotted border-gray-400">
            </div>
            @endfor
        </fieldset>
        <div class="col-span-2 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-fixed border-collapse border border-gray-900">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-2 py-2 text-center text-xs font-bold text-gray-900 tracking-wider border border-gray-900">
                            Critère d’évaluation
                        </th>
                        <th scope="col"
                            class="px-2 py-2 text-center text-xs font-bold text-gray-900 tracking-wider border border-gray-900">
                            Note attribuée par
                        </th>
                        <th scope="col"
                            class="px-2 py-2 text-center text-xs font-bold text-gray-900 tracking-wider whitespace-nowrap border border-gray-900">
                            Pondération (%)
                        </th>
                        <th scope="col"
                            class="px-2 py-2 text-center text-xs font-bold text-gray-900 tracking-wider whitespace-nowrap border border-gray-900">
                            Note (/20)
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm font-medium text-gray-900 border border-gray-900">
                            Valeur globale du travail réalisé et des rapports intermédiaires
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900">
                            Parrain externe et Encadrant interne
                        </td>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm text-gray-900 text-center border border-gray-900">
                            <strong>40%</strong>
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900"></td>
                    </tr>
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm font-medium text-gray-900 border border-gray-900">
                            Evaluation du Rapport
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900">
                            jury de soutenance
                        </td>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm text-gray-900 text-center border border-gray-900">
                            <strong>40%</strong>
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900"></td>
                    </tr>
                    <tr>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm font-medium text-gray-900 border border-gray-900">
                            Evaluation de l’exposé
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900">
                            jury de soutenance
                        </td>
                        <td
                            class="px-2 py-2 whitespace-normal text-sm text-gray-900 text-center border border-gray-900">
                            <strong>20%</strong>
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900"></td>
                    </tr>
                    <tr>
                        <td colspan="3"
                            class="px-2 py-2 whitespace-normal text-sm font-bold text-gray-900 border border-gray-900">
                            Note Globale (/20)
                        </td>
                        <td class="px-2 py-2 whitespace-normal text-sm text-gray-900 border border-gray-900"></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <fieldset class="col-span-2 border border-black p-2">
            <legend class="font-bold">NB</legend>
            <p>Dans le cas où la note est supérieure à 18/20, un rapport doit être établi pour préciser
                l’originalité et l’exception du travail réalisé.</p>
            <p>Le nom du responsable de la validation des éventuelles corrections du PFE doit être précisé.</p>
        </fieldset>
    </div>
</div>
    @pageBreak
    @endforeach

</body>

</html>
