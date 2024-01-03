<?php
namespace App\Filament\Resources\InternshipResource\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;
use App\Models\Internship;
class ReviewAction extends Action
{

    public static function getDefaultName(): string
    {
        return 'review';
    }

    public function handle(Internship $internship): void
    {
        $internship->review();
    }

    public function getTableActions(){
return [
        Action::make('ReviewInternship')
        ->fillForm(fn (Internship $record): array => [
            'internshipId' => $record->student->id,
        ])
        ->form([
        ])
        ->action(function (array $data, Internship $record): void {
            //  return carbon object with this format 2024-01-02 15:40:05, its a datetime format i mysql database
            $record->reviewed_at = Carbon::now()->format('yy-m-d H:i:s');
            $record->save();
        })
    // Add action to review an internship
    // ReviewAction::make('Review Internship'),
    // Tables\Actions\EditAction::make(),
    // Tables\Actions\DeleteAction::make(),
    // Tables\Actions\ForceDeleteAction::make(),
    // Tables\Actions\RestoreAction::make(),
    ];
    }
}