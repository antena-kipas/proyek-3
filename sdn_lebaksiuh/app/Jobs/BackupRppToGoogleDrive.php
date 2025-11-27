<?php

namespace App\Jobs;

use App\Models\Rpp;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Throwable;

class BackupRppToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Rpp $rpp, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('BackupRppToGoogleDrive job started for RPP ID: ' . $this->rpp->id . ' by User ID: ' . $this->user->id);
        try {
            // 1. Generate DOCX Local
            list($localTempPath, $fileName) = $this->generateDocx();
            $content = Storage::disk('local')->get($localTempPath);

            $googleDisk = Storage::disk('google');

            // 2. Cek Eksistensi di Google Drive (Folder Target)
            if ($googleDisk->exists($fileName)) {
                Log::info('File "' . $fileName . '" already exists in Google Drive. Skipping upload.');

                Notification::make()
                    ->title('Backup Dilewati')
                    ->body('RPP \'' . $this->rpp->tema_name . '\' sudah ada di Google Drive.')
                    ->info()
                    ->sendToDatabase($this->user);
            } else {
                // 3. Upload ke Google Drive
                // (Otomatis masuk ke folder 1AGG... karena konfigurasi AppServiceProvider)
                Log::info('File "' . $fileName . '" not found. Uploading...');
                
                $googleDisk->put($fileName, $content);
                
                Log::info('Successfully uploaded "' . $fileName . '" to Google Drive.');

                Notification::make()
                    ->title('Backup Berhasil')
                    ->body('RPP \'' . $this->rpp->tema_name . '\' berhasil di-backup ke Google Drive.')
                    ->success()
                    ->sendToDatabase($this->user);
                
                // Opsional: Update status di DB lokal jika perlu
                // $this->rpp->update(['last_backup_at' => now()]);
            }

            // 4. Bersihkan File Temp Lokal
            Storage::disk('local')->delete($localTempPath);
            Log::info('Temporary file deleted: ' . $localTempPath);
            Log::info('Job finished successfully for RPP ID: ' . $this->rpp->id);

        } catch (Throwable $e) {
            Log::error('Backup Error: ' . $e->getMessage(), ['exception' => $e]);
            
            Notification::make()
                ->title('Backup Gagal')
                ->body('Terjadi kesalahan saat mem-backup RPP.')
                ->danger()
                ->sendToDatabase($this->user);
            
            // Hapus file temp jika error terjadi setelah generate
            if (isset($localTempPath) && Storage::disk('local')->exists($localTempPath)) {
                Storage::disk('local')->delete($localTempPath);
            }
            
            report($e);
        }
    }

    private function generateDocx(): array
    {
        $rpp = $this->rpp;
        $rpp->load(['muatan_terpadus', 'kegiatan_intis', 'tujuanPembelajarans', 'user']);

        $templatePath = public_path('doc/template_rpp.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('Template RPP not found at: ' . $templatePath);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Fill template simple values
        $templateProcessor->setValue('kelas', $rpp->user->kelas ?? '-');
        $templateProcessor->setValue('semester', $rpp->semester);
        $templateProcessor->setValue('tema_name', $rpp->tema_name);
        $templateProcessor->setValue('sub_tema', $rpp->sub_tema_name);
        $templateProcessor->setValue('pembelajaran_ke', $rpp->pembelajaran_ke);
        $templateProcessor->setValue('tanggal', now()->format('d F Y'));
        $templateProcessor->setValue('user_name', $rpp->user->name);

        // Fill array values
        $muatanTerpaduNames = $rpp->muatan_terpadus->pluck('mata_pelajaran')->toArray();
        $templateProcessor->setValue('daftar_nama_pelajaran', implode(', ', $muatanTerpaduNames));

        // Block cloning for Tujuan Pembelajaran
        $tujuanPembelajaranData = [];
        foreach ($rpp->tujuanPembelajarans as $index => $tujuan) {
            $tujuanPembelajaranData[] = ['urutan' => $index + 1, 'konten_tujuan' => $tujuan->tujuan_pembelajaran];
        }
        $templateProcessor->cloneBlock('tujuan_pembelajaran_block', count($tujuanPembelajaranData), true, false, $tujuanPembelajaranData);

        // Block cloning for Kegiatan Inti
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_mengamati', 'ayo_mengamati_block', 'urutan_ayo_mengamati', 'konten_mengamati');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berdiskusi', 'ayo_berdiskusi_block', 'urutan_ayo_berdiskusi', 'konten_berdiskusi');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_membaca', 'ayo_membaca_block', 'urutan_ayo_membaca', 'konten_membaca');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berlatih', 'ayo_berlatih_block', 'urutan_ayo_berlatih', 'konten_berlatih');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_renungkan', 'ayo_renungkan_block', 'urutan_ayo_renungkan', 'konten_renungkan');

        // Filename standardization
        $safeSubTema = str_replace(' ', '_', $rpp->sub_tema_name);
        $safePembelajaran = str_replace(' ', '_', $rpp->pembelajaran_ke);
        $fileName = 'RPP_' . $rpp->id . '_' . $safeSubTema . '_' . $safePembelajaran . '.docx';

        // Save to temp
        $tempDir = 'temp_backups';
        Storage::disk('local')->makeDirectory($tempDir);
        $tempFilePath = $tempDir . '/' . $fileName;
        $fullTempPath = Storage::disk('local')->path($tempFilePath);
        
        $templateProcessor->saveAs($fullTempPath);
        Log::info('Temporary file created at: ' . $fullTempPath);

        return [$tempFilePath, $fileName];
    }
    
    private function processKegiatanIntiBlock(TemplateProcessor $templateProcessor, $kegiatanIntis, string $kelompokName, string $blockName, string $urutanPlaceholder, string $kontenPlaceholder): void
    {
        $filteredActivities = $kegiatanIntis->where('kelompok', $kelompokName);
        $data = [];
        $i = 1;
        foreach ($filteredActivities as $activity) {
            $data[] = [
                $urutanPlaceholder => $i++,
                $kontenPlaceholder => $activity->konten,
            ];
        }

        if (!empty($data)) {
            $templateProcessor->cloneBlock($blockName, 0, true, false, $data);
        } else {
            $templateProcessor->deleteBlock($blockName);
        }
    }
}