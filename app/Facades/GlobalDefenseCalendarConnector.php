<?php

namespace App\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Revolution\Google\Sheets\Facades\Sheets;

class GlobalDefenseCalendarConnector extends Facade
{
    public static function getDefenses(): Collection
    {
        $sheets = Sheets::spreadsheet(config('sheets.defenses_spreadsheet_id'))
            ->sheet(config('sheets.defenses_sheet_id'))
            ->get();

        /* Date Soutenance	Heure	Lieu	ID PFE	Nom de l'étudiant	Filière	Encadrant Interne	"Nom et Prénom
Examinateur 1"	"Nom et Prénom
Examinateur 2" */
        $header = [
            'Date Soutenance',
            'Heure',
            'Lieu',
            'ID PFE',
            'Nom de l\'étudiant',
            'Filière',
            'Encadrant Interne',
            'Nom et Prénom Examinateur 1',
            'Nom et Prénom Examinateur 2',
        ];
        // dd(Sheets::collection(header: $header, rows: $sheets));

        $defenses = Sheets::collection(header: $header, rows: $sheets);

        return $defenses;
    }
}
