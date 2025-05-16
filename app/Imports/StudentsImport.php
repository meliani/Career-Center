<?php

namespace App\Imports;

use App\Enums\Program;
use App\Enums\StudentLevel;
use App\Enums\Title;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;

class StudentsImport implements SkipsEmptyRows, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithProgressBar, WithUpsertColumns, WithUpserts
{
    use Importable;
    
    protected $mergeMode;
    protected $academicYear;
    protected $importResults = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'failed' => 0,
        'total' => 0,
        'errors' => [],
    ];
    
    public function __construct(string $mergeMode = 'update', ?string $academicYear = null)
    {
        $this->mergeMode = $mergeMode; // 'update', 'skip', or 'replace'
        $this->academicYear = $academicYear;
    }

    public function model(array $row)
    {
        try {
            $this->importResults['total']++;
            
            // Try to find the student by email or matricule
            $studentEmail = strtolower(trim($row['mail_inpt'] ?? ''));
            $matricule = trim($row['matricule'] ?? '');
            
            // First, check if the student already exists
            $existingStudent = null;
            if (!empty($studentEmail)) {
                $existingStudent = Student::where('email', $studentEmail)->first();
            }
            
            if (!$existingStudent && !empty($matricule)) {
                $existingStudent = Student::where('matricule', $matricule)->first();
            }
            
            // Handle existing student according to merge mode
            if ($existingStudent) {
                if ($this->mergeMode === 'skip') {
                    $this->importResults['skipped']++;
                    return null;
                }
                
                // For 'update' and 'replace' modes, we'll let the upsert functionality handle it
                $this->importResults['updated']++;
            } else {
                $this->importResults['created']++;
            }
            
            // Get year from setting or use current
            $year_id = Year::current()->id;
            if ($this->academicYear) {
                $yearModel = Year::where('title', $this->academicYear)->first();
                if ($yearModel) {
                    $year_id = $yearModel->id;
                }
            }
            
            // Determine the title (Mr/Mrs)
            $title = Title::Mr;
            if (!empty($row['civilite'])) {
                $title = str_contains(strtolower($row['civilite']), 'mme') ? Title::Mrs : Title::Mr;
            }
            
            // Determine the level based on the enrollment status
            $level = $this->determineStudentLevel($row['annee_inscriptionreiscription_2024_2025'] ?? '');
            
            // Determine the program
            $program = $this->determineProgram($row['filiere'] ?? '', $row['code_filiere'] ?? '');
            
            // Get the email from the row - prefer INPT email
            $email = strtolower(trim($row['mail_inpt'] ?? ''));
            
            // Create the student data
            return new Student([
                'title' => $title,
                'matricule' => $matricule,
                'first_name' => Str::title($row['prenom'] ?? ''),
                'last_name' => Str::title($row['nom'] ?? ''),
                'first_name_ar' => $row['الاسم_الشخصي'] ?? null,
                'last_name_ar' => $row['الاسم_العائلي'] ?? null,
                'email' => $email,
                'email_perso' => strtolower(trim($row['email'] ?? '')),
                'phone' => $row['telephone_mobile'] ?? null,
                'birth_date' => $this->parseDate($row['date_de_naissance'] ?? null),
                'birth_place' => $row['lieu_de_naissance'] ?? null,
                'birth_place_ar' => $row['مكان_الازدياد'] ?? null,
                'nationality' => $row['nationalite'] ?? null,
                'address' => $row['adresse_de_correspondance'] ?? null,
                'city' => $row['ville_de_residence'] ?? null,
                'level' => $level,
                'program' => $program,
                'year_id' => $year_id,
                'is_verified' => true,
                'email_verified_at' => now(),
                'is_active' => true,
                
                // Baccalaureate information
                'bac_year' => $row['annee_du_baccalaureat'] ?? null,
                'bac_type' => $row['serie_du_baccalaureat'] ?? null,
                'bac_mention' => $row['mention_du_baccalaureat'] ?? null,
                'bac_place' => $row['lieu_dobtention_du_bac'] ?? null,
                
                // CNC information
                'cnc' => $row['cnc'] ?? null,
                'cnc_filiere' => $row['filiere_cnc'] ?? null,
                'cnc_rank' => $row['classement_cnc'] ?? null,
                
                // Parent contact information
                'father_phone' => $row['telephone_du_pere'] ?? null,
                'mother_phone' => $row['telephone_de_la_mere'] ?? null,
                
                // Enrollment information
                'enrollment_year' => $row['annee_dentree'] ?? null,
                'access_path' => $row['voie_dacces'] ?? null,
                'enrollment_status' => $row['annee_inscriptionreiscription_2024_2025'] ?? null,
                
                // ID information
                'cine' => $row['cineacarte_sejour'] ?? null,
                'passport' => $row['n_de_passeport'] ?? null,
                'massar_code' => $row['code_massar'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->importResults['failed']++;
            $this->importResults['errors'][] = "Error processing row: {$e->getMessage()}";
            Log::error("Student import error", [
                'error' => $e->getMessage(),
                'row' => $row,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    public function uniqueBy()
    {
        return ['email', 'matricule'];
    }

    public function upsertColumns()
    {
        return [
            'title', 'first_name', 'last_name', 'first_name_ar', 'last_name_ar',
            'email_perso', 'phone', 'birth_date', 'birth_place', 'birth_place_ar',
            'nationality', 'address', 'city', 'level', 'program',
            'year_id', 'is_verified', 'email_verified_at', 'is_active',
            'bac_year', 'bac_type', 'bac_mention', 'bac_place',
            'cnc', 'cnc_filiere', 'cnc_rank',
            'father_phone', 'mother_phone',
            'enrollment_year', 'access_path', 'enrollment_status',
            'cine', 'passport', 'massar_code',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }
    
    protected function determineStudentLevel($enrollmentYear)
    {
        // Convert enrollment year to student level
        $levelMapping = [
            'première année' => StudentLevel::FirstYear,
            'deuxième année' => StudentLevel::SecondYear,
            'troisième année' => StudentLevel::ThirdYear,
            'alumni' => StudentLevel::Alumni,
        ];
        
        $enrollmentYear = strtolower(trim($enrollmentYear));
        
        return $levelMapping[$enrollmentYear] ?? StudentLevel::FirstYear;
    }
    
    protected function determineProgram($filiere, $codeFiliere)
    {
        // Map program code to enum values
        // You'll need to adjust this based on your actual Program enum values
        $programMapping = [
            'ASEDS' => Program::ASEDS,
            'SESNum' => Program::SESNUM,
            'SMART-ICT' => Program::SMARTICT,
            'SUD-CLOUD&IoT' => Program::SUD,
            'DATA' => Program::DATA,
            'AMOA' => Program::AMOA,
            'ICCN' => Program::ICCN,
        ];
        
        return $programMapping[$codeFiliere] ?? Program::ASEDS;
    }
    
    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // If none of the formats match, try parsing with strtotime
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            Log::error("Date parsing error", ['date' => $dateString, 'error' => $e->getMessage()]);
        }
        
        return null;
    }
    
    public function getImportResults()
    {
        return $this->importResults;
    }
}
