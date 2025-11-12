<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;

use Exception;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path(env('GOOGLE_DRIVE_CREDENTIALS_FILENAME')));
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->service = new Google_Service_Drive($this->client);
    }

    /**
     * Upload file ke folder Google Drive.
     *
     * @param string $localPath Path lokal file, misalnya: storage_path('app/silabus/Silabus_1.docx')
     * @param string $folderId  ID folder Google Drive tujuan
     * @return string|null ID file di Drive, atau null jika gagal
     */
    public function uploadFile($localPath, $folderId)
    {
        if (!file_exists($localPath)) {
            Log::error('Google Drive Upload: File not found at path: ' . $localPath);
            return null;
        }

        try {
            $file = new Google_Service_Drive_DriveFile([
                'name' => basename($localPath),
                'parents' => [$folderId],
            ]);

            $content = file_get_contents($localPath);

            Log::info('Google Drive Upload: Attempting to upload ' . basename($localPath) . ' to folder ' . $folderId);

            $createdFile = $this->service->files->create($file, [
                'data' => $content,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id',
            ]);

            Log::info('Google Drive Upload: File created with ID: ' . $createdFile->id);

            if ($createdFile) {
                Log::info('Google Drive Upload: Attempting to give writer permission to ' . env('GOOGLE_DRIVE_OWNER_EMAIL'));
                
                $userPermission = new Google_Service_Drive_Permission([
                    'type' => 'user',
                    'role' => 'writer', // Change owner to writer
                    'emailAddress' => 'tingtest77777@gmail.com', // Hardcoded for testing
                ]);

                $this->service->permissions->create(
                    $createdFile->id,
                    $userPermission,
                    [
                        'supportsAllDrives' => true, // Keep for compatibility
                    ]
                );

                Log::info('Google Drive Upload: Writer permission granted successfully.');
            }

            return $createdFile->id;

        } catch (Exception $e) {
            // Tangkap exception dari Google API Client
            $error = json_decode($e->getMessage(), true);
            $errorMessage = $error['error']['message'] ?? $e->getMessage();
            
            Log::error('Google Drive API Error: ' . $errorMessage, [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Lemparkan kembali exception agar job gagal dan bisa di-retry jika perlu
            throw $e;
        }
    }
}
