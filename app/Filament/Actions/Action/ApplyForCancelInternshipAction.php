<?php

namespace App\Filament\Actions\Action;

// use App\Models\InternshipAgreement;
use App\Models\Apprenticeship as InternshipAgreement;
use Filament\Tables\Actions\Action;

class ApplyForCancelInternshipAction extends Action
{
    public static function getDefaultName(): string
    {
        return __('Apply for internship cancellation');
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, InternshipAgreement $record): void {

            $record->withoutTimestamps(fn () => $record->applyForCancellation($data['cancellation_reason']));

        })
            ->requiresConfirmation(fn (InternshipAgreement $internship) => 'Are you sure you want to apply for the cancellation of this internship?')
            ->form([
                \Filament\Forms\Components\MarkdownEditor::make('cancellation_reason')
                    ->label('Cancellation reason')
                    ->placeholder('Please provide a reason for the cancellation')
                    ->required(),
            ]);

        return $static;
    }
}
