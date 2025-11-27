<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // We request 'offline' access to receive a refresh token.
        // The 'drive' scope is necessary to manage files in Google Drive.
        $scopes = [
            'https://www.googleapis.com/auth/drive'
        ];
        
        return Socialite::driver('google')
            ->scopes($scopes)
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return 'Error: ' . $request->get('error') . '<p>Description: ' . $request->get('error_description') . '</p>';
        }

        try {
            // Using stateless() because we are not creating a persistent user session.
            // We only need to retrieve the token from the authorization response.
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $refreshToken = $googleUser->refreshToken;

            if (empty($refreshToken)) {
                return "<h1>Tidak ada Refresh Token yang diterima dari Google.</h1>"
                    . "<p>Pastikan Anda menyetujui permintaan akses saat di layar persetujuan Google.</p>"
                    . "<p>Coba lagi: <a href='" . url('/google/auth') . "'>Otentikasi Ulang dengan Google</a></p>"
                    . "<p><strong>Penting:</strong> Jika Anda sudah pernah memberikan izin untuk aplikasi ini sebelumnya, Anda mungkin perlu mencabut aksesnya dari akun Google Anda di <a href='https://myaccount.google.com/permissions' target='_blank'>https://myaccount.google.com/permissions</a>, lalu coba otentikasi ulang.</p>";
            }

            return "<h1>Otorisasi Berhasil!</h1>"
                . "<p>Silakan salin Refresh Token di bawah ini dan tempelkan ke dalam file <code>.env</code> Anda sebagai <code>GOOGLE_DRIVE_REFRESH_TOKEN</code>.</p>"
                . "<p><strong>JANGAN BAGIKAN TOKEN INI KEPADA SIAPAPUN.</strong> Ini adalah kredensial sensitif.</p>"
                . "<pre style='background-color: #f1f1f1; padding: 15px; border: 1px solid #ccc; word-wrap: break-word; user-select: all;'>"
                . $refreshToken
                . "</pre>";

        } catch (\Exception $e) {
            return 'Terjadi kesalahan saat otentikasi: ' . $e->getMessage();
        }
    }
}