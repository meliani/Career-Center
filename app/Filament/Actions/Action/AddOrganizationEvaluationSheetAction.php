<?php

namespace App\Filament\Actions\Action;

use App\Models\Project;
use Filament\Tables\Actions\Action;

class AddOrganizationEvaluationSheetAction extends Action
{
    public static array $emails = [];

    protected static $emailBody;

    public static function getDefaultName(): string
    {
        return __('Add organization evaluation sheet');
    }

    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure()->action(function (array $data, Project $record): void {
            $record->organization_evaluation_sheet_url = $data['organization_evaluation_sheet_url'];
            $record->save();
        })
            ->form(function ($record) {
                return [
                    \Filament\Forms\Components\FileUpload::make('organization_evaluation_sheet_url')
                        ->directory('document/organization_evaluation_sheet')
                        ->label('Organization Evaluation Sheet')
                        ->placeholder('Upload the organization evaluation sheet'),
                    // ->acceptedFileTypes(['pdf']),
                ];
            })
            ->color('success');

        return $static;
    }
}
