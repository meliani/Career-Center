<x-layouts.public-layout>
    <div class="flex justify-center py-4">
        <div class="mt-8 w-full">
            <!-- Existing content -->
            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full leading-normal text-sm">
                    <thead>
                        <tr class="bg-gray-100">

                            <!-- Always visible columns headers -->
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Autorisation</th>
                            <th class="px-4 py-2">ID PFE</th>
                            <th class="px-4 py-2">Nom de l'étudiant</th>

                            <!-- Hidden on small screens -->
                            <th class="px-4 py-2 hidden sm:table-cell">Encadrant Interne</th>
                            <th class="px-4 py-2 hidden sm:table-cell">Nom et Prénom Examinateur 1</th>
                            <th class="px-4 py-2 hidden sm:table-cell">Nom et Prénom Examinateur 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $defense)
                        @if($loop->first)
                        @continue
                        @endif
                        <tr>
                            <!-- Always visible columns data -->
                            <td class="px-4 py-2">
                                <p>{{ $defense['Date Soutenance'] }}</p>
                                <p>{{ $defense['Heure'] }}</p>
                                <p>{{ $defense['Lieu'] }}</p>
                            </td>
                            <td class="px-4 py-2"><strong>{{ $defense['Autorisation'] }}</strong></td>
                            <td class="px-4 py-2">{{ $defense['ID PFE'] }}</td>
                            <td class="px-4 py-2"><strong>{{ $defense['Nom de l\'étudiant'] }}</strong>
                                {{-- w'ell check if student if empty --}}
                                @if($defense['Nom de l\'étudiant'] == 'Libre')
                                @else
                                <p><strong>Filière: </strong>{{ $defense['Filière'] }}</p>
                                @endif
                            </td>

                            <!-- Hidden on small screens -->
                            <td class="px-4 py-2 hidden sm:table-cell"></td>
                            <td class="px-4 py-2 hidden sm:table-cell">{{ $defense['Encadrant Interne'] }}</td>
                            <td class="px-4 py-2 hidden sm:table-cell">{{ $defense['Nom et Prénom Examinateur 1'] }}
                            </td>
                            <td class="px-4 py-2 hidden sm:table-cell">{{ $defense['Nom et Prénom Examinateur 2'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.public-layout>