<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RppDownloadController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/rpps/{rpp}/download', [RppDownloadController::class, 'download'])->name('rpps.download');
