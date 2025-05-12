<?php

namespace App\Filament\Actions\Action;

use App\Models\ApprenticeshipAmendment;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class AddApprenticeshipAmendmentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_apprenticeship_amendment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Request Amendment'));
        $this->modalHeading(__('Request Apprenticeship Amendment'));
        $this->modalDescription(function (Model $record) {
            return __(
                'Here you can request changes to your apprenticeship agreement. The amendment will need to be approved by an administrator before it takes effect.'
            );
        });
        $this->icon('heroicon-o-pencil-square');
        $this->color('warning');

        $this->form([
            Forms\Components\Tabs::make('Amendment Types')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('details_amendment')
                        ->label(__('Title & Description'))
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Toggle::make('modify_details')
                                ->label(__('Modify Title and/or Description'))
                                ->live()
                                ->default(false),

                            Forms\Components\Placeholder::make('current_title_placeholder')
                                ->label(__('Current Title'))
                                ->content(fn (Model $record): string => $record->title)
                                ->visible(fn (Forms\Get $get): bool => $get('modify_details')),

                            Forms\Components\TextInput::make('title')
                                ->label(__('New Title'))
                                ->maxLength(255)
                                ->visible(fn (Forms\Get $get): bool => $get('modify_details')),

                            Forms\Components\Placeholder::make('current_description_placeholder')
                                ->label(__('Current Description'))
                                ->content(fn (Model $record): string => mb_substr(strip_tags($record->description), 0, 200) . '...')
                                ->visible(fn (Forms\Get $get): bool => $get('modify_details')),

                            Forms\Components\MarkdownEditor::make('description')
                                ->label(__('New Description'))
                                ->columnSpanFull()
                                ->visible(fn (Forms\Get $get): bool => $get('modify_details')),
                        ]),

                    Forms\Components\Tabs\Tab::make('period_amendment')
                        ->label(__('Internship Period'))
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\Toggle::make('modify_period')
                                ->label(__('Modify Internship Period'))
                                ->live()
                                ->default(false),

                            Forms\Components\Placeholder::make('current_period_placeholder')
                                ->label(__('Current Internship Period'))
                                ->content(fn (Model $record): string => $record->internship_period)
                                ->visible(fn (Forms\Get $get): bool => $get('modify_period')),

                            DateRangePicker::make('internship_period')
                                ->label(__('New Internship Period'))
                                // ->displayFormat('d/m/Y')
                                // ->format('d/m/Y')
                                ->afterStateUpdated(function (string $state, Set $set) {
                                    if (!empty($state)) {
                                        $dates = explode(' - ', $state);
                                        if (count($dates) === 2) {
                                            $set('new_starting_at', $dates[0]);
                                            $set('new_ending_at', $dates[1]);
                                        }
                                    }
                                })
                                ->visible(fn (Forms\Get $get): bool => $get('modify_period')),

                            Forms\Components\Hidden::make('new_starting_at'),
                            Forms\Components\Hidden::make('new_ending_at'),
                        ]),
                ]),

            Forms\Components\Textarea::make('reason')
                ->label(__('Reason for Amendment'))
                ->required()
                ->placeholder(__('Please explain why you need to make these changes'))
                ->helperText(__('This explanation will be reviewed by the administrator'))
                ->rows(3)
                ->columnSpanFull(),
        ]);

        $this->action(function (Model $record, array $data): void {
            if (!$data['modify_details'] && !$data['modify_period']) {
                Notification::make()
                    ->title(__('No changes selected'))
                    ->body(__('Please select at least one type of amendment to request'))
                    ->warning()
                    ->send();
                return;
            }

            // Create the amendment record
            $amendment = new ApprenticeshipAmendment();
            $amendment->apprenticeship_id = $record->id;
            
            if ($data['modify_details']) {
                $amendment->title = $data['title'] ?? null;
                $amendment->description = $data['description'] ?? null;
            }
            
            if ($data['modify_period']) {
                $amendment->new_starting_at = $data['new_starting_at'] ? \Carbon\Carbon::createFromFormat('d/m/Y', $data['new_starting_at']) : null;
                $amendment->new_ending_at = $data['new_ending_at'] ? \Carbon\Carbon::createFromFormat('d/m/Y', $data['new_ending_at']) : null;
            }
            
            $amendment->reason = $data['reason'];
            $amendment->status = 'pending';
            $amendment->save();
            
            Notification::make()
                ->title(__('Amendment requested'))
                ->body(__('Your amendment has been submitted for approval'))
                ->success()
                ->send();
        });

        $this->visible(function (Model $record): bool {
            // Only allow amendments for apprenticeships that are not in Draft status
            // and that don't already have a pending amendment
            return $record->status !== 'Draft' && !$record->hasPendingAmendmentRequests();
        });
    }
}