<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Get projects that don't have timetables assigned
     */
    public function getNonPlannedProjects()
    {
        $projects = Project::whereDoesntHave('timetable')
            ->with(['final_internship_agreements.student', 'professors', 'organization'])
            ->orderBy('end_date', 'asc')
            ->get();

        return response()->json([
            'projects' => $projects,
            'total' => $projects->count()
        ]);
    }

    // ...existing code...
}