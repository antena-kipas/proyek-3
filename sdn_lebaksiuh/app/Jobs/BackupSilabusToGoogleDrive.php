<?php

namespace App\Jobs;

use App\Models\Silabus;
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

class BackupSilabusToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Silabus $silabus, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('BackupSilabusToGoogleDrive job started for Silabus ID: ' . $this->silabus->id . ' by User ID: ' . $this->user->id);
        try {
            // 1. Generate DOCX Local
            list($localTempPath, $fileName) = $this->generateDocx();
            $content = Storage::disk('local')->get($localTempPath);

            $googleDisk = Storage::disk('google_silabus');

            // 2. Cek Eksistensi di Google Drive (Folder Target)
            if ($googleDisk->exists($fileName)) {
                Log::info('File "' . $fileName . '" already exists in Google Drive. Skipping upload.');

                Notification::make()
                    ->title('Backup Dilewati')
                    ->body('Silabus \'' . $this->silabus->tema . '\' sudah ada di Google Drive.')
                    ->info()
                    ->sendToDatabase($this->user);
            } else {
                // 3. Upload ke Google Drive
                Log::info('File "' . $fileName . '" not found. Uploading...');
                
                $googleDisk->put($fileName, $content);
                
                Log::info('Successfully uploaded "' . $fileName . '" to Google Drive.');

                Notification::make()
                    ->title('Backup Berhasil')
                    ->body('Silabus \'' . $this->silabus->tema . '\' berhasil di-backup ke Google Drive.')
                    ->success()
                    ->sendToDatabase($this->user);
            }

            // 4. Bersihkan File Temp Lokal
            Storage::disk('local')->delete($localTempPath);
            Log::info('Temporary file deleted: ' . $localTempPath);
            Log::info('Job finished successfully for Silabus ID: ' . $this->silabus->id);

        } catch (Throwable $e) {
            Log::error('Backup Error: ' . $e->getMessage(), ['exception' => $e]);
            
            Notification::make()
                ->title('Backup Gagal')
                ->body('Terjadi kesalahan saat mem-backup Silabus.')
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
        $silabus = $this->silabus;
        $silabus->load(['user', 'mataPelajaran', 'kompetensiDasars', 'indikators']);

        $templatePath = public_path('doc/template_silabus.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('Template Silabus not found at: ' . $templatePath);
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Fill simple values
        $templateProcessor->setValue('nama_sekolah', 'SDN Lebaksiuh'); // Asumsi statis
        $templateProcessor->setValue('kelas', $silabus->kelas ?? '-');
        $templateProcessor->setValue('semester', $silabus->semester ?? '-');
        $templateProcessor->setValue('nama_guru', $silabus->user->name ?? '-');
        $templateProcessor->setValue('nip_guru', $silabus->user->nip ?? '-'); // Asumsi ada NIP di model User
        $templateProcessor->setValue('id_tema', $silabus->id_tema ?? '-');
        $templateProcessor->setValue('tema', $silabus->tema ?? '-');
        $templateProcessor->setValue('id_subtema', $silabus->id_subtema ?? '-');
        $templateProcessor->setValue('sub_tema', $silabus->sub_tema ?? '-');
        $templateProcessor->setValue('nama_pelajaran', $silabus->mataPelajaran->nama_pelajaran ?? '-');

        // Block cloning for Kompetensi Dasar
        $kdData = [];
        foreach ($silabus->kompetensiDasars as $index => $kd) {
            $kdData[] = ['urutan_kd' => $index + 1, 'konten_kd' => $kd->kompetensi_dasar];
        }
        $templateProcessor->cloneBlock('kd_block', count($kdData), true, false, $kdData);

        // Block cloning for Indikator
        $indikatorData = [];
        foreach ($silabus->indikators as $index => $indikator) {
            $indikatorData[] = ['urutan_indikator' => $index + 1, 'konten_indikator' => $indikator->indikator];
        }
        $templateProcessor->cloneBlock('indikator_block', count($indikatorData), true, false, $indikatorData);


        // Filename standardization
        $safeTema = str_replace(' ', '_', $silabus->tema);
        $safeSubTema = str_replace(' ', '_', $silabus->sub_tema);
        $fileName = 'SILABUS_' . $silabus->id . '_' . $safeTema . '_' . $safeSubTema . '.docx';

        // Save to temp
        $tempDir = 'temp_backups';
        Storage::disk('local')->makeDirectory($tempDir);
        $tempFilePath = $tempDir . '/' . $fileName;
        $fullTempPath = Storage::disk('local')->path($tempFilePath);
        
        $templateProcessor->saveAs($fullTempPath);
        Log::info('Temporary file created at: ' . $fullTempPath);

        return [$tempFilePath, $fileName];
    }
}