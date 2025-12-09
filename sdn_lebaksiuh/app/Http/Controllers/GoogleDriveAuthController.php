<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleDriveAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $client->setRedirectUri(route('google.auth.callback'));
        $client->setScopes([GoogleDriveService::DRIVE]);
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // Memaksa untuk selalu menampilkan dialog persetujuan & mendapatkan refresh token

        // Logging the exact redirect URI
        Log::info('Generated Redirect URI for Google: ' . route('google.auth.callback'));

        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return 'Error: Tidak ada kode otorisasi dari Google.';
        }

        try {
            $client = new GoogleClient();
            $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
            $client->setRedirectUri(route('google.auth.callback'));
            
            $accessToken = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($accessToken['refresh_token'])) {
                $refreshToken = $accessToken['refresh_token'];
                return view('google-token', ['token' => $refreshToken]);
            } else {
                return 'Error: Refresh token tidak diterima dari Google. Pastikan Anda memberikan persetujuan (consent) dan coba lagi. Jika Anda baru saja membuat token, token lama mungkin masih aktif. Coba cabut akses aplikasi dari akun Google Anda dan ulangi proses ini.';
            }

        } catch (\Exception $e) {
            return 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
