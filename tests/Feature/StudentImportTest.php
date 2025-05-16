<?php

namespace Tests\Feature;

use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_can_be_imported_from_csv()
    {
        // Create a fake CSV file with student data
        $csvContent = <<<'CSV'
matricule,civilite,nom,prenom,الاسم_الشخصي,الاسم_العائلي,annee_inscriptionreiscription_2024_2025,code_filiere,filiere,cineacarte_sejour,n_de_passeport,code_massar,date_de_naissance,lieu_de_naissance,مكان_الازدياد,nationalite,adresse_de_correspondance,ville_de_residence,annee_du_baccalaureat,serie_du_baccalaureat,mention_du_baccalaureat,lieu_dobtention_du_bac,cnc,filiere_cnc,classement_cnc,telephone_mobile,telephone_du_pere,telephone_de_la_mere,email,mail_inpt,annee_dentree,voie_dacces,observations
230036,M.,AABDANE,ABDELKARIM,عبد الكريم,عبدان,deuxième année,ASEDS,Advanced Software Engineering for Digital Services,JB524993,,D130013154,03/10/2004,REGGADA TIZNIT,الركادة تيزنيت,MAROCAINE,HAY AIT IDDER RUE 1306 NR 41 DCHEIRA INEZGANE,INEZGANE,2021,Sciences Mathématiques,TRES BIEN,SOUSS MASSA,,MP,211,07 06 15 82 98,06 05 87 06 50,06 29 98 85 12,aabdaneabdelkarim@gmail.com,aabdane.abdelkarim@ine.inpt.ac.ma,2023-2024,CNC 2023,
CSV;

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);
        
        // Store the file
        $path = Storage::disk('local')->putFile('temp', $file);
        
        // Run the import
        $import = new StudentsImport();
        $import->import(Storage::disk('local')->path($path));
        
        // Check if the student was imported correctly
        $this->assertDatabaseHas('students', [
            'matricule' => '230036',
            'first_name' => 'Abdelkarim',
            'last_name' => 'Aabdane',
            'email' => 'aabdane.abdelkarim@ine.inpt.ac.ma',
        ]);
        
        // Check import results
        $results = $import->getImportResults();
        $this->assertEquals(1, $results['created']);
        $this->assertEquals(0, $results['updated']);
        $this->assertEquals(0, $results['failed']);
        
        // Test updating an existing student
        $updatedCsvContent = <<<'CSV'
matricule,civilite,nom,prenom,الاسم_الشخصي,الاسم_العائلي,annee_inscriptionreiscription_2024_2025,code_filiere,filiere,cineacarte_sejour,n_de_passeport,code_massar,date_de_naissance,lieu_de_naissance,مكان_الازدياد,nationalite,adresse_de_correspondance,ville_de_residence,annee_du_baccalaureat,serie_du_baccalaureat,mention_du_baccalaureat,lieu_dobtention_du_bac,cnc,filiere_cnc,classement_cnc,telephone_mobile,telephone_du_pere,telephone_de_la_mere,email,mail_inpt,annee_dentree,voie_dacces,observations
230036,M.,AABDANE,ABDELKARIM,عبد الكريم,عبدان,troisième année,ASEDS,Advanced Software Engineering for Digital Services,JB524993,,D130013154,03/10/2004,REGGADA TIZNIT,الركادة تيزنيت,MAROCAINE,NEW ADDRESS,INEZGANE,2021,Sciences Mathématiques,TRES BIEN,SOUSS MASSA,,MP,211,07 06 15 82 99,06 05 87 06 50,06 29 98 85 12,aabdaneabdelkarim@gmail.com,aabdane.abdelkarim@ine.inpt.ac.ma,2023-2024,CNC 2023,
CSV;

        $updatedFile = UploadedFile::fake()->createWithContent('updated_students.csv', $updatedCsvContent);
        $updatedPath = Storage::disk('local')->putFile('temp', $updatedFile);
        
        // Run the import again with update mode
        $updateImport = new StudentsImport('update');
        $updateImport->import(Storage::disk('local')->path($updatedPath));
        
        // Check if the student was updated correctly
        $this->assertDatabaseHas('students', [
            'matricule' => '230036',
            'first_name' => 'Abdelkarim',
            'last_name' => 'Aabdane',
            'email' => 'aabdane.abdelkarim@ine.inpt.ac.ma',
            'address' => 'NEW ADDRESS',
            'phone' => '07 06 15 82 99',
        ]);
        
        // Check import results for update
        $updateResults = $updateImport->getImportResults();
        $this->assertEquals(0, $updateResults['created']);
        $this->assertEquals(1, $updateResults['updated']);
    }
}
