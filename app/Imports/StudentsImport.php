<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Str;
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
        $year_id = \App\Models\Year::current()->id;

        $row = array_map('trim', $row);
        $row['name'] = trim($row['name'], "'");
        // Split the name into an array
        $nameParts = explode(' ', $row['name']);

        // The last name is the first element of the array
        $last_name = array_shift($nameParts);

        // The first name is the rest of the array
        $first_name = implode(' ', $nameParts);
        $title = $row['title'] == 1 ? 'Mr' : 'Mrs';

        // $row['id'] = is_numeric($row['id']) ? $row['id'] : (int) str_replace(',', '', $row['id']);
        // $title = $row['title'] == 'M.' ? 'Mr' : ($row['title'] == 'Mme.' ? 'Mrs' : $row['title']);
        $level = $row['level'] == 'INE2' ? 'SecondYear' : ($row['level'] == ('INE1') ? 'FirstYear' : $row['level']);
        // $program = str_replace(['SUD-CLOUD&IoT', 'SESNum', 'SMART-ICT'], ['SUD', 'SESNUM', 'SMART-ICT'], $row['program']);
        $program = $row['program'];
        $email = $row['email'];
        // $email = str_replace(['@ine.inpt.ma', '@inemail.ine.inpt.ma'], ['@ine.inpt.ac.ma', '@ine.inpt.ac.ma'], $row['email']);

        return new Student([
            // 'id' => $row['id'],
            'title' => $title,
            'first_name' => Str::title($first_name),
            'last_name' => Str::title($last_name),
            'level' => $level,
            'program' => $program,
            'email' => $email,
            'year_id' => $year_id,
            'is_verified' => 1,
            'email_verified_at' => now(),
            'name' => Str::title($row['name']),
            'is_active' => 1,
        ]);
    }

    public function uniqueBy()
    {
        return 'email';
    }

    public function upsertColumns()
    {
        return [
            'program',
            'level',
            'title',
            'first_name',
            'last_name',
            'year_id',
            'is_verified',
            'email_verified_at',
            'name',
            'is_active',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
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
