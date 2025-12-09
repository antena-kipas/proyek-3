<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RppApiController;
use App\Http\Controllers\Api\AbsensiApiController;
use App\Http\Controllers\Api\SilabusApiController;
use App\Http\Controllers\Api\MataPelajaranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // RPP Routes
    Route::post('/rpp/generate-kegiatan-inti', [RppApiController::class, 'generateKegiatanInti']);
    Route::post('/rpps', [RppApiController::class, 'store']);
    Route::get('/rpps', [RppApiController::class, 'index']);

    // Absensi Routes
    Route::get('/absensi', [AbsensiApiController::class, 'index']);
    Route::post('/absensi/simpan', [AbsensiApiController::class, 'saveBulk']);

    // Silabus Routes
    Route::get('/silabus', [SilabusApiController::class, 'index']);
    Route::post('/silabus/generate-details', [SilabusApiController::class, 'generateDetails']);
    Route::post('/silabus', [SilabusApiController::class, 'store']);

    // Master Data Routes
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index']);
});
