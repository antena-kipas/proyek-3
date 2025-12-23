> hey gemini pada proyek ini akan memasuki tahap production, menurut anda hal apa saja yang seharusnya ada dan hal apa saja
  yang seharusnya tidak ada dalam taha production ini ?

✦ Tentu, ini adalah pertanyaan yang sangat penting dan bagus. Mempersiapkan aplikasi untuk tahap produksi adalah langkah
  krusial.

  Berdasarkan praktik umum dan standar untuk aplikasi Laravel seperti proyek Anda, berikut adalah hal-hal yang seharusnya ada
  dan yang seharusnya tidak ada di lingkungan produksi.

  ---

  Hal yang Seharusnya ADA di Lingkungan Produksi

   1. Konfigurasi `.env` yang Tepat:
       * APP_ENV=production: Ini akan menonaktifkan banyak fitur debugging dan mengoptimalkan beberapa layanan.
       * APP_DEBUG=false: SANGAT PENTING. Jika true, error akan menampilkan informasi sensitif tentang server dan kode Anda.
       * APP_KEY: Pastikan ini di-generate secara unik untuk lingkungan produksi (php artisan key:generate).
       * Kredensial Produksi: Pastikan semua kredensial (database, mail, API keys seperti Google, Gemini) adalah untuk
         lingkungan produksi, bukan development.

   2. Optimasi Performa:
       * Cache Konfigurasi, Route, dan View: Jalankan perintah ini saat deployment untuk mempercepat aplikasi secara
         signifikan.

   1         php artisan config:cache
   2         php artisan route:cache
   3         php artisan view:cache
       * Optimasi Autoloader Composer: Saat installasi dependensi, gunakan flag --no-dev dan --optimize-autoloader.

   1         composer install --no-dev --optimize-autoloader
       * Kompilasi Aset Frontend: Jalankan ini untuk menggabungkan dan meminifikasi file CSS dan JavaScript Anda.
   1         npm run build

   3. Keamanan:
       * HTTPS/SSL: Wajibkan semua traffic melalui HTTPS untuk mengenkripsi data.
       * Izin File (Permissions): Pastikan hanya direktori yang diperlukan (seperti storage dan bootstrap/cache) yang dapat
         ditulis oleh server. Direktori lain harusnya read-only.
       * Backup Otomatis: Siapkan jadwal rutin untuk backup database dan file-file penting (seperti yang sudah kita siapkan
         untuk Google Drive).

   4. Proses Latar Belakang yang Andal:
       * Queue Worker: Gunakan process manager seperti supervisor untuk menjalankan php artisan queue:work. Ini memastikan
         job-job di latar belakang (seperti backup ke GDrive) berjalan terus-menerus dan akan di-restart jika gagal.
       * Task Scheduler: Konfigurasikan satu cron job di server Anda untuk menjalankan scheduler Laravel setiap menit.

   1         * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

  ---

  Hal yang Seharusnya TIDAK ADA di Lingkungan Produksi

   1. Paket untuk Development (`require-dev`):
       * Paket seperti laravel/telescope, barryvdh/laravel-debugbar, atau faker tidak boleh terinstal di produksi. Perintah
         composer install --no-dev akan mencegah ini.

   2. Fungsi Debugging dalam Kode:
       * Pastikan tidak ada fungsi seperti dd(), dump(), atau Log::debug() yang tertinggal di dalam kode. Ini bisa mengganggu
         eksekusi atau membocorkan informasi.

   3. File `.env` di Version Control (Git):
       * File .env berisi kredensial sensitif dan tidak boleh ada di repository Git. File ini harus dibuat manual di server
         produksi. File .env.example adalah panduannya.

   4. Data Dummy atau Seeder yang Tidak Perlu:
       * Jangan menjalankan seeder yang mengisi database dengan data palsu (kecuali untuk data awal yang memang diperlukan,
         seperti role atau user admin pertama). Hati-hati dengan perintah seperti php artisan migrate:fresh --seed.

   5. Menampilkan Pesan Error Detail ke Pengguna:
       * APP_DEBUG=false akan menangani ini. Buat halaman error kustom (untuk error 404, 500, dll.) agar pengalaman pengguna
         lebih baik dan tidak mengekspos detail teknis.

  Singkatnya, lingkungan produksi harus  ramping, teroptimasi, dan seaman mungkin.

  Apakah Anda ingin saya membantu memeriksa salah satu dari poin di atas, misalnya, melihat file composer.json Anda untuk
  memastikan dependensi development sudah terpisah dengan benar?
