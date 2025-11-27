<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RppDownloadController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Controllers\SilabusDownloadController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/rpps/{rpp}/download', [RppDownloadController::class, 'download'])->name('rpps.download');
Route::get('/silabus/{silabus}/download', SilabusDownloadController::class)->name('silabus.download');
