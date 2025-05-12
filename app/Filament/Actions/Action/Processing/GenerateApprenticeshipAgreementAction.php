<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\Apprenticeship;
use App\Services\UrlService;
// use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str; // Ajouter ce use

use function Spatie\LaravelPdf\Support\pdf;

class GenerateApprenticeshipAgreementAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, Apprenticeship $apprenticeship): void {
            $apprenticeship = $apprenticeship->load('student', 'organization');
            $template_view = 'pdf.templates.' . $apprenticeship->student->level->value . '.agreement_template';
            $pdf_path = 'storage/pdf/apprenticeship_agreements/' . $apprenticeship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($apprenticeship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            // Generate verification string and get verification URL
            $verification_string = \App\Services\UrlService::encodeShortUrl($apprenticeship->id);
            $verification_url = route('internship-agreement.verify', $verification_string);
            
            // Save the verification string to the apprenticeship record
            $apprenticeship->verification_string = $verification_string;
            $apprenticeship->save();

            // Generate QR code for verification
            $qrCodeSvg = UrlService::getQrCodeSvg($verification_url);

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, [
                    'internship' => $apprenticeship,
                    'qrCodeSvg' => $qrCodeSvg, // Ajouter le QR code à la vue
                ])
                ->save(
                    $pdf_path . '/' . $pdf_file_name
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
