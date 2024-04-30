<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;

class StudentsImport implements SkipsEmptyRows, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithProgressBar, WithUpsertColumns, WithUpserts
{
    use Importable;

    public function model(array $row)
    {

        $row = array_map('trim', $row);
        // convert from anything to numberformat, clumn might be formatted as text or having commas
        $row['id'] = is_numeric($row['id']) ? $row['id'] : (int) str_replace(',', '', $row['id']);
        $title = $row['title'] == 'M.' ? 'Mr' : ($row['title'] == 'Mme.' ? 'Mrs' : $row['title']);
        $level = $row['level'] == 'deuxième année' ? 'SecondYear' : ($row['level'] == ('première année' || $row['level'] == 'Première année') ? 'FirstYear' : $row['level']);
        $program = str_replace(['SUD-CLOUD&IoT', 'SESNum', 'SMART-ICT'], ['SUD', 'SESNUM', 'SMART-ICT'], $row['program']);
        $email = str_replace(['@ine.inpt.ma', '@inemail.ine.inpt.ma'], ['@ine.inpt.ac.ma', '@ine.inpt.ac.ma'], $row['email']);

        return new Student([
            'id' => $row['id'],
            'title' => $title,
            'first_name' => $row['prenom'],
            'last_name' => $row['nom'],
            'level' => $level,
            'program' => $program,
            'email' => $email,
            'year_id' => \App\Models\Year::current()->id,
            'is_verified' => 1,
            'email_verified_at' => now(),
        ]);
    }

    public function uniqueBy()
    {
        return 'email';
    }

    public function upsertColumns()
    {
        return ['program', 'level', 'title', 'first_name', 'last_name', 'year_id', 'is_verified', 'email_verified_at'];
    }

    public function chunkSize(): int
    {
        return 5;
    }

    public function batchSize(): int
    {
        return 5;
    }

    public function rules(): array
    {
        return [];
        /*         return [
                    '1' => Rule::in(['patrick@maatwebsite.nl']),

                    // Above is alias for as it always validates in batches
                    '*.1' => Rule::in(['patrick@maatwebsite.nl']),

                    // Can also use callback validation rules
                    '0' => function ($attribute, $value, $onFailure) {
                        if ($value !== 'Patrick Brouwers') {
                            $onFailure('Name is not Patrick Brouwers');
                        }
                    },
                ]; */
    }
}
