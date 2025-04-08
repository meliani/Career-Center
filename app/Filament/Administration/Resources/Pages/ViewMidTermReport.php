<?php

namespace App\Filament\Administration\Resources\MidTermReportResource\Pages;

use App\Filament\Administration\Resources\MidTermReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewMidTermReport extends ViewRecord
{
    protected static string $resource = MidTermReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('toggle_read')
                ->label(fn () => $this->record->is_read_by_supervisor ? 'Mark as Unread' : 'Mark as Read')
                ->icon(fn () => $this->record->is_read_by_supervisor ? 'heroicon-o-x-mark' : 'heroicon-o-check')
                ->color(fn () => $this->record->is_read_by_supervisor ? 'gray' : 'success')
                ->action(function () {
                    $newStatus = ! $this->record->is_read_by_supervisor;
                    $this->record->update([
                        'is_read_by_supervisor' => $newStatus,
                    ]);

                    $status = $newStatus ? 'read' : 'unread';

                    Notification::make()
                        ->success()
                        ->title("Report marked as {$status}")
                        ->send();

                    // Redirect to self to refresh the page
                    return $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),
        ];
    }
}
