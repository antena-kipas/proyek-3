<?php

namespace App\Http\Controllers;

use App\Models\Silabus;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Exception;

class SilabusDownloadController extends Controller
{
    public function __invoke(Silabus $silabus)
    {
        try {
            $templatePath = public_path('doc/template_silabus.docx');

            if (!file_exists($templatePath)) {
                abort(404, 'Template Silabus tidak ditemukan.');
            }

            $silabus->load([
                'user',
                'mataPelajaran',
                'kompetensiIntis',
                'kompetensiDasars',
                'indikators',
                'materiPelajaran',
                'kegiatanPembelajaran',
                'penilaianDiri'
            ]);

            $templateProcessor = new TemplateProcessor($templatePath);

            // Set single values
            $templateProcessor->setValue('kelas', $silabus->kelas ?? '');
            $templateProcessor->setValue('semester', $silabus->semester ?? '');
            $templateProcessor->setValue('temaid', $silabus->id_tema ?? '');
            $templateProcessor->setValue('tema_name', $silabus->tema ?? '');
            $templateProcessor->setValue('subtemaid', $silabus->id_subtema ?? '');
            $templateProcessor->setValue('sub_tema', $silabus->subtema ?? '');
            $templateProcessor->setValue('mata_pelajaran', $silabus->mataPelajaran->nama_pelajaran ?? '');
            $templateProcessor->setValue('tanggal', now()->translatedFormat('d F Y'));
            $templateProcessor->setValue('nama_guru', $silabus->user->name ?? '');

            // Process blocks
            $this->cloneBlock($templateProcessor, 'kompotensi_inti_block', $silabus->kompetensiIntis, function($item, $index) {
                return ['konten_kompetensi_inti' => $item->kompetensi_inti];
            });

            $this->cloneBlock($templateProcessor, 'kompetensi_dasar_block', $silabus->kompetensiDasars, function($item) {
                return ['konten_kompetensi_dasar' => $item->deskripsi_kd];
            });

            $this->cloneBlock($templateProcessor, 'indikator_block', $silabus->indikators, function($item) {
                return ['konten_indikator' => $item->deskripsi_indikator];
            });

            $this->cloneBlock($templateProcessor, 'materi_pembelajaran_block', $silabus->materiPelajaran, function($item) {
                return ['konten_materi_pembelajaran' => $item->materi_pelajaran];
            });

            $this->cloneBlock($templateProcessor, 'kegiatan_pembelajaran_block', $silabus->kegiatanPembelajaran, function($item) {
                return ['konten_kegiatan_pembelajaran' => $item->kegiatan_pembelajaran];
            });

            $this->cloneBlock($templateProcessor, 'penilaian_diri_block', $silabus->penilaianDiri, function($item) {
                return ['penilaian_diri' => $item->penilaian_diri];
            });

            // Save and download
            $safeTema = str_replace(' ', '_', $silabus->tema);
            $safeSubTema = str_replace(' ', '_', $silabus->subtema);
            $fileName = 'Silabus_' . $silabus->id . '_' . $safeTema . '_' . $safeSubTema . '.docx';
            
            Storage::makeDirectory('public/temp');
            $tempFilePath = Storage::path('public/temp/' . $fileName);

            $templateProcessor->saveAs($tempFilePath);

            return Response::download($tempFilePath, $fileName)->deleteFileAfterSend(true);

        } catch (Exception $e) {
            // Log the exception message for debugging
            report($e);
            // Return a user-friendly error
            abort(500, 'Terjadi kesalahan saat membuat dokumen Silabus.');
        }
    }

    private function cloneBlock(TemplateProcessor $templateProcessor, string $blockName, $items, callable $callback)
    {
        if ($items->count() > 0) {
            $replacements = [];
            foreach ($items as $index => $item) {
                $replacements[] = $callback($item, $index);
            }
            $templateProcessor->cloneBlock($blockName, 0, true, false, $replacements);
        } else {
            // If there are no items, remove the block
            $templateProcessor->cloneBlock($blockName, 1);
            $templateProcessor->setValue(str_replace(['_block', '${', '}'], '', $blockName), '');
        }
    }
}