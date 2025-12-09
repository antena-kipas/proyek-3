<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RppDownloadController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\SilabusDownloadController;
use App\Http\Controllers\GoogleDriveAuthController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/rpps/{rpp}/download', [RppDownloadController::class, 'download'])->name('rpps.download');
Route::get('/silabus/{silabus}/download', SilabusDownloadController::class)->name('silabus.download');
Route::get('/siswa/daftar-per-kelas', [App\Http\Controllers\SiswaController::class, 'daftarPerKelas'])->middleware('auth')->name('siswa.daftarPerKelas');

// Routes for Google Drive OAuth Token Generation
Route::get('/google/auth/redirect', [GoogleDriveAuthController::class, 'redirectToGoogle'])->name('google.auth.redirect');
Route::get('/google/auth/callback', [GoogleDriveAuthController::class, 'handleGoogleCallback'])->name('google.auth.callback');
