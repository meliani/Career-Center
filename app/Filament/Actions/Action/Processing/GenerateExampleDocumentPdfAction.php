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

                $template_view = 'pdf.templates.' . $documentTemplate->level . '.Defenses.evaluation_sheet';
                $pdf_path = 'storage/document_models/defenses/' . $documentTemplate->level;
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
            $template_view = 'pdf.templates.' . $documentTemplate->level . '.agreement_template';
            $pdf_path = 'storage/pdf/example_agreements/' . $documentTemplate->level;
            $pdf_file_name = 'convention-de-stage-' . Str::slug('example') . '-' . time() . hash('sha256', 123) . '.pdf';

            if (! File::exists($pdf_path)) {
                File::makeDirectory($pdf_path, 0755, true);
            }

            $internship = [
                'student' => [
                    'long_full_name' => '........ .........',
                    'full_name' => '........ .........',
                    'level' => '........',
                    'program' => '........',
                    'email' => '......................',
                    'phone' => '............',
                    'address' => '......................',
                    'city' => '............',
                    'postal_code' => '............',
                    'birth_date' => '............',
                    'birth_place' => '............',
                ],
                'organization' => [
                    'name' => '............',
                    'address' => '......................',
                    'city' => '............',
                    'postal_code' => '............',
                    'phone' => '............',
                    'fax' => '............',
                    'email' => '......................',
                    'website' => '......................',
                    'legal_representative' => '......................',
                    'legal_representative_function' => '......................',
                ],
                'supervisor' => [
                    'full_name' => '........ .........',
                    'function' => '............',
                    'phone' => '............',
                    'email' => '......................',
                ],
                'parrain' => [
                    'full_name' => '........ .........',
                    'function' => '............',
                    'phone' => '............',
                    'email' => '......................',
                ],
                'tutor' => [
                    'full_name' => '........ .........',
                    'function' => '............',
                    'phone' => '............',
                    'email' => '......................',
                ],
                'title' => '............',
                'description' => '............',
                'keywords' => '............',
                'starting_at' => '............',
                'ending_at' => '............',
                'remuneration' => '............',
                'currency' => '............',
                'workload' => '............',
                'observations' => '............',
                'duration_in_weeks' => '............',
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
