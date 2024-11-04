<?php

namespace App\Filament\Actions\Action;

use App\Enums;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class ValidateAction extends Action
{
    public static function getDefaultName(): string
    {
        return __('Program Coordinator validation');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, InternshipAgreement | FinalYearInternshipAgreement $record): void {

            $record->withoutTimestamps(fn () => $record->validate($data['assigned_department'] ?? null));

        })
            ->requiresConfirmation('Are you sure you want to mark this internship as Signed?')
            ->form([\Filament\Forms\Components\Select::make('assigned_department')
                ->options(Enums\Department::class)
                ->placeholder('Select a department or leave empty if not assigned yet'),
            ]);

        return $static;
    }

    // public function getTableActions()
    // {
    //     static $action = Action::class;
    //     $action::make('ValidateInternship')
    //         ->label('Validate')
    //         ->icon('check-circle')
    //         ->message('Are you sure you want to validate this internship?')
    //         ->confirmText('Validate')
    //         ->cancelText('Cancel')
    //         ->method('post')
    //         ->url('/internships/{record}/validate')
    //         ->primary();

    //     return $action;

    //     return [

    //         Action::make('ValidateInternship')
    //             ->form([\Filament\Forms\Components\Select::make('assigned_department')
    //                 ->options(Enums\Department::class)])
    //             ->action(function (array $data, InternshipAgreement | FinalYearInternshipAgreement $record): void {
    //                 $record->validated_at = Carbon::now()->format('yy-m-d H:i:s');
    //                 $record->save();
    //             }),
    //         // Add action to validate an internship
    //         // Tables\Actions\EditAction::make(),
    //         // Tables\Actions\DeleteAction::make(),
    //         // Tables\Actions\ForceDeleteAction::make(),
    //         // Tables\Actions\RestoreAction::make(),
    //     ];
    // }
}
