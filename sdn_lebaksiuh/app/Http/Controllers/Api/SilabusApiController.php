<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Models\Silabus;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SilabusApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Ambil semua silabus milik user, eager load relasi mataPelajaran
            $silabus = $user->silabuses()->with('mataPelajaran')->latest()->get();

            return response()->json($silabus);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal mengambil daftar silabus.'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kelas' => 'required|string',
            'semester' => 'required|string',
            'id_tema' => 'required|numeric',
            'id_subtema' => 'required|numeric',
            'tema' => 'required|string',
            'subtema' => 'required|string',
            'mata_pelajaran_id' => 'required',
            
            'kompetensiIntis' => 'present|array',
            'kompetensiIntis.*.kompetensi_inti' => 'required|string',

            'kompetensiDasars' => 'present|array',
            'kompetensiDasars.*.deskripsi_kd' => 'required|string',
            
            'indikators' => 'present|array',
            'indikators.*.deskripsi_indikator' => 'required|string',

            'materiPelajaran' => 'present|array',
            'materiPelajaran.*.materi_pelajaran' => 'required|string',

            'kegiatanPembelajaran' => 'present|array',
            'kegiatanPembelajaran.*.kegiatan_pembelajaran' => 'required|string',

            'penilaianDiri' => 'present|array',
            'penilaianDiri.*.penilaian_diri' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            $silabus = DB::transaction(function () use ($validated, $request) {
                $user = $request->user();

                $silabusData = [
                    'user_id' => $user->id,
                    'kelas' => $validated['kelas'],
                    'semester' => $validated['semester'],
                    'id_tema' => $validated['id_tema'],
                    'id_subtema' => $validated['id_subtema'],
                    'tema' => $validated['tema'],
                    'subtema' => $validated['subtema'],
                    'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                ];
                
                // Create the main Silabus record
                $silabus = Silabus::create($silabusData);

                // Helper function to prepare data for relationships
                $prepareData = function($items, $mataPelajaranId) {
                    return array_map(fn($item) => ['mata_pelajaran_id' => $mataPelajaranId] + $item, $items);
                };

                // Create related records
                if (!empty($validated['kompetensiIntis'])) {
                    $silabus->kompetensiIntis()->createMany($validated['kompetensiIntis']);
                }
                if (!empty($validated['kompetensiDasars'])) {
                    $silabus->kompetensiDasars()->createMany($prepareData($validated['kompetensiDasars'], $validated['mata_pelajaran_id']));
                }
                if (!empty($validated['indikators'])) {
                    $silabus->indikators()->createMany($prepareData($validated['indikators'], $validated['mata_pelajaran_id']));
                }
                if (!empty($validated['materiPelajaran'])) {
                    $silabus->materiPelajaran()->createMany($prepareData($validated['materiPelajaran'], $validated['mata_pelajaran_id']));
                }
                if (!empty($validated['kegiatanPembelajaran'])) {
                    $silabus->kegiatanPembelajaran()->createMany($prepareData($validated['kegiatanPembelajaran'], $validated['mata_pelajaran_id']));
                }
                if (!empty($validated['penilaianDiri'])) {
                    $silabus->penilaianDiri()->createMany($prepareData($validated['penilaianDiri'], $validated['mata_pelajaran_id']));
                }

                return $silabus;
            });
            
            return response()->json($silabus, 201);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal menyimpan silabus ke database.', 'error' => $e->getMessage()], 500);
        }
    }

    public function generateDetails(Request $request, GeminiAIService $gemini): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kelas' => 'required',
            'semester' => 'required',
            'tema' => 'required|string',
            'subtema' => 'required|string',
            'mata_pelajaran_id' => 'required',
            'kompetensi_intis' => 'present|array',
            'kompetensi_dasars' => 'present|array',
            'indikators' => 'present|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $gemini->generateSilabusDetails($request->all());

            if ($result) {
                return response()->json($result);
            }

            return response()->json(['message' => 'Gagal menghasilkan detail dari AI.'], 500);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Terjadi kesalahan pada service AI.'], 500);
        }
    }
}
