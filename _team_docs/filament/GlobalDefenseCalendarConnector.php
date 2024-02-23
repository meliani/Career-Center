<?php

namespace App\Livewire\Sheets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\View;
use Livewire\Component;
use Revolution\Google\Sheets\Facades\Sheets;


class GlobalDefenseCalendarConnector extends Facade
{

    public static function getDefenses(): Collection
    {
    $sheets = Sheets::spreadsheet(config('sheets.defenses_spreadsheet_id'))
        ->sheet(config('sheets.defenses_sheet_id'))
        ->get();

    $header = [
        'day',
        'hour',
        'authorization',
        'location',
        'id',
        'name',
        'stream',
        'supervisor_france',
        'supervisor',
        'Reviewer_1',
        'Reviewer_2',
        'Field_13',
        'Field_14',
        'Field_15',
        'mobility',
        'Field_17',
        'Field_18',
        'organization',
        'country',
        'city',
        'address',
        'project_title',
        'project_details',
        'keywords',
        'Field_26',
        'Field_27',
        'intern_started_at',
        'intern_ended_at',
        'Field_30',
        'duration',
        'Field_32',
        'Field_33',
        'Field_34',
        'Field_35',
        'Field_36',
        'Field_37',
        'referrer',
        'Field_39',
        'Field_40',
        'Field_41',
        'Field_42',
        'Field_43',
        'Field_44',

    ];
    // dd(Sheets::collection(header: $header, rows: $sheets));
    $defenses = Sheets::collection(header: $header, rows: $sheets);

    return $defenses;
    }

}