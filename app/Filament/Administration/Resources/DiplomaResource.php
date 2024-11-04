<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\DiplomaResource\Pages;
use App\Filament\Core\BaseResource;
use App\Filament\Imports\DiplomaImporter;
use App\Models\Diploma;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

// use Tecnickcom\TCPDF as PDF;

use function Spatie\LaravelPdf\Support\pdf;

class DiplomaResource extends BaseResource
{
    protected static ?string $model = Diploma::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $modelLabel = 'Diploma';

    protected static ?string $pluralModelLabel = 'Diplomas';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Diplomas';

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
                Forms\Components\TextInput::make('defense_status')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->groups([
                Tables\Grouping\Group::make('defense_status')
                    ->collapsible()
                    ->label(__('Defense status')),
                Tables\Grouping\Group::make('is_foreign')
                    ->collapsible()
                    ->label(__('Foreign students')),
                Tables\Grouping\Group::make('is_deliberated')
                    ->collapsible()
                    ->label(__('Deliberated students')),
            ])
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
                Tables\Columns\TextColumn::make('defense_status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\CheckboxColumn::make('is_foreign')
                    ->label('Foreign student')
                    ->sortable(),
                // Tables\Columns\CheckboxColumn::make('is_deliberated')
                //     ->label('Deliberated')
                //     ->sortable(),
                Tables\Columns\CheckboxColumn::make('deliberation1_desision')
                    ->label('Deliberated 1')
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('deliberation2_desision')
                    ->label('Deliberated 2')
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('deliberation3_desision')
                    ->label('Deliberated 3')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('defense_status')
                    ->options(\App\Enums\DefenseStatus::class)
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $status): Builder => $query->where('defense_status', $status)
                        ),
                    ),
                Tables\Filters\SelectFilter::make('is_foreign')
                    ->options([
                        '1' => 'Foreign student',
                        '0' => 'Not foreign student',
                    ])
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $isForeign): Builder => $query->where('is_foreign', $isForeign)
                        ),
                    ),
                Tables\Filters\SelectFilter::make('deliberation1_desision')
                    ->options([
                        '1' => 'Deliberated',
                        '0' => 'Not deliberated',
                    ])
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $isDeliberated): Builder => $query->where('deliberation1_desision', $isDeliberated)
                        ),
                    )
                    ->label('Deliberated 1'),
                Tables\Filters\SelectFilter::make('deliberation2_desision')
                    ->options([
                        '1' => 'Deliberated',
                        '0' => 'Not deliberated',
                    ])
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $isDeliberated): Builder => $query->where('deliberation2_desision', $isDeliberated)
                        ),
                    )
                    ->label('Deliberated 2'),
                Tables\Filters\SelectFilter::make('deliberation3_desision')
                    ->options([
                        '1' => 'Deliberated',
                        '0' => 'Not deliberated',
                    ])
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query, $isDeliberated): Builder => $query->where('deliberation3_desision', $isDeliberated)
                        ),
                    )
                    ->label('Deliberated 3'),
                Tables\Filters\SelectFilter::make('council')
                    ->options(
                        Diploma::query()
                            ->select('council')
                            ->distinct()
                            ->get()
                            ->mapWithKeys(fn ($diploma) => [$diploma->council => $diploma->council])
                            ->toArray(),
                    )
                    ->searchable()
                    ->label('Council')
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['values'],
                            fn (Builder $query, $data): Builder => $query->whereIn('council', $data)
                        ),
                    )
                    ->multiple(),

                Tables\Filters\SelectFilter::make('program_code')
                    ->options(
                        Diploma::query()
                            ->select('program_code')
                            ->distinct()
                            ->get()
                            ->mapWithKeys(fn ($diploma) => [$diploma->program_code => $diploma->program_code])
                            ->toArray(),
                    )
                    ->searchable()
                    ->label('Program')
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['values'],
                            fn (Builder $query, $data): Builder => $query->whereIn('program_code', $data)
                        ),
                    )
                    ->multiple(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generate verification strings')
                        ->label('Generate verification strings')
                        // ->icon('heroicon-o-qrcode')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->generateVerificationLink();
                            }
                        }),
                ])
                    ->hidden(fn () => ! (auth()->user()->isSuperAdministrator())),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                Tables\Actions\BulkAction::make('generate-pdf-mpdf')
                    ->label('Generate PDF Recto')
                    ->icon('heroicon-o-document')
                    ->action(function ($records) {
                        $titlePositionY = 50;
                        $diploma_recto = Storage::disk('local')->path('document-templates/diploma-template-recto.pdf');
                        $foreign_diploma_recto = Storage::disk('local')->path('document-templates/foreign-diploma-recto.pdf');

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
                        // $mpdf->setSourceFile($diploma_recto);
                        // $tplId = $mpdf->importPage(1);
                        // $mpdf->useTemplate($tplId, ['adjustPageSize' => false]);
                        // $mpdf->SetPageTemplate($tplId);
                        $pageWidth = $mpdf->w;
                        $pageHeight = $mpdf->h;
                        $centerLeftHalf = $pageWidth / 4;
                        $centerRightHalf = ($pageWidth / 4) * 3;
                        $customMargin = 0;

                        $mpdf->SetSourceFile($foreign_diploma_recto);
                        $tpl_foreign_diploma_recto = $mpdf->importPage(1);
                        $mpdf->SetSourceFile($diploma_recto);
                        $tpl_diploma_recto = $mpdf->importPage(1);

                        $index = 0; // Initialize a counter
                        $totalItems = count($records); // Total number of items
                        foreach ($records as $record) {
                            $index++; // Increment counter at the start of each iteration
                            $loop = (object) [
                                'index' => $index, // Current iteration number
                                'first' => $index === 1, // True if first iteration
                                'last' => $index === $totalItems, // True if last iteration
                                'remaining' => $totalItems - $index, // Remaining items
                                'count' => $totalItems, // Total items
                            ];
                            // $svg = self::generateLinkQrSvg($record);
                            $svg = self::generateTextQrSvg($record);

                            if ($record->is_foreign) {
                                $mpdf->useTemplate($tpl_foreign_diploma_recto, ['adjustPageSize' => false]);
                                $mpdf->WriteText(34, $titlePositionY + 80, $record->birth_place_fr);
                            } else {
                                $mpdf->useTemplate($tpl_diploma_recto, ['adjustPageSize' => false]);
                                $mpdf->WriteText(57, $titlePositionY + 80, $record->birth_place_fr);
                            }

                            // $mpdf->SetXY(60, $titlePositionY + 65);
                            $mpdf->SetFont('DejaVuSans', 'Regular', 9);
                            // $startXPosition = self::calculateCenterPosition($mpdf, $record->council, $pageWidth) - 30;
                            $mpdf->WriteText(142, $titlePositionY + 61.3, $record->council);
                            $mpdf->SetFont('DejaVuSans', 'SemiBold', 10);
                            $mpdf->WriteText(55, $titlePositionY + 74, $record->full_name);
                            // $mpdf->SetXY(-99, $titlePositionY + 99);

                            $mpdf->WriteText(215, $titlePositionY + 74, $record->full_name_ar);
                            $mpdf->WriteText(220, $titlePositionY + 80, $record->birth_place_ar);
                            $startXPosition = self::calculateCenterPosition($mpdf, $record->birth_date, $pageWidth) + 35;
                            $mpdf->WriteText($startXPosition, $titlePositionY + 80, $record->birth_date);
                            $startXPosition = self::calculateCenterPosition($mpdf, $record->cin, $pageWidth) + 35;
                            $mpdf->WriteText($startXPosition, $titlePositionY + 86, $record->cin);
                            $startXPosition = self::calculateCenterPosition($mpdf, $record->cne, $pageWidth) + 35;
                            $mpdf->WriteText($startXPosition, $titlePositionY + 92, $record->cne);
                            // $mpdf->SetDirectionality('rtl');
                            $referenceSentence = $record->assigned_program . ' ' . $record->program_arabic;
                            $assigned_program = self::adjustSpacingForPDF($record->assigned_program, $record->program_arabic, $referenceSentence);
                            $leftMargin = 15;
                            $rightMargin = 15;
                            $startXPosition = self::calculateCenterPosition($mpdf, $assigned_program, $pageWidth, $leftMargin, $rightMargin);
                            $mpdf->WriteText($startXPosition, $titlePositionY + 103.5, $assigned_program);
                            $startXPosition = self::calculateCenterPosition($mpdf, '2024', $pageWidth) + 35;
                            $mpdf->WriteText($startXPosition, $titlePositionY + 110, '2024');
                            $mpdf->SetXY(19, 175);
                            $svg = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" />';
                            $mpdf->WriteHTML($svg);
                            if (! $loop->last) {
                                $mpdf->AddPage('L');
                                $mpdf->SetFont('DejaVuSans', 'SemiBold', 10);
                            }
                            // $mpdf->WriteHTML('<div style="text-align: left; direction: ltr;">السلام عليكم</div>');
                        }
                        $filename = 'mpdf-recto' . now() . '.pdf';
                        $mpdf->Output(Storage::disk('diplomas')->path($filename), 'F');

                        self::notifyDocumentCreated($filename);

                    })
                    ->hidden(fn () => ! (auth()->user()->isAdministrativeSupervisor(12) || auth()->user()->isAdministrator())),
                Tables\Actions\BulkAction::make('generate-pdf-verso-mpdf')
                    ->label('Generate PDF Verso')
                    ->color('success')
                    ->icon('heroicon-o-document')
                    ->action(function ($records) {
                        $titlePositionY = 50;
                        $diploma_verso = Storage::disk('local')->path('document-templates/diploma-template-verso.pdf');
                        $foreign_diploma_verso = Storage::disk('local')->path('document-templates/foreign-diploma-verso.pdf');
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

                        $mpdf->AddPage('L');
                        $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);

                        $pageWidth = $mpdf->w;
                        $pageHeight = $mpdf->h;
                        $centerLeftHalf = $pageWidth / 4;
                        $centerRightHalf = ($pageWidth / 4) * 3;
                        $customMargin = 0;

                        $mpdf->SetSourceFile($foreign_diploma_verso);
                        $tpl_foreign_diploma_verso = $mpdf->importPage(1);
                        $mpdf->SetSourceFile($diploma_verso);
                        $tpl_diploma_verso = $mpdf->importPage(1);

                        $index = 0; // Initialize a counter
                        $totalItems = count($records); // Total number of items

                        foreach ($records as $record) {
                            $index++; // Increment counter at the start of each iteration
                            $loop = (object) [
                                'index' => $index, // Current iteration number
                                'first' => $index === 1, // True if first iteration
                                'last' => $index === $totalItems, // True if last iteration
                                'remaining' => $totalItems - $index, // Remaining items
                                'count' => $totalItems, // Total items
                            ];
                            // $svg = self::generateLinkQrSvg($record);
                            $svg = self::generateTextQrSvg($record);

                            if ($record->is_foreign) {
                                $mpdf->useTemplate($tpl_foreign_diploma_verso, ['adjustPageSize' => false]);
                            } else {
                                $mpdf->useTemplate($tpl_diploma_verso, ['adjustPageSize' => false]);
                            }
                            if ($record->is_foreign) {
                                // $mpdf->SetXY(90, $titlePositionY + 65);
                                $mpdf->SetFont('DejaVuSans', 'Narrow', 7.5);
                                $mpdf->WriteText(123, $titlePositionY + 51, $record->council);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(35, $titlePositionY + 67, $record->full_name_ar);
                                $mpdf->WriteText(40, $titlePositionY + 74, $record->birth_place_ar);
                                // $mpdf->WriteText(115, $titlePositionY + 74, $record->birth_date);
                                // $mpdf->WriteText(55, $titlePositionY + 83, $record->cin);
                                //                                 $mpdf->WriteText(90, $titlePositionY + 90, $record->cne);

                                $mpdf->SetFont('DejaVuSans', 'Narrow', 7.5);
                                $mpdf->WriteText(196, $titlePositionY + 51.5, $record->council);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(200, $titlePositionY + 67, $record->full_name);
                                $mpdf->WriteText(172, $titlePositionY + 73.5, $record->birth_place_fr);
                                $mpdf->WriteText(257, $titlePositionY + 73.5, $record->birth_date);
                                $mpdf->WriteText(222, $titlePositionY + 79.5, $record->cin);
                                // $mpdf->WriteText(220, $titlePositionY + 89, $record->cne);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 7.5);

                                $mpdf->WriteText(28, $titlePositionY + 85, $record->program_tifinagh);
                                $mpdf->WriteText(174, $titlePositionY + 94, $record->program_english);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(38, $titlePositionY + 100.5, '2024');
                                $mpdf->WriteText(188, $titlePositionY + 102, '2024');

                                $mpdf->SetXY(19, 175);
                                $svg = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" />';
                                $mpdf->WriteHTML($svg);
                            } else {
                                $mpdf->SetFont('DejaVuSans', 'Narrow', 7.5);
                                $mpdf->WriteText(123, $titlePositionY + 54, $record->council);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(35, $titlePositionY + 70, $record->full_name_ar);
                                $mpdf->WriteText(45, $titlePositionY + 77, $record->birth_place_ar);
                                $mpdf->WriteText(115, $titlePositionY + 77, $record->birth_date);
                                $mpdf->WriteText(55, $titlePositionY + 83, $record->cin);
                                $mpdf->WriteText(90, $titlePositionY + 90, $record->cne);
                                $mpdf->WriteText(28, $titlePositionY + 104, $record->program_tifinagh);
                                $mpdf->WriteText(35, $titlePositionY + 110, '2024');

                                $mpdf->SetFont('DejaVuSans', 'Narrow', 7.5);
                                $mpdf->WriteText(196, $titlePositionY + 54.5, $record->council);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(200, $titlePositionY + 70, $record->full_name);
                                $mpdf->WriteText(180, $titlePositionY + 76, $record->birth_place_fr);
                                $mpdf->WriteText(257, $titlePositionY + 76, $record->birth_date);
                                $mpdf->WriteText(222, $titlePositionY + 83, $record->cin);
                                $mpdf->WriteText(220, $titlePositionY + 89, $record->cne);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 7.5);

                                $mpdf->WriteText(174, $titlePositionY + 105.3, $record->program_english);
                                $mpdf->SetFont('DejaVuSans', 'NarrowBold', 9);
                                $mpdf->WriteText(185, $titlePositionY + 110, '2024');

                                $mpdf->SetXY(19, 175);
                                $svg = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '" />';
                                $mpdf->WriteHTML($svg);
                            }
                            if (! $loop->last) {
                                $mpdf->AddPage('L');
                                $mpdf->SetFont('DejaVuSans', 'SemiBold', 10);

                            }
                            // $mpdf->WriteHTML('<div style="text-align: left; direction: ltr;">السلام عليكم</div>');
                        }
                        $filename = 'mpdf-verso' . now() . '.pdf';
                        $mpdf->Output(Storage::disk('diplomas')->path($filename), 'F');

                        self::notifyDocumentCreated($filename);

                    })
                    ->hidden(fn () => ! (auth()->user()->isAdministrativeSupervisor(12) || auth()->user()->isAdministrator())),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(DiplomaImporter::class),
                Tables\Actions\Action::make('Sync with Defenses')
                    // ->confirm('Are you sure you want to sync the diplomas with defenses?')
                    ->action(function () {
                        Diploma::syncWithDefenses();
                    }),
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

    private static function adjustSpacingForPDF($assignedProgram, $programArabic, $referenceSentence)
    {
        // Calculate the length of the reference sentence
        $referenceLength = mb_strlen($referenceSentence);

        // Calculate the lengths of the individual parts
        $assignedProgramLength = mb_strlen($assignedProgram);
        $programArabicLength = mb_strlen($programArabic);

        // Combine the assigned program and Arabic program with a base spacing
        $baseSpacing = '      '; // 14 spaces as the base
        $combinedSentence = $assignedProgram . $baseSpacing . $programArabic;
        $combinedLength = mb_strlen($combinedSentence);

        // Calculate the difference in length
        $lengthDifference = $referenceLength - $combinedLength;

        // Adjust the calculation for additional spaces to account for the Arabic part's length
        // This increases the additional spaces for shorter Arabic parts
        $scalingFactor = ($assignedProgramLength / ($programArabicLength - 90)) * 0.2; // Adjust this factor as needed

        // Calculate the number of additional spaces needed
        $additionalSpacesCount = max(0, (int) ($lengthDifference / $scalingFactor));

        // $scalingFactor = 20; // Number of characters in the combined sentence per additional space

        // // Calculate the number of additional spaces needed
        // $additionalSpacesCount = max(0, (int) ($lengthDifference / $scalingFactor));

        // Create the additional spaces string
        $additionalSpaces = str_repeat(' ', $additionalSpacesCount);

        // Combine the sentences with the adjusted spacing
        $adjustedSentence = $assignedProgram . $baseSpacing . $additionalSpaces . $programArabic;

        return $adjustedSentence;
    }

    private static function calculateCenterPosition($mpdf, $text, $pageWidth, $leftMargin = 40, $rightMargin = 40)
    {
        // Calculate the width of the text
        $textWidth = $mpdf->GetStringWidth($text);

        // Calculate the usable page width
        $usablePageWidth = $pageWidth - $leftMargin - $rightMargin;

        // Calculate the starting X position for the text to be centered
        $occupiedWidth = $textWidth + $leftMargin + $rightMargin;
        $startXPosition = ($usablePageWidth - $occupiedWidth * 0.8) / 2 + $leftMargin;
        // dd($startXPosition);

        return $startXPosition;
    }

    public static function generateLinkQrSvg($record)
    {
        $errorCorrectionLevel = ErrorCorrectionLevel::M();
        $qrLink = $record->generateVerificationLink();
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(60, 0, null, null, null),
                new SvgImageBackEnd
            )
        ))->writeString($qrLink);

        return $svg;
    }

    public static function generateTextQrSvg($record)
    {
        $errorCorrectionLevel = ErrorCorrectionLevel::M();
        $qrLink = $record->generateTextForQrCode();
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(60, 0, null, null, null),
                new SvgImageBackEnd
            )
        ))->writeString($qrLink);

        return $svg;
    }

    public static function notifyDocumentCreated($filename)
    {
        \Filament\Notifications\Notification::make()
            ->title('PDF generated successfully')
            ->success()
            ->actions([
                \Filament\Notifications\Actions\Action::make('Download')
                    ->url(URL::to(Storage::disk('diplomas')->url($filename)), shouldOpenInNewTab: true),
            ])
            ->send();
    }
}
