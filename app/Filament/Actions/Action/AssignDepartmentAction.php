<?php

namespace App\Filament\Actions\Action;

use App\Models\InternshipAgreement;
use Filament\Tables\Actions\Action;

class AssignDepartmentAction extends Action
{
    // protected ?string $name = null;

    // protected ?string $label = null;

    public static function getDefaultName(): string
    {
        return __('Assign department');
    }

    public static function make(?string $name = null): static
    {
        // dd('action called');

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, InternshipAgreement $record): void {
            // add a form to select the department
            // dd('action called');
            $record->withoutTimestamps(fn () => $record->assignDepartment($data['assigned_department']));

        })
            ->form([\Filament\Forms\Components\Select::make('assigned_department')
                ->options([
                    'SC' => 'SC',
                    'MIR' => 'MIR',
                    'EMO' => 'EMO',
                    'GLC' => 'GLC',
                ])]);

        return $static;
    }
}
