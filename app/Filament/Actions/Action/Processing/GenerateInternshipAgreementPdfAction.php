<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\Internship;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateInternshipAgreementPdfAction extends Action
{
    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, Internship $internship): void {
            $internship = $internship->load('student', 'organization');
            $template_view = 'pdf.templates.' . $internship->student->level->value . '.internship_agreement';
            $pdf_path = 'storage/pdf/internship_agreements/' . $internship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($internship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, ['internship' => $internship])
                ->save(
                    $pdf_path . '/' . $pdf_file_name
                )
                ->name($pdf_file_name);

            $internship->pdf_path = $pdf_path;
            $internship->pdf_file_name = $pdf_file_name;
            $internship->save();

            Notification::make()
                ->title('Internship Agreement has been generated successfully')
                ->success()

                ->actions([
                    \Filament\Notifications\Actions\Action::make('Download')
                        ->url(URL::to($pdf_path . '/' . $pdf_file_name), shouldOpenInNewTab: true),
                ])
                ->send();

        });

        return $static;
    }
}
