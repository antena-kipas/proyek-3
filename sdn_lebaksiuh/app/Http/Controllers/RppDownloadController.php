<?php

namespace App\Http\Controllers;

use App\Models\Rpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class RppDownloadController extends Controller
{
    public function download(Rpp $rpp)
    {
        $rpp->load(['muatan_terpadus', 'kegiatan_intis', 'tujuanPembelajarans', 'user']);

        // Path to your template .docx file
        $templatePath = public_path('doc/template_rpp.docx');

        // Ensure the template file exists
        if (!file_exists($templatePath)) {
            abort(404, 'Template RPP not found.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Fill basic RPP details
        $templateProcessor->setValue('kelas', $rpp->user->kelas);
        $templateProcessor->setValue('semester', $rpp->semester);
        $templateProcessor->setValue('tema_name', $rpp->tema_name);
        $templateProcessor->setValue('sub_tema', $rpp->sub_tema_name); // Assuming sub_tema_name exists
        $templateProcessor->setValue('pembelajaran_ke', $rpp->pembelajaran_ke);
        $templateProcessor->setValue('tanggal', now()->format('d F Y')); // Current date
        $templateProcessor->setValue('user_name', $rpp->user->name);

        // Handle Muatan Terpadu (comma-separated mata pelajaran)
        $muatanTerpaduNames = $rpp->muatan_terpadus->pluck('mata_pelajaran')->toArray(); // Corrected field name
        $templateProcessor->setValue('daftar_nama_pelajaran', implode(', ', $muatanTerpaduNames));

        // Handle Tujuan Pembelajaran (numbered list)
        $tujuanPembelajaranData = [];
        foreach ($rpp->tujuanPembelajarans as $index => $tujuan) {
            $tujuanPembelajaranData[] = [
                'urutan' => $index + 1,
                'konten_tujuan' => $tujuan->tujuan_pembelajaran // Corrected field name
            ];
        }
        if (!empty($tujuanPembelajaranData)) {
            $templateProcessor->cloneBlock('tujuan_pembelajaran_block', 0, true, false, $tujuanPembelajaranData);
        } else {
            $templateProcessor->cloneBlock('tujuan_pembelajaran_block', 0, true, true);
        }

        // Handle Kegiatan Inti sub-sections (Ayo Mengamati, Ayo Berdiskusi, etc.)

        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_mengamati', 'ayo_mengamati_block', 'urutan_ayo_mengamati', 'konten_mengamati');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berdiskusi', 'ayo_berdiskusi_block', 'urutan_ayo_berdiskusi', 'konten_berdiskusi');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_membaca', 'ayo_membaca_block', 'urutan_ayo_membaca', 'konten_membaca'); // Corrected placeholder name
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berlatih', 'ayo_berlatih_block', 'urutan_ayo_berlatih', 'konten_berlatih');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_renungkan', 'ayo_renungkan_block', 'urutan_ayo_renungkan', 'konten_renungkan');

        // Save the generated document to a temporary file
        // Sanitize parts of the filename by replacing spaces with underscores
        $safeSubTema = str_replace(' ', '_', $rpp->sub_tema_name);
        $safePembelajaran = str_replace(' ', '_', $rpp->pembelajaran_ke);

        // Create the new standardized filename
        $fileName = 'RPP_' . $rpp->id . '_' . $safeSubTema . '_' . $safePembelajaran . '.docx';
        $tempFilePath = Storage::path('public/temp/' . $fileName); // Use storage for temporary files

        // Ensure the directory exists
        Storage::makeDirectory('public/temp');

        $templateProcessor->saveAs($tempFilePath);

        // Return the file as a download response
        return Response::download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    private function processKegiatanIntiBlock(TemplateProcessor $templateProcessor, $kegiatanIntis, string $kelompokName, string $blockName, string $urutanPlaceholder, string $kontenPlaceholder): void
    {
        $filteredActivities = $kegiatanIntis->where('kelompok', $kelompokName);
        $data = [];
        foreach ($filteredActivities as $index => $activity) {
            $data[] = [
                $urutanPlaceholder => $index + 1,
                $kontenPlaceholder => $activity->konten,
            ];
        }

        if (!empty($data)) {
            $templateProcessor->cloneBlock($blockName, 0, true, false, $data);
        } else {
            $templateProcessor->cloneBlock($blockName, 0, true, true);
        }
    }
}
