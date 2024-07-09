<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\DiplomaResource\Pages;
use App\Filament\Core\BaseResource;
use App\Filament\Imports\DiplomaImporter;
use App\Models\Diploma;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
// use setasign\Fpdi\Fpdi;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use setasign\Fpdi\Tfpdf\Fpdi;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Orientation;

// use Tecnickcom\TCPDF as PDF;

use function Spatie\LaravelPdf\Support\pdf;

class DiplomaResource extends BaseResource
{
    protected static ?string $model = Diploma::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $modelLabel = 'Diploma';

    protected static ?string $pluralModelLabel = 'Diplomas';

    protected static ?string $navigationGroup = 'INPT';

    public static function canAccess(): bool
    {
        if (auth()->check()) {

            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrativeSupervisor(12);
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrativeSupervisor(12);
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('registration_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cne')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('birth_place_ar')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('birth_place_fr')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('birth_date')
                    ->required(),
                Forms\Components\TextInput::make('nationality')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('council')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('program_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('assigned_program')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('program_tifinagh')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('program_english')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('program_arabic')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('qr_code')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cne')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_place_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_place_fr')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    // ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('council')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_tifinagh')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_english')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_arabic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_code')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generate-pdf')
                        ->label('Generate PDF')
                        ->icon('heroicon-o-document')
                        ->action(function ($records) {
                            $pdf = pdf()->view('pdf.templates.ThirdYear.diploma', ['records' => $records]);
                            $pdf->disk('diplomas')
                                ->orientation(Orientation::Landscape)
                                ->format(Format::A4)
                                ->save('diploma.pdf');
                        }),
                    Tables\Actions\BulkAction::make('generate-pdf-fpdi')
                        ->label('Generate PDF FPDI')
                        ->icon('heroicon-o-document')
                        ->action(function ($records) {
                            $titlePositionY = 35;

                            // Initialize FPDI
                            // $pdf = new Fpdi();
                            // $pdf = new Tfpdf('L', 'mm', true, 'UTF-8', false);
                            $pdf = new Fpdi('L', 'mm');
                            $pdf->AddPage('L'); // 'L' for Landscape orientation

                            // Specify the path to your background PDF
                            $backgroundPdfPath = Storage::disk('local')->path('document-templates/diploma-template.pdf');
                            // Set the source file
                            $pageCount = $pdf->setSourceFile($backgroundPdfPath);
                            // Import the first page of the PDF
                            $tplId = $pdf->importPage(1);
                            $pdf->useTemplate($tplId, ['adjustPageSize' => true]);

                            // $pdf->AddFont('DejaVuSans', 'Bold', Storage::disk('local')->path('fonts/dejavu/DejaVuSans-Bold.ttf'), true);
                            $pdf->AddFont('DejaVuSans', 'Bold', 'DejaVuSans-Bold.ttf', true);
                            // $pdf->SetFont('Arial', '', 12);
                            $pdf->SetFont('DejaVuSans', 'Bold', 14);
                            // Loop through your records and add them to the PDF
                            foreach ($records as $record) {
                                $pdf->SetXY(45, $titlePositionY + 73);
                                $pdf->Write(0, $record->full_name);
                                $pdf->SetXY(-10, $titlePositionY + 73);
                                $pdf->Write(0, 'السلام عليكم', '', 0, 'L', true, 0, false, false, 0);

                                // $pdf->Write(0, $record->full_name_ar);
                                // $pdf->Cell(100, 20, $record->full_name_ar, 0, 1, 'C');
                                // $pdf->SetRightMargin(10); // Adjust according to your needs
                                // $pdf->SetX(-10, true); // Move to the right edge
                                // $pdf->Cell(0, 10, 'مرحبا بالعالم', 0, 1, 'R'); // 'R' for right alignment
                                $reversedText = mb_convert_encoding($record->full_name_ar, 'UTF-16BE', 'UTF-8');
                                // $reversedText = strrev($record->full_name_ar);
                                $reversedText = strrev($reversedText);
                                $reversedText = mb_convert_encoding($reversedText, 'UTF-8', 'UTF-16LE');
                                $pdf->Write(-10, $reversedText);

                                // $pdf->Write(0, utf8_decode($record->full_name_ar));
                                // $pdf->Write(10, mb_convert_encoding($record->full_name_ar, 'windows-1252', 'utf-8'));
                                // $pdf->Write(15, iconv('utf-8', 'windows-1252', $record->full_name_ar));
                                // dd($record->full_name_ar);

                            }

                            // Save the PDF to a file in the 'diplomas' disk
                            $pdfPath = Storage::disk('diplomas')->path('diploma-fpdi.pdf');
                            $pdf->Output($pdfPath, 'F');
                        }),
                    Tables\Actions\BulkAction::make('generate-pdf-Tcpdf')
                        ->label('Generate PDF TCPDF')
                        ->icon('heroicon-o-document')
                        ->action(function ($records) {
                            $backgroundPdfPath = Storage::disk('local')->path('document-templates/diploma-template.pdf');
                            // PDF::SetTitle('Hello World');
                            PDF::SetFont('DejaVuSans', '', 14);
                            PDF::AddPage('L');
                            PDF::setSourceFile($backgroundPdfPath);
                            $tplId = PDF::importPage(1);
                            PDF::useTemplate($tplId, ['adjustPageSize' => true]);
                            foreach ($records as $record) {
                                PDF::SetXY(45, $titlePositionY + 73);
                                PDF::Write(0, $record->full_name);
                                PDF::SetXY(-10, $titlePositionY + 73);
                                PDF::Write(0, $record->full_name_ar);

                                PDF::Output(Storage::disk('diplomas')->path('tcpdf.pdf'), 'F');
                            }
                        }),
                ])
                    ->hidden(fn () => ! (auth()->user()->isSuperAdministrator())),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                Tables\Actions\BulkAction::make('generate-pdf-mpdf')
                    ->label('Generate PDF mPDF')
                    ->icon('heroicon-o-document')
                    ->action(function ($records) {
                        $titlePositionY = 50;
                        $backgroundPdfPath = Storage::disk('local')->path('document-templates/diploma-template.pdf');
                        $mpdf = new \Mpdf\Mpdf
                        // ();
                        ([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'orientation' => 'L',
                            // 'unit' => 'px',
                            'margin_left' => 0,
                            'margin_right' => 0,
                            'margin_top' => 0,
                            'margin_bottom' => 0,
                            'margin_header' => 0,
                            'margin_footer' => 0,
                        ]);
                        // $mpdf->SetImportUse();
                        // $mpdf->SetImportPage(1);
                        // $mpdf->WriteHTML(file_get_contents($backgroundPdfPath));
                        $mpdf->AddPage('L');
                        $mpdf->SetFont('DejaVuSans', 'SemiBold', 10);
                        $mpdf->setSourceFile($backgroundPdfPath);
                        $tplId = $mpdf->importPage(1);
                        $mpdf->useTemplate($tplId, ['adjustPageSize' => false]);
                        // $mpdf->SetPageTemplate($tplId);
                        foreach ($records as $record) {
                            $qrLink = $record->generateVerificationLink();
                            $svg = (new Writer(
                                new ImageRenderer(
                                    new RendererStyle(60, 0, null, null, null),
                                    new SvgImageBackEnd()
                                )
                            ))->writeString($qrLink);

                            $mpdf->SetXY(90, $titlePositionY + 65);
                            $mpdf->WriteText(135, $titlePositionY + 62, $record->council);
                            $mpdf->WriteText(48, $titlePositionY + 74, $record->full_name);
                            // $mpdf->SetXY(-99, $titlePositionY + 99);
                            $mpdf->WriteText(215, $titlePositionY + 74, $record->full_name_ar, '', 0, 'R', true, 0, false, false, 0);
                            $mpdf->WriteText(40, $titlePositionY + 80, $record->birth_place_fr);
                            $mpdf->WriteText(230, $titlePositionY + 80, $record->birth_place_ar, '', 0, 'R', true, 0, false, false, 0);
                            // $mpdf->WriteText(136, $titlePositionY + 80, $record->birth_date->format('d/m/Y'));
                            $mpdf->WriteText(136, $titlePositionY + 80, $record->birth_date);
                            $mpdf->WriteText(136, $titlePositionY + 86, $record->cin);
                            $mpdf->WriteText(136, $titlePositionY + 92, $record->cne, 0, 0, 'R');
                            $mpdf->WriteText(32, $titlePositionY + 103, $record->assigned_program, '', 0, 'L', true, 0, false, false, 0);
                            $mpdf->WriteText(247, $titlePositionY + 103, $record->program_arabic, '', 0, 'R', true, 0, false, false, 0);
                            // well add a qrcode
                            $mpdf->SetXY(18, 176);
                            $svg = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" />';
                            $mpdf->WriteHTML($svg);
                            $mpdf->AddPage('L');
                            $mpdf->SetFont('DejaVuSans', 'SemiBold', 10);
                            $mpdf->useTemplate($tplId, ['adjustPageSize' => false]);
                            // $mpdf->WriteHTML('<div style="text-align: left; direction: ltr;">السلام عليكم</div>');

                        }
                        $mpdf->Output(Storage::disk('diplomas')->path('mpdf.pdf'), 'F');
                        \Filament\Notifications\Notification::make()
                            ->title('PDF generated successfully')
                            ->success()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('Download')
                                    ->url(URL::to(Storage::disk('diplomas')->url('mpdf.pdf')), shouldOpenInNewTab: true),
                            ])
                            ->send();

                    })
                    ->hidden(fn () => ! (auth()->user()->isAdministrativeSupervisor(12) || auth()->user()->isAdministrator())),

            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(DiplomaImporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiplomas::route('/'),
            'create' => Pages\CreateDiploma::route('/create'),
            'view' => Pages\ViewDiploma::route('/{record}'),
            'edit' => Pages\EditDiploma::route('/{record}/edit'),
        ];
    }
}
