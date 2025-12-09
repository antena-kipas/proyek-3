<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MataPelajaranController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $mataPelajaran = MataPelajaran::all();
            return response()->json($mataPelajaran);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal mengambil data mata pelajaran.'], 500);
        }
    }
}
