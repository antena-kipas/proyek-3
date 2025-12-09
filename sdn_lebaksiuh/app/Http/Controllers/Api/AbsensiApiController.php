<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Siswa;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperUser = $user->role === 'super-user';

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date_format:Y-m-d',
            'kelas_id' => $isSuperUser ? 'required|integer|exists:users,kelas' : 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $tanggal = Carbon::parse($validated['tanggal']);
        $kelasId = $isSuperUser ? $validated['kelas_id'] : $user->kelas;

        try {
            // 1. Get all students for the class (matching web logic, without status_aktif)
            $students = Siswa::where('kelas_sekarang', $kelasId)
                            ->orderBy('nama_lengkap', 'asc')
                            ->get(['id', 'nama_lengkap']);

            // 2. Get attendance for that specific day and class
            $attendanceRecords = Absensi::where('tanggal', $tanggal->toDateString())
                                        ->whereIn('siswa_id', $students->pluck('id'))
                                        ->get()
                                        ->keyBy('siswa_id'); // Key by student_id for easy lookup

            // 3. Combine student list with their attendance status
            $responseData = $students->map(function ($student) use ($attendanceRecords) {
                $status = $attendanceRecords->get($student->id)?->status ?? 'Hadir'; // Diubah ke Hadir
                return [
                    'id' => $student->id,
                    'nama_lengkap' => $student->nama_lengkap,
                    'status' => $status,
                ];
            });

            return response()->json($responseData);

        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal mengambil data absensi.'], 500);
        }
    }

    public function saveBulk(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperUser = $user->role === 'super-user';

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date_format:Y-m-d',
            'kelas_id' => $isSuperUser ? 'required|integer|exists:users,kelas' : 'nullable|integer',
            'statuses' => 'required|array',
            'statuses.*.id' => 'required|integer|exists:siswas,id',
            'statuses.*.status' => 'required|string|in:Hadir,Izin,Sakit,Alfa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $tanggal = Carbon::parse($validated['tanggal']);
        $kelasId = $isSuperUser ? $validated['kelas_id'] : $user->kelas;

        try {
            DB::transaction(function () use ($validated, $tanggal, $kelasId, $user) {
                foreach ($validated['statuses'] as $absen) {
                    Absensi::updateOrCreate(
                        [
                            'siswa_id' => $absen['id'],
                            'tanggal' => $tanggal->toDateString(),
                        ],
                        [
                            'status' => $absen['status'],
                            'kelas_saat_ini' => $kelasId,
                            'user_id' => $user->id,
                        ]
                    );
                }
            });

            return response()->json(['message' => 'Absensi berhasil disimpan.']);

        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Gagal menyimpan data absensi ke database.'], 500);
        }
    }
}
