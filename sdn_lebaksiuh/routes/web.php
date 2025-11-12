<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RppDownloadController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\GoogleDriveController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/rpps/{rpp}/download', [RppDownloadController::class, 'download'])->name('rpps.download');

Route::get('/auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

// Routes for Google Drive Authentication
Route::get('/drive/redirect', [GoogleDriveController::class, 'redirect'])->name('drive.redirect');
Route::get('/drive/callback', [GoogleDriveController::class, 'callback'])->name('drive.callback');
