<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\Internship;
use Closure;

class ValidateAction extends Action
{

    public static function getDefaultName(): string
    {
        return __('Validate');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Internship $record): void {
            //  return carbon object with this format 2024-01-02 15:40:05, its a datetime format i mysql database
            // dd('action called');
            $record->withoutTimestamps(fn () => $record->validate());
            // $record->validated_at = Carbon::now()->format('yy-m-d H:i:s');
            // $record->save();
        })
        ->form([\Filament\Forms\Components\Select::make('assigned_department')
        ->options([
            'SC' => 'SC',
            'MIR' => 'MIR',
            'EMO' => 'EMO',
            'GLC' => 'GLC',
        ])
        ->placeholder('Select a department or leave empty if not assigned yet')
    ]);

        return $static;
    }
    // public function action(Closure|string|null $action): static
    // {
    //     $this->configure(fn () => $this->action = $action);
    //     dd('action called');
    //     return $this;
    // }
    public function getTableActions()
    {
        static $action = Action::class;
        $action::make('ValidateInternship')
            ->label('Validate')
            ->icon('check-circle')
            ->message('Are you sure you want to validate this internship?')
            ->confirmText('Validate')
            ->cancelText('Cancel')
            ->method('post')
            ->url('/internships/{record}/validate')
            ->primary();
            return $action;
        return [

            Action::make('ValidateInternship')
                // ->form([
                //     TextInput::make('subject')->required(),
                //     RichEditor::make('body')->required(),
                // ])
            ->form([\Filament\Forms\Components\Select::make('assigned_department')
            ->options([
                'SC' => 'SC',
                'MIR' => 'MIR',
                'EMO' => 'EMO',
                'GLC' => 'GLC',
            ])])

                // ->fillForm(fn (Internship $record): array => [
                //     'internshipId' => $record->student->id,
                // ])
                // ->form([])
                ->action(function (array $data, Internship $record): void {
                    //  return carbon object with this format 2024-01-02 15:40:05, its a datetime format i mysql database
                    $record->validated_at = Carbon::now()->format('yy-m-d H:i:s');
                    $record->save();
                })
            // Add action to validate an internship
            // ValidateAction::make('Validate Internship'),
            // Tables\Actions\EditAction::make(),
            // Tables\Actions\DeleteAction::make(),
            // Tables\Actions\ForceDeleteAction::make(),
            // Tables\Actions\RestoreAction::make(),
        ];
    }
}