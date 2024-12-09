<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\FinalYearInternshipAgreement;
use App\Services\UrlService;
// use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str; // Ajouter ce use

use function Spatie\LaravelPdf\Support\pdf;

class GenerateInternshipAgreementAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, FinalYearInternshipAgreement $FinalYearInternship): void {
            $FinalYearInternship = $FinalYearInternship->load('student', 'organization');

            // Determine template based on organization's country
            $template_view = 'pdf.templates.' . $FinalYearInternship->student->level->value . '/';
            if ($FinalYearInternship->organization->country === 'France') {
                $template_view .= 'france_agreement_template';
            } else {
                $template_view .= $FinalYearInternship->student->level->value . '.agreement_template';
            }

            $pdf_path = 'storage/pdf/apprenticeship_agreements/' . $FinalYearInternship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($FinalYearInternship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $veriication_link = $FinalYearInternship->generateVerificationLink();
            // dd($veriication_link);
            // Générer le QR code
            $qrCodeSvg = UrlService::getQrCodeSvg($veriication_link);

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, [
                    'internship' => $FinalYearInternship,
                    'qrCodeSvg' => $qrCodeSvg, // Ajouter le QR code à la vue
                ])
                ->save(
                    $pdf_path . '/' . $pdf_file_name
                )
                ->name($pdf_file_name);

            $FinalYearInternship->pdf_path = $pdf_path;
            $FinalYearInternship->pdf_file_name = $pdf_file_name;
            $FinalYearInternship->save();

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
