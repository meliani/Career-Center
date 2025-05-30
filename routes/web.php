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
    // $connector = new GlobalDefenseCalendarConnector;
    // $data = $connector->getDefenses(); // Assuming fetchData() is a method to get the data

    // Import at the top of the file:
    // use App\Models\Timetable;
    $data = \App\Models\Timetable::planned()
        ->whereHas('timeslot', function ($query) {
            $query->where('year_id', 8);
        })
        ->with(['project.agreements.agreeable.student', 'timeslot', 'room'])
        ->get()
        ->filter(function ($timetable) {
            return $timetable->project !== null;
        })
        ->map(function ($timetable) {
            $project = $timetable->project;
            $students = $project?->agreements->map(function($agreement) {
                return optional($agreement->agreeable?->student);
            })->filter();
            $studentNames = $students->map(fn($s) => $s->full_name)->join(' & ');
            $studentIds = $students->map(fn($s) => $s->id_pfe)->join(' & ');
            $studentFiliere = $students->map(fn($s) => $s->program?->getLabel())->unique()->join(' / ');
            return collect([
                'Date Soutenance' => optional($timetable->timeslot)->start_time?->format('Y-m-d'),
                'Heure' => optional($timetable->timeslot)->start_time?->format('H:i'),
                'Autorisation' => $project?->isAuthorized() ? 'Autorisé' : 'En Attente',
                'Lieu' => optional($timetable->room)->name,
                'ID PFE' =>  $studentIds,
                "Nom de l'étudiant" => $studentNames ?: 'Libre',
                'Filière' => $studentFiliere,
                'Encadrant Interne' => $project?->academic_supervisor()?->name,
                'Nom et Prénom Examinateur 1' => $project?->first_reviewer()?->name,
                'Nom et Prénom Examinateur 2' => $project?->second_reviewer()?->name,
                'remarques' => '',
                'convention signée' => '',
                'accord encadrant interne' => '',
                'fiche evaluation entreprise' => '',
            ]);
        });

    return view('livewire.defense-calendar', ['data' => $data]);
})->name('globalDefenseCalendar');

Route::get('/internship/{internship}/applications/preview', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'preview'])
    ->name('internship.applications.preview')
    ->middleware('signed');

Route::get('/internship/{internship}/applications/export', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'export'])
    ->name('internship.applications.export')
    ->middleware('signed');
