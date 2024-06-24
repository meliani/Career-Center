<x-layouts.public-layout>
    <div class="flex justify-center py-4">
        <div class="mt-8 w-full">
            <div class="flex items-center justify-between">
                <!-- Title centered both vertically and horizontally -->
                <div class="flex-1 text-center mb-6 pb-10 mt-0 my-auto">
                    <h2 class="text-2xl font-semibold text-gray-800">Calendrier des soutenances 2024</h2>
                </div>
                <!-- Logo aligned to the right with vertical centering and margins -->
                <div x-data="{ mode: 'light' }" x-on:dark-mode-toggled.window="mode = $event.detail"
                    class="flex-shrink-0 ml-4 mr-2 my-auto">
                    <img src="{{ asset('/svg/logo-colors.svg') }}" alt="Logo" class="h-20">
                </div>
            </div>
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
                            <th class="px-4 py-2">Encadrant Interne</th>
                            <th class="px-4 py-2">Nom et Prénom Examinateur 1</th>
                            <th class="px-4 py-2">Nom et Prénom Examinateur 2</th>
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
                            <td class="px-4 py-2">{{ $defense['Encadrant Interne'] }}</td>
                            <td class="px-4 py-2">{{ $defense['Nom et Prénom Examinateur 1'] }}
                            </td>
                            <td class="px-4 py-2">{{ $defense['Nom et Prénom Examinateur 2'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.public-layout>