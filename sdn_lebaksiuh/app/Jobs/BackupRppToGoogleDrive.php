<?php

namespace App\Jobs;

use App\Models\Rpp;
use App\Models\User;
use Filament\Notifications\Notification;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
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
            // Generate DOCX file and get its path and standardized name
            list($tempFilePath, $fileName) = $this->generateDocx();

            // Setup Google Client and Service
            $client = $this->setupGoogleClient();
            $driveService = new Google_Service_Drive($client);

            // Check if file already exists in Google Drive
            if ($this->fileExistsInDrive($fileName, $driveService)) {
                // If exists, skip upload and delete local file
                Log::info('File "' . $fileName . '" already exists in Google Drive. Skipping upload.');
                Storage::disk('local')->delete($tempFilePath);

                Notification::make()
                    ->title('Backup Dilewati')
                    ->body('RPP \'' . $this->rpp->tema_name . '\' sudah ada di Google Drive.')
                    ->info()
                    ->sendToDatabase($this->user);

            } else {
                // If not exists, upload to Google Drive
                Log::info('File "' . $fileName . '" not found in Google Drive. Proceeding with upload.');
                $fileId = $this->uploadToDrive($tempFilePath, $fileName, $driveService);

                // Save the Google Drive file ID to the RPP record
                Rpp::where('id', $this->rpp->id)->update(['google_drive_file_id' => $fileId]);
                Log::info('Google Drive File ID ' . $fileId . ' saved for RPP ID: ' . $this->rpp->id);

                // Delete temporary file
                Storage::disk('local')->delete($tempFilePath);
                Log::info('Temporary file deleted: ' . $tempFilePath);

                Notification::make()
                    ->title('Backup Berhasil')
                    ->body('RPP \'' . $this->rpp->tema_name . '\' berhasil di-backup ke Google Drive.')
                    ->success()
                    ->sendToDatabase($this->user);
            }

            Log::info('BackupRppToGoogleDrive job finished successfully for RPP ID: ' . $this->rpp->id);

        } catch (Throwable $e) {
            Log::error('An error occurred in BackupRppToGoogleDrive job: ' . $e->getMessage(), ['exception' => $e]);
            Notification::make()
                ->title('Backup Gagal')
                ->body('Terjadi kesalahan saat mem-backup RPP \'' . $this->rpp->tema_name . '\'.')
                ->danger()
                ->sendToDatabase($this->user);
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

        // Fill template (existing logic)
        $templateProcessor->setValue('kelas', $rpp->user->kelas);
        $templateProcessor->setValue('semester', $rpp->semester);
        $templateProcessor->setValue('tema_name', $rpp->tema_name);
        $templateProcessor->setValue('sub_tema', $rpp->sub_tema_name);
        $templateProcessor->setValue('pembelajaran_ke', $rpp->pembelajaran_ke);
        $templateProcessor->setValue('tanggal', now()->format('d F Y'));
        $templateProcessor->setValue('user_name', $rpp->user->name);
        $muatanTerpaduNames = $rpp->muatan_terpadus->pluck('mata_pelajaran')->toArray();
        $templateProcessor->setValue('daftar_nama_pelajaran', implode(', ', $muatanTerpaduNames));
        $tujuanPembelajaranData = [];
        foreach ($rpp->tujuanPembelajarans as $index => $tujuan) {
            $tujuanPembelajaranData[] = ['urutan' => $index + 1, 'konten_tujuan' => $tujuan->tujuan_pembelajaran];
        }
        $templateProcessor->cloneBlock('tujuan_pembelajaran_block', count($tujuanPembelajaranData), true, false, $tujuanPembelajaranData);
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_mengamati', 'ayo_mengamati_block', 'urutan_ayo_mengamati', 'konten_mengamati');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berdiskusi', 'ayo_berdiskusi_block', 'urutan_ayo_berdiskusi', 'konten_berdiskusi');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_membaca', 'ayo_membaca_block', 'urutan_ayo_membaca', 'konten_membaca');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_berlatih', 'ayo_berlatih_block', 'urutan_ayo_berlatih', 'konten_berlatih');
        $this->processKegiatanIntiBlock($templateProcessor, $rpp->kegiatan_intis, 'ayo_renungkan', 'ayo_renungkan_block', 'urutan_ayo_renungkan', 'konten_renungkan');

        // Sanitize parts of the filename by replacing spaces with underscores
        $safeSubTema = str_replace(' ', '_', $rpp->sub_tema_name);
        $safePembelajaran = str_replace(' ', '_', $rpp->pembelajaran_ke);

        // Create the new standardized filename
        $fileName = 'RPP_' . $rpp->id . '_' . $safeSubTema . '_' . $safePembelajaran . '.docx';

        $tempDir = 'temp_backups';
        Storage::disk('local')->makeDirectory($tempDir);
        $tempFilePath = $tempDir . '/' . $fileName;
        $fullTempPath = Storage::disk('local')->path($tempFilePath);
        $templateProcessor->saveAs($fullTempPath);
        Log::info('Temporary file created at: ' . $fullTempPath);

        return [$tempFilePath, $fileName];
    }

    private function setupGoogleClient(): Google_Client
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');

        // Set the user's token
        $accessToken = [
            'access_token' => $this->user->google_access_token,
            'refresh_token' => $this->user->google_refresh_token,
            'expires_in' => $this->user->google_token_expires_at->getTimestamp() - time(),
        ];
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired
        if ($client->isAccessTokenExpired()) {
            Log::info('Google Access Token is expired. Refreshing...');
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $newAccessToken = $client->getAccessToken();

            // Update the user's record with the new token
            $this->user->update([
                'google_access_token' => $newAccessToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newAccessToken['expires_in']),
            ]);
            Log::info('Google Access Token refreshed and updated for User ID: ' . $this->user->id);
        }
        
        return $client;
    }

    private function fileExistsInDrive(string $fileName, Google_Service_Drive $driveService): bool
    {
        $folderId = env('GOOGLE_DRIVE_FOLDER_ID');
        // Use addslashes to escape single quotes in the filename
        $query = "name = '" . addslashes($fileName) . "' and '" . $folderId . "' in parents and trashed = false";

        $optParams = [
            'q' => $query,
            'fields' => 'files(id)',
            'pageSize' => 1
        ];

        try {
            $results = $driveService->files->listFiles($optParams);
            return count($results->getFiles()) > 0;
        } catch (Throwable $e) {
            Log::error('Error checking file existence in Google Drive: ' . $e->getMessage());
            // If we can't check, assume it doesn't exist to allow backup attempt
            return false;
        }
    }

    private function uploadToDrive(string $tempFilePath, string $fileName, Google_Service_Drive $driveService): string
    {
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')]
        ]);

        $content = Storage::disk('local')->get($tempFilePath);
        $createdFile = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        Log::info('Successfully uploaded to Google Drive. File ID: ' . $createdFile->id);
        return $createdFile->id;
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
