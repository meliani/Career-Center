<?php

namespace App\Filament\Actions\Action;

// use App\Models\InternshipAgreement;
use App\Models\Apprenticeship as InternshipAgreement;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
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

            $record->withoutTimestamps(fn () => $record->applyForCancellation($data['cancellation_reason'] ?? null, $data['verification_document_url'] ?? null));

            Notification::make()
                ->title('Internship cancellation request submitted')
                ->body('Your internship cancellation request has been submitted successfully. You will be notified once the request has been processed.')
                ->success()
                ->icon('heroicon-o-check-circle')
                ->iconColor('green')
                ->duration(5000)
                ->persistent()
                ->actions([

                ])
                ->send();

        })
            ->slideOver()
            // ->requiresConfirmation(fn (InternshipAgreement $internship) => 'Are you sure you want to apply for the cancellation of this internship?')
            ->form([
                \Filament\Forms\Components\Select::make('is_signed_by_organization')
                    ->label('Has the agreement been signed by the organization?')
                    ->options([
                        true => __('Yes (The organization has signed the agreement)'),
                        false => __('No (The organization has not signed the agreement)'),
                    ])
                    ->default(false)
                    ->required()
                    ->live(),
                \Filament\Forms\Components\Placeholder::make('Anouncement')
                    ->hiddenLabel()
                    ->hidden(fn (Get $get) => $get('is_signed_by_organization') == false)
                    ->content(__('If the internship has been signed by the organization, you should ask the organization to send us a cancellation request to proceed with the cancellation.')),
                \Filament\Forms\Components\Placeholder::make('Anouncement')
                    ->hiddenLabel()
                    ->hidden(fn (Get $get) => $get('is_signed_by_organization') == true)
                    ->content(__('The text you provide here will be sent to the organization to inform them of the cancellation.')),
                \Filament\Forms\Components\MarkdownEditor::make('cancellation_reason')
                    ->label('Cancellation reason')
                    ->placeholder(__('Please provide a reason for the cancellation.'))
                    ->required()
                    ->hidden(fn (Get $get) => $get('is_signed_by_organization') == true),

                // \Filament\Forms\Components\Placeholder::make('Verification document')
                //     ->hiddenLabel()
                //     ->hidden(fn (Get $get) => $get('is_signed_by_organization') == true)
                //     ->content(__('Please upload a photo of the last page of the agreement to verify signatures')),
                \Filament\Forms\Components\FileUpload::make('verification_document_url')
                    ->label('Verification document')
                    ->placeholder(__('Please upload a photo of the last page of the agreement to verify signatures.'))
                    ->required()
                    // ->directory('document/cancellation_verification')
                    ->disk('cancellation_verification')
                    ->hidden(fn (Get $get) => $get('is_signed_by_organization') == true),
            ]);

        return $static;
    }
}
