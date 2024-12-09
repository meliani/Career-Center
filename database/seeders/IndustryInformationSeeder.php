<?php

namespace Database\Seeders;

use App\Models\IndustryInformation;
use Illuminate\Database\Seeder;

class IndustryInformationSeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            [
                'name_en' => 'Agriculture',
                'name_fr' => 'Agriculture',
                'name_ar' => 'الزراعة',
                'slug' => 'agriculture',
                'description_en' => 'Agriculture is the science, art and practice of cultivating plants and livestock.',
                'description_fr' => "L'agriculture est la science, l'art et la pratique de la culture des plantes et de l'élevage.",
                'description_ar' => 'الزراعة هي علم وفن وممارسة زراعة النباتات وتربية المواشي.',
                'icon' => 'heroicon-o-globe-alt',
            ],
            [
                'name_en' => 'Automotive',
                'name_fr' => 'Automobile',
                'name_ar' => 'السيارات',
                'slug' => 'automotive',
                'description_en' => 'The automotive industry comprises companies involved in vehicle design, manufacturing, and sales.',
                'description_fr' => "L'industrie automobile comprend les entreprises impliquées dans la conception, la fabrication et la vente de véhicules.",
                'description_ar' => 'تشمل صناعة السيارات الشركات المشاركة في تصميم وتصنيع وبيع المركبات.',
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name_en' => 'Construction',
                'name_fr' => 'Construction',
                'name_ar' => 'البناء',
                'slug' => 'construction',
                'description_en' => 'Construction is the process of constructing a building or infrastructure.',
                'description_fr' => "La construction est le processus de réalisation de bâtiments ou d'infrastructures.",
                'description_ar' => 'البناء هو عملية تشييد مبنى أو بنية تحتية.',
                'icon' => 'heroicon-o-building-office',
            ],
            [
                'name_en' => 'Education',
                'name_fr' => 'Éducation',
                'name_ar' => 'التعليم',
                'slug' => 'education',
                'description_en' => 'Education is the process of facilitating learning and knowledge acquisition.',
                'description_fr' => "L'éducation est le processus de facilitation de l'apprentissage et l'acquisition de connaissances.",
                'description_ar' => 'التعليم هو عملية تسهيل التعلم واكتساب المعرفة.',
                'icon' => 'heroicon-o-academic-cap',
            ],
            [
                'name_en' => 'Finance',
                'name_fr' => 'Finance',
                'name_ar' => 'التمويل',
                'slug' => 'finance',
                'description_en' => 'Finance is a term for matters regarding money management and investments.',
                'description_fr' => 'La finance concerne la gestion de l\'argent et des investissements.',
                'description_ar' => 'التمويل هو مصطلح يتعلق بإدارة الأموال والاستثمارات.',
                'icon' => 'heroicon-o-banknotes',
            ],
            [
                'name_en' => 'Healthcare',
                'name_fr' => 'Santé',
                'name_ar' => 'الرعاية الصحية',
                'slug' => 'healthcare',
                'description_en' => 'Healthcare focuses on maintaining and improving health through medical services.',
                'description_fr' => 'Le secteur de la santé se concentre sur le maintien et l\'amélioration de la santé par les services médicaux.',
                'description_ar' => 'الرعاية الصحية تركز على الحفاظ على الصحة وتحسينها من خلال الخدمات الطبية.',
                'icon' => 'heroicon-o-heart',
            ],
            [
                'name_en' => 'Hospitality',
                'name_fr' => 'Hospitalité',
                'name_ar' => 'الضيافة',
                'slug' => 'hospitality',
                'description_en' => 'Hospitality covers accommodation, food service, and tourism industries.',
                'description_fr' => 'L\'hôtellerie couvre les secteurs de l\'hébergement, de la restauration et du tourisme.',
                'description_ar' => 'تشمل الضيافة صناعات الإقامة وخدمات الطعام والسياحة.',
                'icon' => 'heroicon-o-home',
            ],
            [
                'name_en' => 'Information Technology',
                'name_fr' => 'Informatique',
                'name_ar' => 'تكنولوجيا المعلومات',
                'slug' => 'information-technology',
                'description_en' => 'IT involves the use of computers and telecommunications for information processing.',
                'description_fr' => 'L\'informatique implique l\'utilisation d\'ordinateurs et de télécommunications pour le traitement de l\'information.',
                'description_ar' => 'تشمل تكنولوجيا المعلومات استخدام الحواسيب والاتصالات لمعالجة المعلومات.',
                'icon' => 'heroicon-o-computer-desktop',
            ],
            [
                'name_en' => 'Manufacturing',
                'name_fr' => 'Fabrication',
                'name_ar' => 'التصنيع',
                'slug' => 'manufacturing',
                'description_en' => 'Manufacturing is the production of goods using labor, machines, and tools.',
                'description_fr' => 'La fabrication est la production de biens utilisant la main-d\'œuvre, les machines et les outils.',
                'description_ar' => 'التصنيع هو إنتاج السلع باستخدام العمالة والآلات والأدوات.',
                'icon' => 'heroicon-o-cog-6-tooth',
            ],
            [
                'name_en' => 'Mining',
                'name_fr' => 'Exploitation minière',
                'name_ar' => 'التعدين',
                'slug' => 'mining',
                'description_en' => 'Mining involves extracting valuable minerals and materials from the Earth.',
                'description_fr' => 'L\'exploitation minière consiste à extraire des minéraux et matériaux précieux de la Terre.',
                'description_ar' => 'يتضمن التعدين استخراج المعادن والمواد الثمينة من الأرض.',
                'icon' => 'heroicon-o-cube',
            ],
            [
                'name_en' => 'Real Estate',
                'name_fr' => 'Immobilier',
                'name_ar' => 'العقارات',
                'slug' => 'real-estate',
                'description_en' => 'Real estate involves property management, development, and transactions.',
                'description_fr' => 'L\'immobilier implique la gestion, le développement et les transactions de propriétés.',
                'description_ar' => 'تشمل العقارات إدارة الممتلكات والتطوير والمعاملات.',
                'icon' => 'heroicon-o-building-office-2',
            ],
            [
                'name_en' => 'Retail',
                'name_fr' => 'Commerce de détail',
                'name_ar' => 'التجزئة',
                'slug' => 'retail',
                'description_en' => 'Retail involves selling goods directly to consumers through various channels.',
                'description_fr' => 'Le commerce de détail implique la vente de biens directement aux consommateurs.',
                'description_ar' => 'تشمل تجارة التجزئة بيع السلع مباشرةً للمستهلكين عبر قنوات مختلفة.',
                'icon' => 'heroicon-o-shopping-bag',
            ],
            [
                'name_en' => 'Telecommunications',
                'name_fr' => 'Télécommunications',
                'name_ar' => 'الاتصالات',
                'slug' => 'telecommunications',
                'description_en' => 'Telecommunications involves information exchange through electronic systems.',
                'description_fr' => 'Les télécommunications concernent l\'échange d\'informations via des systèmes électroniques.',
                'description_ar' => 'تتعلق الاتصالات بتبادل المعلومات عبر الأنظمة الإلكترونية.',
                'icon' => 'heroicon-o-signal',
            ],
            [
                'name_en' => 'Transportation',
                'name_fr' => 'Transport',
                'name_ar' => 'النقل',
                'slug' => 'transportation',
                'description_en' => 'Transportation covers the movement of people and goods between locations.',
                'description_fr' => 'Le transport couvre le déplacement des personnes et des marchandises entre différents lieux.',
                'description_ar' => 'يشمل النقل نقل الأشخاص والبضائع بين المواقع.',
                'icon' => 'heroicon-o-truck',
            ],
            [
                'name_en' => 'Utilities',
                'name_fr' => 'Services publics',
                'name_ar' => 'المرافق',
                'slug' => 'utilities',
                'description_en' => 'Utilities provide essential services like electricity, water, and gas.',
                'description_fr' => 'Les services publics fournissent des services essentiels comme l\'électricité, l\'eau et le gaz.',
                'description_ar' => 'توفر المرافق خدمات أساسية مثل الكهرباء والماء والغاز.',
                'icon' => 'heroicon-o-bolt',
            ],
            [
                'name_en' => 'Wholesale',
                'name_fr' => 'Commerce de gros',
                'name_ar' => 'الجملة',
                'slug' => 'wholesale',
                'description_en' => 'Wholesale involves selling goods in bulk to retailers.',
                'description_fr' => 'Le commerce de gros implique la vente de marchandises en grande quantité aux détaillants.',
                'description_ar' => 'تشمل تجارة الجملة بيع البضائع بالجملة لتجار التجزئة.',
                'icon' => 'heroicon-o-building-storefront',
            ],
        ];

        foreach ($industries as $industry) {
            IndustryInformation::updateOrCreate(
                ['slug' => $industry['slug']],
                $industry
            );
        }
    }
}
