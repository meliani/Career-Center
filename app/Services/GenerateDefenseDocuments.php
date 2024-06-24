<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateDefenseDocuments
{
    public function generateEvaluationSheet(Project $project): void
    {
        $template_view = 'pdf.templates.ThirdYear.Defenses.evaluation_sheet';
        $pdf_path = 'storage/document/defenses';
        $pdf_file_name = 'evaluation-sheet-projectId-' . Str::slug($project->id_pfe) . '-' . time() . '.pdf';

        if (! File::exists($pdf_path)) {
            File::makeDirectory($pdf_path, 0755, true);
        }
        pdf()
            ->view($template_view, ['project' => $project])
            ->save($pdf_path . '/' . $pdf_file_name)
            ->name($pdf_file_name);

        $project->evaluation_sheet_url = env('APP_URL') . '/' . $pdf_path . '/' . $pdf_file_name;
        $project->save();
    }
}
