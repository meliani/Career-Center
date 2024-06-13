<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\FastTextService;
use Illuminate\Console\Command; // Assuming you have a Project model

class DetectProjectTitleLanguage extends Command
{
    protected $signature = 'detect:project-title-language';

    protected $description = 'Detect the language of project titles and label them';

    protected $fastTextService;

    public function __construct(FastTextService $fastTextService)
    {
        parent::__construct();
        $this->fastTextService = $fastTextService;
    }

    public function handle()
    {
        $projects = Project::all(); // Retrieve all projects

        foreach ($projects as $project) {
            $title = $project->title; // Assuming each project has a 'title' field
            $language = $this->fastTextService->detectLanguage($title); // Detect language

            $project->language = $language; // Assuming each project has a 'language' field to store the detected language
            $project->save(); // Save the updated project

            $this->info("Updated Project ID {$project->id} with Language: {$language}");
        }

        $this->info('All project titles have been processed.');
    }
}
