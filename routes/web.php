<?php

use App\Facades\GlobalDefenseCalendarConnector;
use App\Http\Controllers\AgreementVerificationController;
use App\Http\Controllers\DiplomaVerificationController;
use App\Http\Controllers\PVVerificationController;
use App\Http\Controllers\QrUrlDecoder;
use App\Models\DefenseSync;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('health', HealthCheckResultsController::class);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::group(['middleware' => 'redirect.if.not.installed'], function () {
//     Route::get('install', function () {
//         return view('welcome');
//     })->name('install');

// });
Route::get('/', function () {
    $internshipOffersCount = \App\Models\InternshipOffer::withoutTrashed()
        ->where('status', 'Published')
        ->where('internship_level', 'FinalYearInternship')->count();
    // dd($internshipOffersCount);

    return view('welcome', [
        'internshipOffersCount' => $internshipOffersCount,
    ]);
})->name('home');
Route::get('lang/{lang}', 'App\Http\Controllers\LanguageController@switchLang');

// Route::get('/url/{version}/{cipher}', Pages\QrResponse::class);
// Route::get('/url', QrUrlDecoder::class);
Route::get('/verify-agreement', QrUrlDecoder::class);
Route::get('/verify-diploma/{verification_code}', DiplomaVerificationController::class)->name('diploma.verify');
Route::get('/verify-diploma/', DiplomaVerificationController::class);
Route::get('/verify-deliberation-pv/{verification_code}', PVVerificationController::class)->name('deliberation-pv.verify');
// Route::get('/qr-response', Pages\QrResponse::class)->name('qr-response');
Route::get('/verify-agreement/{verification_code}', AgreementVerificationController::class)->name('internship-agreement.verify');
// Route::get('/mail-preview/{email}', 'App\Http\Controllers\MailPreviewController@show');

// Route::get('/public-internship-offer-form', \App\Livewire\PublicInternshipOfferForm::class);

Route::get('/publier-un-stage', \App\Livewire\NewInternship::class)->name('new-internship-fr');
Route::get('/publish-internship', \App\Livewire\NewInternship::class)->name('new-internship');
// Route::get('/publier-une-offre-demploi', \App\Livewire\NewJobOffer::class)->name('new-job-offer');

Route::get('/publier-un-evenement', \App\Livewire\NewMidweekEvent::class)->name('new-midweek-event');

Route::get('/soutenances', function () {
    // First, get all the regular defense dates except June 27
    $regularData = \App\Models\Timetable::planned()
        ->whereHas('timeslot', function ($query) {
            $query->where('year_id', 8)
                  ->whereRaw("DATE_FORMAT(start_time, '%m-%d') != '06-27'");
        })
        ->with(['project.agreements.agreeable.student', 'timeslot', 'room'])
        ->get()
        ->filter(function ($timetable) {
            if ($timetable->project === null) {
                return false;
            }
            $hasStudents = $timetable->project->agreements->some(function($agreement) {
                return $agreement->agreeable?->student !== null;
            });
            return $hasStudents;
        })
        ->map(function ($timetable) {
            $project = $timetable->project;
            $students = $project->agreements->map(function($agreement) {
                return optional($agreement->agreeable?->student);
            })->filter();
            $studentNames = $students->map(fn($s) => $s->full_name)->join(' & ');
            $studentIds = $students->map(fn($s) => $s->id_pfe)->join(' & ');
            $studentFiliere = $students->map(fn($s) => $s->program?->getLabel())->unique()->join(' / ');
            
            return collect([
                'Date Soutenance' => optional($timetable->timeslot)->start_time?->format('Y-m-d'),
                'Heure' => optional($timetable->timeslot)->start_time?->format('H:i'),
                'Autorisation' => $project->isAuthorized() ? 'Autorisé' : 'En Attente',
                'Lieu' => optional($timetable->room)->name,
                'ID PFE' =>  $studentIds,
                "Nom de l'étudiant" => $studentNames,
                'Filière' => $studentFiliere,
                'Encadrant Interne' => $project->academic_supervisor()?->name,
                'Nom et Prénom Examinateur 1' => $project->first_reviewer()?->name,
                'Nom et Prénom Examinateur 2' => $project->second_reviewer()?->name,
                'remarques' => '',
                'convention signée' => '',
                'accord encadrant interne' => '',
                'fiche evaluation entreprise' => '',
            ]);
        });

    // Check if we need to insert the holiday row
    $hasJune27 = \App\Models\Timetable::planned()
        ->whereHas('timeslot', function ($query) {
            $query->whereRaw("DATE_FORMAT(start_time, '%m-%d') = '06-27'");
        })
        ->exists();

    if ($hasJune27) {
        // Find the position to insert the holiday entry
        $holidayData = collect([
            'holiday' => true,
            'message' => 'فاتح شهر محرم 1447',
            '_special_class' => 'holiday-row',
            'Date Soutenance' => '',
            'Heure' => '',
            'Lieu' => '',
            'Autorisation' => '',
            'ID PFE' => '',
            "Nom de l'étudiant" => '',
            'Filière' => '',
            'Encadrant Interne' => '',
            'Nom et Prénom Examinateur 1' => '',
            'Nom et Prénom Examinateur 2' => '',
            'remarques' => '',
            'convention signée' => '',
            'accord encadrant interne' => '',
            'fiche evaluation entreprise' => '',
        ]);

        // Insert the holiday entry at the correct position
        $finalData = collect();
        $holidayInserted = false;
        
        foreach ($regularData as $entry) {
            if (!$holidayInserted && $entry['Date Soutenance'] > '2025-06-27') {
                $finalData->push($holidayData);
                $holidayInserted = true;
            }
            $finalData->push($entry);
        }

        // If holiday hasn't been inserted yet (it's the last date), add it at the end
        if (!$holidayInserted) {
            $finalData->push($holidayData);
        }

        $data = $finalData;
    } else {
        $data = $regularData;
    }

    return view('livewire.defense-calendar', ['data' => $data]);
})->name('globalDefenseCalendar');

Route::get('/internship/{internship}/applications/preview', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'preview'])
    ->name('internship.applications.preview')
    ->middleware('signed');

Route::get('/internship/{internship}/applications/export', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'export'])
    ->name('internship.applications.export')
    ->middleware('signed');
