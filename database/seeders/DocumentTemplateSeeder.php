<?php

namespace Database\Seeders;

use App\Enums\StudentLevel;
use Illuminate\Database\Seeder;
use App\Enums\DocumentTemplateType;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTemplates = [
            [
                'name' => 'Document Template 1',
                'content' => 'Document Template 1 Content',
                'example_url' => null,
                'type' => DocumentTemplateType::Sheet,
                'level' => StudentLevel::FirstYear,
                'status' => 'active',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Document Template 2',
                'content' => 'Document Template 2 Content',
                'example_url' => null,
                'type' => DocumentTemplateType::AgreementMorocco,
                'level' => StudentLevel::SecondYear,
                'status' => 'active',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Document Template 3',
                'content' => 'Document Template 3 Content',
                'example_url' => null,
                'type' => DocumentTemplateType::AgreementFrance,
                'level' => StudentLevel::ThirdYear,
                'status' => 'active',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($documentTemplates as $documentTemplate) {
            \App\Models\DocumentTemplate::create($documentTemplate);
        }
    }
}
