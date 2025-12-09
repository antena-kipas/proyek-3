<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Refresh Token</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; margin: 0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; max-width: 80%; }
        h1 { color: #333; }
        p { color: #555; }
        textarea { width: 100%; padding: 0.5rem; margin-top: 1rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; font-size: 1.1rem; }
        button { background-color: #4285F4; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #357ae8; }
        .warning { color: #d93025; font-weight: bold; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Refresh Token Google Drive Anda</h1>
        <p>Proses otentikasi berhasil. Salin token di bawah ini dan simpan ke dalam file <code>.env</code> Anda pada variabel <code>GOOGLE_DRIVE_REFRESH_TOKEN</code>.</p>
        <textarea id="refreshToken" rows="4" readonly>{{ $token }}</textarea>
        <button onclick="copyToken()">Salin Token</button>
        <p class="warning">PENTING: Setelah menyalin, segera tutup halaman ini dan jangan bagikan token ini kepada siapa pun.</p>
    </div>

    <script>
        function copyToken() {
            const textarea = document.getElementById('refreshToken');
            textarea.select();
            document.execCommand('copy');
            alert('Token berhasil disalin ke clipboard!');
        }
    </script>
</body>
</html>
