<?php

namespace App\Filament\Actions\Action\Processing;

use App\Models\DocumentTemplate;
use App\Services\UrlService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use stdClass;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateExampleDocumentPdfAction extends Action
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function (array $data, DocumentTemplate $documentTemplate): void {
            if ($documentTemplate->template_type == 'sheet') {

                $template_view = 'pdf.templates.' . $documentTemplate->level->value . '.Defenses.evaluation_sheet';
                $pdf_path = 'storage/document_models/defenses/' . $documentTemplate->level->value;
                $pdf_file_name = 'evaluation-sheet' . Str::slug('example') . '-' . time() . hash('sha256', 123) . '.pdf';

                if (! File::exists($pdf_path)) {
                    File::makeDirectory($pdf_path, 0755, true);
                }
                pdf()
                    ->view($template_view)
                    ->save($pdf_path . '/' . $pdf_file_name)
                    ->name($pdf_file_name);

                $documentTemplate->example_url = $pdf_path . '/' . $pdf_file_name;
                $documentTemplate->save();

                return;
            }
            // Générer l'URL de vérification
            $verificationString = 'TestRecord-TestRecord';
            $encodedUrl = UrlService::encodeUrl($verificationString);
            $verificationUrl = URL::to('/verify-agreement?x=' . $encodedUrl);

            // Générer le QR code
            $qrCodeSvg = UrlService::getQrCodeSvg($verificationUrl);

            // Génération du PDF
            $template_view = 'pdf.templates.' . $documentTemplate->level->value . '.agreement_template';
            $pdf_path = 'storage/pdf/example_agreements/' . $documentTemplate->level->value;
            $pdf_file_name = 'convention-de-stage-' . Str::slug('example') . '-' . time() . hash('sha256', 123) . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $internship = [
                'student' => [
                    'long_full_name' => 'Nom et prénom de l\'étudiant',
                    'full_name' => 'Nom complet de l\'étudiant',
                    'phone' => '0666666666',
                    'email' => 'student@school.org',
                    'program' => 'Filière de l\'étudiant',
                    'program_coordinator' => [
                        'full_name' => 'Program coordinator full name',
                        'phone' => '0666666666',
                        'email' => 'coordinator@school.org',
                    ],
                ],
                'organization' => [
                    'name' => 'Organisation name',
                    'address' => 'Organisation address',
                    'city' => 'Organisation city',
                    'country' => 'Organisation country',
                    'postal_code' => '999999999',
                    'phone' => '0666666666',
                    'fax' => '0666666666',
                    'email' => 'email@organization.com',
                    'website' => '......................',
                    'legal_representative' => '......................',
                    'legal_representative_function' => '......................',
                ],
                'supervisor' => [
                    'full_name' => 'Supervisor full name',
                    'function' => 'Supervisor function',
                    'phone' => '0666666666',
                    'email' => 'supervisor@school.org',
                ],
                'parrain' => [
                    'full_name' => 'parrain full name',
                    'function' => 'parrain function',
                    'phone' => '0666666666',
                    'email' => 'parrain@entreprise.com',
                ],
                'tutor' => [
                    'full_name' => 'tutor full name',
                    'function' => 'tutor function',
                    'phone' => '0666666666',
                    'email' => 'tutor@school.org',
                ],
                'title' => 'Stage de fin d\'études',
                'description' => 'description du stage de fin d\'études',
                'duration' => '3 mois',
                'starting_at' => '01/01/2021',
                'ending_at' => '01/04/2021',
                'remuneration' => '1000 DH',
                'working_hours' => '8h - 17h',
                'working_days' => 'Lundi - Vendredi',
                'office_location' => 'adresse du bureau ou le stagiaire va travailler',
            ];

            function array_to_object($array)
            {
                $obj = new stdClass;
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        $obj->{$k} = array_to_object($v);
                    } else {
                        $obj->{$k} = $v;
                    }
                }

                return $obj;
            }

            $internship = array_to_object($internship);

            $chromePath = env('BROWSERSHOT_CHROME_PATH');
            pdf()
                ->view($template_view, [
                    'internship' => $internship,
                    'qrCodeSvg' => $qrCodeSvg,
                ])
                ->save($pdf_path . '/' . $pdf_file_name)
                ->name($pdf_file_name);

            $documentTemplate->example_url = $pdf_path . '/' . $pdf_file_name;
            $documentTemplate->save();

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
