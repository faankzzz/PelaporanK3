# Lapor Insiden K3 (Laravel)

Sistem pelaporan insiden kerja untuk departemen K3 dengan 3 peran: **Pelapor**, **Petugas K3**, **Admin**.
Paket ini berisi file aplikasi (migrasi, model, controller, route, view, seeder) yang ditimpa ke proyek Laravel baru.

## Kebutuhan
PHP 8.2+, Composer, MySQL/MariaDB, Laravel 11 atau 12.

## Cara pasang
1. Buat proyek Laravel baru:
   composer create-project laravel/laravel lapor-k3
2. Salin isi paket ini (folder app, database, resources, routes) ke dalam folder lapor-k3,
   pilih "timpa/replace" jika diminta (routes/web.php dan migrasi users bawaan memang diganti).
3. Atur database di .env, misalnya:
   DB_CONNECTION=mysql
   DB_DATABASE=lapor_k3
   DB_USERNAME=root
   DB_PASSWORD=
   APP_LOCALE=id
   SESSION_DRIVER=database
4. Jalankan:
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
5. Buka http://127.0.0.1:8000

## Akun demo (ganti password sebelum dipakai sungguhan)
| Peran      | Email           | Password    |
|------------|-----------------|-------------|
| Admin      | admin@k3.test   | password123 |
| Petugas K3 | k3@k3.test      | password123 |
| Pelapor    | pelapor@k3.test | password123 |

## Fitur per peran
- Pelapor: buat laporan (jenis, waktu, lokasi, kronologi, korban, saksi, foto), lihat riwayat dan status, ubah laporan selama belum diverifikasi.
- Petugas K3: verifikasi dan tentukan keparahan, isi investigasi (penyebab langsung/dasar), tambah tindakan korektif/preventif (penanggung jawab, tenggat), ubah status sampai ditutup, dashboard, cetak laporan, ekspor CSV (bisa dibuka di Excel).
- Admin: kelola akun, master data (unit, jenis insiden), lihat semua laporan, ekspor, audit log.

## Alur status
Dilaporkan -> Diverifikasi -> Investigasi -> Tindak Lanjut -> Selesai.
Laporan hanya bisa ditutup jika ada minimal satu tindakan dan semuanya berstatus selesai.

## Catatan
- Tampilan memakai Bootstrap 5 dari CDN, jadi perlu internet saat dibuka.
- Cetak PDF: buka halaman Cetak lalu pilih "Save as PDF" di browser.
- Notifikasi email/WhatsApp belum disertakan (bisa ditambah lewat Laravel Notification/Mail).
