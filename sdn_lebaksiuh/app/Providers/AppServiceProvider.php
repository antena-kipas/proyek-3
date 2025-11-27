<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            Storage::extend('google', function (Application $app, array $config) {
                
                // 1. Setup Google Client (OAuth)
                $client = new GoogleClient();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                // 2. Setup Service
                $service = new GoogleDriveService($client);
                
                $options = [];
                if (isset($config['teamDriveId'])) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                // --- PERBAIKAN LOGIKA DI SINI ---
                
                $rootFolderId = $config['folder_id'] ?? 'root';
                $rootPath = 'root'; // Default fallback

                if ($rootFolderId !== 'root') {
                    try {
                        // KITA TANYA NAMANYA DULU KE GOOGLE
                        // Karena Adapter butuh NAMA/PATH, bukan ID.
                        $folder = $service->files->get($rootFolderId, ['fields' => 'name']);
                        $rootPath = $folder->getName();
                        
                        Log::info("Mengubah ID '$rootFolderId' menjadi Nama Folder: '$rootPath'");
                    } catch (\Exception $e) {
                        Log::warning("Gagal menemukan nama folder dari ID. Kembali ke root. Error: " . $e->getMessage());
                    }
                }

                // 3. Masukkan NAMA FOLDER (bukan ID) ke Adapter
                $adapter = new GoogleDriveAdapter($service, $rootPath, $options);
                
                // 4. Bungkus Filesystem
                $driver = new Filesystem($adapter);

                return new FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            Log::error('Gagal memuat Google Drive Storage: ' . $e->getMessage());
        }
    }
}