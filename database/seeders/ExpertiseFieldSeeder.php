<?php

namespace Database\Seeders;

use App\Enums\Program;
use App\Models\ExpertiseField;
use Illuminate\Database\Seeder;

class ExpertiseFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'Développement Web',
                'slug' => 'developpement-web',
                'description' => 'Développement frontend et backend, architectures web modernes',
                'icon' => 'heroicon-o-code-bracket',
                'programs' => [Program::ASEDS->value, Program::SUD->value, Program::SMARTICT->value],
            ],
            [
                'name' => 'Intelligence Artificielle',
                'slug' => 'intelligence-artificielle',
                'description' => 'Machine learning, deep learning et systèmes intelligents',
                'icon' => 'heroicon-o-cpu-chip',
                'programs' => [Program::DATA->value, Program::SMARTICT->value],
            ],
            [
                'name' => 'Sécurité Informatique',
                'slug' => 'securite-informatique',
                'description' => 'Cybersécurité, cryptographie, protection des données',
                'icon' => 'heroicon-o-shield-check',
                'programs' => [Program::ICCN->value],
            ],
            [
                'name' => 'Analyse de Données',
                'slug' => 'analyse-donnees',
                'description' => 'Big data, statistiques, visualisation de données',
                'icon' => 'heroicon-o-chart-bar',
                'programs' => [Program::DATA->value, Program::SMARTICT->value],
            ],
            [
                'name' => 'Systèmes Embarqués',
                'slug' => 'systemes-embarques',
                'description' => 'IoT, programmation bas niveau, systèmes temps réel',
                'icon' => 'heroicon-o-chip',
                'programs' => [Program::SESNUM->value],
            ],
            [
                'name' => 'Cloud Computing',
                'slug' => 'cloud-computing',
                'description' => 'Architecture cloud, conteneurisation, microservices',
                'icon' => 'heroicon-o-cloud',
                'programs' => [Program::ASEDS->value, Program::SUD->value],
            ],
            [
                'name' => 'Gestion de Projet IT',
                'slug' => 'gestion-projet-it',
                'description' => 'Méthodologies agiles, management de projet, AMOA',
                'icon' => 'heroicon-o-document-check',
                'programs' => [Program::AMOA->value],
            ],
            [
                'name' => 'Architecture Logicielle',
                'slug' => 'architecture-logicielle',
                'description' => 'Patterns de conception, architecture distribuée',
                'icon' => 'heroicon-o-squares-2x2',
                'programs' => [Program::ASEDS->value, Program::SUD->value],
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'CI/CD, automatisation, gestion d\'infrastructure',
                'icon' => 'heroicon-o-arrow-path',
                'programs' => [Program::ASEDS->value, Program::SUD->value],
            ],
            [
                'name' => 'Systèmes Distribués',
                'slug' => 'systemes-distribues',
                'description' => 'Architecture distribuée, systèmes répartis',
                'icon' => 'heroicon-o-circle-stack',
                'programs' => [Program::SUD->value],
            ],
        ];

        foreach ($fields as $field) {
            ExpertiseField::create($field);
        }

    }
}
