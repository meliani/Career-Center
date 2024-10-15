<?php

use App\Facades\GlobalDefenseCalendarConnector;
use App\Filament\Org\Pages;
use App\Http\Controllers\DiplomaVerificationController;
use App\Http\Controllers\PVVerificationController;
use App\Http\Controllers\QrUrlDecoder;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
})->name('home');
Route::get('lang/{lang}', 'App\Http\Controllers\LanguageController@switchLang');

// Route::get('/', function () {
//     return redirect('/backend');
// });
// Route::get('/programCoordinator', function () {
//     return redirect('/programCoordinator/login');
// });

// Route::get('/url/{version}/{cipher}', Pages\QrResponse::class);
// Route::get('/url', QrUrlDecoder::class);
Route::get('/verify-agreement', QrUrlDecoder::class);
Route::get(
    '/verify-diploma/{verification_code}',
    DiplomaVerificationController::class
)
    ->name('diploma.verify');
Route::get(
    '/verify-diploma/',
    DiplomaVerificationController::class
);
Route::get(
    '/verify-deliberation-pv/{verification_code}',
    PVVerificationController::class
)
    ->name('deliberation-pv.verify');
// Route::get('/qr-response', Pages\QrResponse::class)->name('qr-response');

// Route::get('/mail-preview/{email}', 'App\Http\Controllers\MailPreviewController@show');

// Route::get('/public-internship-offer-form', \App\Livewire\PublicInternshipOfferForm::class);

Route::get('/publier-un-stage', \App\Livewire\NewInternship::class)->name('new-internship-fr');
Route::get('/publish-internship', \App\Livewire\NewInternship::class)->name('new-internship');
// Route::get('/publier-une-offre-demploi', \App\Livewire\NewJobOffer::class)->name('new-job-offer');

Route::get('/publier-un-evenement', \App\Livewire\NewMidweekEvent::class)->name('new-midweek-event');

Route::get('/soutenances', function () {
    $connector = new GlobalDefenseCalendarConnector;
    $data = $connector->getDefenses(); // Assuming fetchData() is a method to get the data

    return view('livewire.defense-calendar', ['data' => $data]);
})->name('globalDefenseCalendar');
