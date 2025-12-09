<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rpp;
use App\Services\GeminiAIService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RppApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $rpps = $user->rpps()->with(['tujuanPembelajarans', 'muatan_terpadus', 'kegiatan_intis'])->get();

            return response()->json($rpps);
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal mengambil daftar RPP.'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:1,2',
            'pembelajaran_ke' => 'required|numeric',
            'tema_id' => 'required|numeric',
            'tema_name' => 'required|string|max:255',
            'sub_tema_id' => 'required|numeric',
            'sub_tema_name' => 'required|string|max:255',
            
            'tujuanPembelajarans' => 'required|array|min:1',
            'tujuanPembelajarans.*.tujuan_pembelajaran' => 'required|string',
            
            'muatanTerpadus' => 'required|array|min:1',
            'muatanTerpadus.*.mata_pelajaran' => 'required|string',
            
            'kegiatanIntis' => 'sometimes|array',
            'kegiatanIntis.*.kelompok' => 'required|string',
            'kegiatanIntis.*.konten' => 'required|string',
            'kegiatanIntis.*.urutan' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            $rpp = DB::transaction(function () use ($validated, $request) {
                $user = $request->user();

                // Create the main RPP record
                $rpp = $user->rpps()->create([
                    'semester' => $validated['semester'],
                    'pembelajaran_ke' => $validated['pembelajaran_ke'],
                    'tema_id' => $validated['tema_id'],
                    'tema_name' => $validated['tema_name'],
                    'sub_tema_id' => $validated['sub_tema_id'],
                    'sub_tema_name' => $validated['sub_tema_name'],
                ]);

                // Create related records
                $rpp->tujuanPembelajarans()->createMany($validated['tujuanPembelajarans']);
                $rpp->muatan_terpadus()->createMany($validated['muatanTerpadus']);

                if (!empty($validated['kegiatanIntis'])) {
                    // The frontend sends 'id' which is not in the DB schema, so we need to unset it.
                    $kegiatanIntisData = array_map(function($item) {
                        unset($item['id']);
                        return $item;
                    }, $validated['kegiatanIntis']);
                    
                    $rpp->kegiatan_intis()->createMany($kegiatanIntisData);
                }

                return $rpp;
            });

            return response()->json($rpp->load(['tujuanPembelajarans', 'muatan_terpadus', 'kegiatan_intis']), 201);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal menyimpan RPP ke database.'], 500);
        }
    }

    public function generateKegiatanInti(Request $request, GeminiAIService $geminiService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kelas' => 'required|string',
            'tema_name' => 'required|string',
            'sub_tema_name' => 'required|string',
            'tujuan_pembelajaran_string' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            $generatedActivities = $geminiService->generateKegiatanInti(
                $validated['kelas'],
                $validated['tema_name'],
                $validated['sub_tema_name'],
                $validated['tujuan_pembelajaran_string']
            );

            if (isset($generatedActivities['error'])) {
                return response()->json(['message' => 'Gagal menghasilkan kegiatan dari service AI.', 'details' => $generatedActivities['error']], 500);
            }

            return response()->json(['kegiatan_intis' => $generatedActivities]);

        } catch (Exception $e) {
            // Log the exception details for debugging
            report($e);
            
            return response()->json(['message' => 'Terjadi kesalahan internal pada server.'], 500);
        }
    }
}
