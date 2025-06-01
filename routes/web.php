<?php

use App\Facades\GlobalDefenseCalendarConnector;
use App\Http\Controllers\AgreementVerificationController;
use App\Http\Controllers\DefenseCalendarController;
use App\Http\Controllers\DiplomaVerificationController;
use App\Http\Controllers\PVVerificationController;
use App\Http\Controllers\QrUrlDecoder;
use App\Models\DefenseSync;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('health', HealthCheckResultsController::class);

Route::get('/', function () {
    $internshipOffersCount = \App\Models\InternshipOffer::withoutTrashed()
        ->where('status', 'Published')
        ->where('internship_level', 'FinalYearInternship')->count();

    return view('welcome', [
        'internshipOffersCount' => $internshipOffersCount,
    ]);
})->name('home');

Route::get('lang/{lang}', 'App\Http\Controllers\LanguageController@switchLang');

Route::get('/verify-agreement', QrUrlDecoder::class);
Route::get('/verify-diploma/{verification_code}', DiplomaVerificationController::class)->name('diploma.verify');
Route::get('/verify-diploma/', DiplomaVerificationController::class);
Route::get('/verify-deliberation-pv/{verification_code}', PVVerificationController::class)->name('deliberation-pv.verify');
Route::get('/verify-agreement/{verification_code}', AgreementVerificationController::class)->name('internship-agreement.verify');

Route::get('/publier-un-stage', \App\Livewire\NewInternship::class)->name('new-internship-fr');
Route::get('/publish-internship', \App\Livewire\NewInternship::class)->name('new-internship');
Route::get('/publier-un-evenement', \App\Livewire\NewMidweekEvent::class)->name('new-midweek-event');

// Defense calendar routes
Route::get('/soutenances', \App\Livewire\DefenseCalendar::class)->name('globalDefenseCalendar');

// Internship application routes
Route::get('/internship/{internship}/applications/preview', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'preview'])
    ->name('internship.applications.preview')
    ->middleware('signed');

Route::get('/internship/{internship}/applications/export', [\App\Http\Controllers\InternshipApplicationPreviewController::class, 'export'])
    ->name('internship.applications.export')
    ->middleware('signed');
