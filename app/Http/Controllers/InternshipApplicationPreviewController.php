<?php

namespace App\Http\Controllers;

use App\Exports\InternshipApplicationsExport;
use App\Models\InternshipOffer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InternshipApplicationPreviewController extends Controller
{
    public function preview(Request $request, InternshipOffer $internship)
    {
        return view('internship.applications.preview', [
            'internship' => $internship,
            'applications' => $internship->applications()->with('student')->get(),
        ]);
    }

    public function export(Request $request, InternshipOffer $internship)
    {
        return Excel::download(
            new InternshipApplicationsExport($internship),
            'applications-' . $internship->id . '.xlsx'
        );
    }
}
