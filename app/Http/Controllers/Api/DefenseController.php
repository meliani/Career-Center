<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DefenseSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DefenseController extends Controller
{
    public function store(Request $request)
    {
        $defenses = $request->all();

        // Log the received data for debugging
        Log::info('Received defenses data:', $defenses);

        // Save the data to the database
        foreach ($defenses as $defense) {
            DefenseSync::updateOrCreate(
                ['id_pfe' => $defense['ID PFE']],
                [
                    'date_soutenance' => $defense['Date Soutenance'] ?? null,
                    'heure' => $defense['Heure'] ?? null,
                    'autorisation' => $defense['Autorisation'] ?? null,
                    'lieu' => $defense['Lieu'] ?? null,
                    'nom_etudiant' => $defense['Nom de l\'étudiant'] ?? null,
                    'filiere' => $defense['Filière'] ?? null,
                    'encadrant_interne' => $defense['Encadrant Interne'] ?? null,
                    'examinateur_1' => $defense['Nom et Prénom Examinateur 1'] ?? null,
                    'examinateur_2' => $defense['Nom et Prénom Examinateur 2'] ?? null,
                    'remarques' => $defense['remarques'] ?? null,
                    'convention_signee' => $defense['convention signée'] ?? null,
                    'accord_encadrant_interne' => $defense['accord encadrant interne'] ?? null,
                    'fiche_evaluation_entreprise' => $defense['fiche evaluation entreprise'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Data received successfully'], 200);
    }
}
