<html lang="en">

<head>
    <title>{{__('Projects participation')}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <div class="px-2 py-8 max-w-xl mx-auto">
        <div class="flex items-center justify-between mb-8 mt-10">
            <div class="text-gray-700 font-semibold text-center text-lg justify">
                {{__('Participation aux encadrements')}}
            </div>
        </div>
        <table class="w-full text-center mb-8 border-1 border-gray-300">
            <thead class="text-sm">
                <tr>
                    <th class="text-gray-700 font-bold uppercase py-2">{{__('PFE ID')}}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{__('Student name')}}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{__('Project title')}}</th>
                    <th class="text-gray-700 font-bold uppercase py-2">{{__('Jury role')}}</th>
                </tr>
            </thead>
            <tbody class="text-xs text-left">
                @foreach ($professor->projects as $project)
                <tr>
                    <td class="py-4 text-gray-700">{{ $project->id_pfe }}</td>
                    <td class="py-4 text-gray-700">
                        @foreach ($project->students as $student)
                        <p>{{ $student->full_name }}</p>
                        @endforeach
                    </td>
                    <td class="py-4 text-gray-700">{{ $project->title }}</td>

                    <td class="py-4 text-gray-700">{{ $project->pivot->jury_role->getLabel() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="flex justify-start mb-8">
            <div class="text-gray-700 mr-2">Nombre d'encadrements</div>
            <div class="text-gray-700">{{$professor->projects_count}}</div>
        </div>
    </div>

</body>

</html>