<?php

namespace App\Exports;

use App\Models\InternshipOffer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InternshipApplicationsExport implements FromCollection, WithHeadings
{
    protected $internship;

    public function __construct(InternshipOffer $internship)
    {
        $this->internship = $internship;
    }

    public function collection()
    {
        return $this->internship->applications()->with('student')->get()
            ->map(function ($application) {
                return [
                    'name' => $application->student->name,
                    'level' => $application->student->level->getLabel(),
                    'email' => $application->student->email,
                    'phone' => $application->student->phone,
                    'personal_email' => $application->student->email_perso,
                    'cv' => $application->student->cv ?? '',
                    'cover_letter' => $application->student->lm ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Level',
            'Email',
            'Phone',
            'Personal Email',
            'CV',
            'Cover Letter',
        ];
    }
}
