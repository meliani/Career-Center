<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\Apprenticeship;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
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
            $pdf_path = 'storage/pdf/' . Str::slug($apprenticeship->student->name) . '-internship-agreement-' . time() . '.pdf';
            pdf()
                ->view('pdf.templates.FirstYear.apprenticeship_agreement', ['internship' => $apprenticeship])
                ->name('InternshipAgreement.pdf')
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
                    $pdf_path
                    // 'storage/pdf/app.pdf'
                    // )
                );

            // $apprenticeship->pdf_path = 'pdf/' . Str::slug($apprenticeship->student->name) . '-internship-agreement-' . time() . '.pdf';

            // $apprenticeship->save();

            // Notification::make()
            //     ->title('Internship Agreement has been generated successfully')
            //     ->success()
            //     ->send();

        });

        return $static;
    }
}
