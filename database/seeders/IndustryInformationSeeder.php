<?php

namespace Database\Seeders;

use App\Models\IndustryInformation;
use Illuminate\Database\Seeder;

class IndustryInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // English Industries
        $industries_en = [
            [
                'name' => 'Agriculture',
                'slug' => 'agriculture',
                'description' => 'Agriculture is the science, art and practice of cultivating plants and livestock.',
                'icon' => 'heroicon-o-globe-alt',
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'The automotive industry comprises companies involved in vehicle design, manufacturing, and sales.',
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name' => 'Construction',
                'slug' => 'construction',
                'description' => 'Construction is the process of constructing a building or infrastructure.',
                'icon' => 'heroicon-o-building-office',
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Education is the process of facilitating learning and knowledge acquisition.',
                'icon' => 'heroicon-o-academic-cap',
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Finance is a term for matters regarding money management and investments.',
                'icon' => 'heroicon-o-banknotes',
            ],
            [
                'name' => 'Healthcare',
                'slug' => 'healthcare',
                'description' => 'Healthcare focuses on maintaining and improving health through medical services.',
                'icon' => 'heroicon-o-heart',
            ],
            [
                'name' => 'Hospitality',
                'slug' => 'hospitality',
                'description' => 'Hospitality covers accommodation, food service, and tourism industries.',
                'icon' => 'heroicon-o-home',
            ],
            [
                'name' => 'Information Technology',
                'slug' => 'information-technology',
                'description' => 'IT involves the use of computers and telecommunications for information processing.',
                'icon' => 'heroicon-o-computer-desktop',
            ],
            [
                'name' => 'Manufacturing',
                'slug' => 'manufacturing',
                'description' => 'Manufacturing is the production of goods using labor, machines, and tools.',
                'icon' => 'heroicon-o-cog-6-tooth',
            ],
            [
                'name' => 'Mining',
                'slug' => 'mining',
                'description' => 'Mining involves extracting valuable minerals and materials from the Earth.',
                'icon' => 'heroicon-o-cube',
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Real estate involves property management, development, and transactions.',
                'icon' => 'heroicon-o-building-office-2',
            ],
            [
                'name' => 'Retail',
                'slug' => 'retail',
                'description' => 'Retail involves selling goods directly to consumers through various channels.',
                'icon' => 'heroicon-o-shopping-bag',
            ],
            [
                'name' => 'Telecommunications',
                'slug' => 'telecommunications',
                'description' => 'Telecommunications involves information exchange through electronic systems.',
                'icon' => 'heroicon-o-signal',
            ],
            [
                'name' => 'Transportation',
                'slug' => 'transportation',
                'description' => 'Transportation covers the movement of people and goods between locations.',
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name' => 'Utilities',
                'slug' => 'utilities',
                'description' => 'Utilities provide essential services like electricity, water, and gas.',
                'icon' => 'heroicon-o-bolt',
            ],
            [
                'name' => 'Wholesale',
                'slug' => 'wholesale',
                'description' => 'Wholesale involves selling goods in bulk to retailers.',
                'icon' => 'heroicon-o-building-storefront',
            ],
        ];

        // French Industries
        $industries_fr = [
            [
                'name' => 'Agriculture',
                'slug' => 'agriculture',
                'description' => "L'agriculture est la science, l'art et la pratique de la culture des plantes et de l'élevage.",
                'icon' => 'heroicon-o-globe-alt',
            ],
            [
                'name' => 'Automobile',
                'slug' => 'automobile',
                'description' => "L'industrie automobile comprend les entreprises impliquées dans la conception, la fabrication et la vente de véhicules.",
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name' => 'Construction',
                'slug' => 'construction',
                'description' => 'La construction est le processus de réalisation de bâtiments ou d\'infrastructures.',
                'icon' => 'heroicon-o-building-office',
            ],
            [
                'name' => 'Éducation',
                'slug' => 'education',
                'description' => "L'éducation est le processus de facilitation de l'apprentissage et l'acquisition de connaissances.",
                'icon' => 'heroicon-o-academic-cap',
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'La finance concerne la gestion de l\'argent et des investissements.',
                'icon' => 'heroicon-o-banknotes',
            ],
            [
                'name' => 'Santé',
                'slug' => 'sante',
                'description' => 'Le secteur de la santé se concentre sur le maintien et l\'amélioration de la santé par les services médicaux.',
                'icon' => 'heroicon-o-heart',
            ],
            [
                'name' => 'Hôtellerie',
                'slug' => 'hotellerie',
                'description' => "L'hôtellerie couvre les secteurs de l'hébergement, de la restauration et du tourisme.",
                'icon' => 'heroicon-o-home',
            ],
            [
                'name' => 'Informatique',
                'slug' => 'informatique',
                'description' => "L'informatique implique l'utilisation d'ordinateurs et des télécommunications pour le traitement de l'information.",
                'icon' => 'heroicon-o-computer-desktop',
            ],
            [
                'name' => 'Fabrication',
                'slug' => 'fabrication',
                'description' => 'La fabrication est la production de biens utilisant la main-d\'œuvre, les machines et les outils.',
                'icon' => 'heroicon-o-cog-6-tooth',
            ],
            [
                'name' => 'Exploitation minière',
                'slug' => 'exploitation-miniere',
                'description' => "L'exploitation minière consiste à extraire des minéraux et matériaux précieux de la Terre.",
                'icon' => 'heroicon-o-cube',
            ],
            [
                'name' => 'Immobilier',
                'slug' => 'immobilier',
                'description' => "L'immobilier implique la gestion, le développement et les transactions de propriétés.",
                'icon' => 'heroicon-o-building-office-2',
            ],
            [
                'name' => 'Commerce de détail',
                'slug' => 'commerce-detail',
                'description' => 'Le commerce de détail implique la vente de biens directement aux consommateurs.',
                'icon' => 'heroicon-o-shopping-bag',
            ],
            [
                'name' => 'Télécommunications',
                'slug' => 'telecommunications',
                'description' => "Les télécommunications concernent l'échange d'informations via des systèmes électroniques.",
                'icon' => 'heroicon-o-signal',
            ],
            [
                'name' => 'Transport',
                'slug' => 'transport',
                'description' => 'Le transport couvre le déplacement des personnes et des marchandises entre différents lieux.',
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name' => 'Services publics',
                'slug' => 'services-publics',
                'description' => "Les services publics fournissent des services essentiels comme l'électricité, l'eau et le gaz.",
                'icon' => 'heroicon-o-bolt',
            ],
            [
                'name' => 'Commerce de gros',
                'slug' => 'commerce-gros',
                'description' => 'Le commerce de gros implique la vente de marchandises en grande quantité aux détaillants.',
                'icon' => 'heroicon-o-building-storefront',
            ],
        ];

        // Create with locale
        foreach ($industries_en as $industry) {
            IndustryInformation::create(array_merge($industry, ['locale' => 'en']));
        }

        foreach ($industries_fr as $industry) {
            IndustryInformation::create(array_merge($industry, ['locale' => 'fr']));
        }
    }
}
