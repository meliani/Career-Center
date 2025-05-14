<?php

namespace App\Filament\Actions\Page;

use App\Models\FinalYearInternshipAgreement;
use App\Services\UrlService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateFinalYearInternshipAgreementPageAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generateFinalYearInternshipAgreement';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (array $data, FinalYearInternshipAgreement $record): void {
            $internship = $record->load('student', 'organization', 'externalSupervisor');
            $template_view = 'pdf.templates.' . $internship->student->level->value . '.agreement_template';
            $pdf_path = 'storage/pdf/internship_agreements/' . $internship->student->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug($internship->student->full_name) . '-' . time() . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            // Generate verification string and get verification URL
            $verification_string = UrlService::encodeShortUrl($internship->id);
            $verification_url = route('internship-agreement.verify', $verification_string);
            
            // Save the verification string to the internship record
            $internship->verification_string = $verification_string;
            $internship->save();

            // Generate QR code for verification
            $qrCodeSvg = UrlService::getQrCodeSvg($verification_url);

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, [
                    'internship' => $internship,
                    'qrCodeSvg' => $qrCodeSvg,
                ])
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
    }
}
