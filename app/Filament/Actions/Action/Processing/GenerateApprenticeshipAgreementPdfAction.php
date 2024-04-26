<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\Apprenticeship;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateApprenticeshipAgreementPdfAction extends Action
{
    public static function make(?string $name = null): static
    {

        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, Apprenticeship $apprenticeship): void {
            $apprenticeship = $apprenticeship->load('student', 'organization');
            $template_view = 'pdf.templates.' . $apprenticeship->student->level->value . '.apprenticeship_agreement';
            $pdf_path = 'storage/pdf/apprenticeship_agreements/' . $apprenticeship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($apprenticeship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->withBrowsershot(function (Browsershot $browsershot) use ($chromePath) {
                    $browsershot
                        ->setChromePath($chromePath);
                })
                ->view($template_view, ['internship' => $apprenticeship])
                    // ->name('InternshipAgreement.pdf')
                    // ->withBrowsershot(function (Browsershot $browsershot) {
                    //     $browsershot
                    //         // ->scale(0.5)
                    //         ->noSandbox()
                    //         ->setNodeBinary('/home/mo/.nvm/versions/node/v20.3.0/bin/node')
                    //         ->setNpmBinary('/home/mo/.nvm/versions/node/v20.3.0/bin/npm');
                    //     // ->setBinPath('/usr/bin/chromium-browser');
                    // })
                ->save(
                    // storage_path(
                    $pdf_path . '/' . $pdf_file_name
                    // 'storage/pdf/app.pdf'
                    // )
                )
                ->name($pdf_file_name);

            $apprenticeship->pdf_path = $pdf_path;
            $apprenticeship->pdf_file_name = $pdf_file_name;
            $apprenticeship->save();

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
