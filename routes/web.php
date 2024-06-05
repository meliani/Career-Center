<?php

use App\Filament\Org\Pages;
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
// Route::get('/qr-response', Pages\QrResponse::class)->name('qr-response');

Route::get('/mail-preview/{email}', 'App\Http\Controllers\MailPreviewController@show');
