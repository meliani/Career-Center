<?php

namespace App\Filament\Actions\Action\Processing;

// use Filament\Forms\Components\Actions\Action;
use App\Enums;
use App\Services\UrlService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model; // Ajouter ce use
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateInternshipAgreementAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, Model $FinalYearInternship): void {
            $FinalYearInternship = $FinalYearInternship->load('student', 'organization');
            $template_view = 'pdf.templates.' . $FinalYearInternship->student->level->value . '.';

            if ($FinalYearInternship->student->is_mobility) {
                // stop the process and notify the user
                if ($FinalYearInternship->student->exchangePartner->country === 'FR') {
                    $template_view .= '.exchange_france_agreement_template';
                } else {
                    Notification::make()
                        ->title('Error generating document, Please contact the administration')
                        ->warning()
                        ->send();

                    return;
                }

            } else {
                // Determine template based on organization's country
                if ($FinalYearInternship->organization->country === 'France') {
                    $template_view .= 'france_agreement_template';
                } else {
                    $template_view .= 'agreement_template';
                }
            }
            $pdf_path = 'storage/pdf/apprenticeship_agreements/' . $FinalYearInternship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($FinalYearInternship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $verication_link = $FinalYearInternship->generateVerificationLink();

            $qrCodeSvg = UrlService::getQrCodeSvg($verication_link);

            if ($FinalYearInternship->status === Enums\Status::Draft) {
                // Option 1: Pass a watermark variable to your Blade view
                $watermark = 'DRAFT';
            }

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, [
                    'internship' => $FinalYearInternship,
                    'qrCodeSvg' => $qrCodeSvg,
                    'watermark' => $watermark ?? null,
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
