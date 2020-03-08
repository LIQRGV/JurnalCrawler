# Jurnal Crawler

## Syarat situs arsip jurnal yang bisa di-crawl:
- Bisa diakses
- Halaman arsip berakhiran "issue/archive"

## Syarat Instalasi
- Redis 
- PHP 5.6 - 7.2 (7.3 ke atas gagal)
- Composer

## Instalasi
- Saat berada di root project ini, lakukan `composer install` (dengan asumsi composer sudah terinstall sebelumnya)
- Salin .env.example menjadi .env dan sesuaikan isinya dengan pengaturan di komputer / mesin lokal
- Lakukan migrasi database dengan `php artisan migrate`. Jika gagal, periksa apakah versi PHP sudah sesuai dengan syarat instalasi

## Cara Penggunaan
- Saat berada di root project, jalankan perintah `php artisan crawl --url=URL_YANG_INGIN_DI_CRAWL`, misalnya `php artisan crawl --url=https://ejournal.unitomo.ac.id/index.php/jsk/issue/archive`
- Setelah perintah di atas dilakukan, maka job akan tersimpan di Redis. Jika Redis belum ada, maka job akan gagal.
- Untuk menjalankan job, lakukan perintah `php artisan horizon`, tunggu hingga semua job selesai.

### Tips
Jika memiliki banyak web yang ingin di-crawl, simpan list situs dalam 1 file CSV pada kolom A. Setelah itu, jalankan perintah ini `awk -F"," 'FNR > 1 {print $1}' nama_file.csv | xargs -I {} php artisan crawl --url={}`