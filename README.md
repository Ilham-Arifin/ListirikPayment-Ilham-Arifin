# Paytrik
Sistem Pembayaran Listrik Pasca Bayar

Dibuat oleh: **Ilham Arifin**

## Deskripsi
Paytrik adalah sebuah sistem pembayaran listrik pascabayar yang memungkinkan pengguna untuk melakukan pembayaran tagihan listrik secara online dengan mudah dan aman. Aplikasi ini dibangun dengan teknologi modern untuk memberikan pengalaman pengguna yang baik dan proses transaksi yang efisien.

### Fitur Utama
- Pendaftaran dan autentikasi pengguna
- Pengecekan tagihan listrik
- Pembayaran online yang aman
- Riwayat transaksi
- Manajemen profil pengguna
- Notifikasi pembayaran
- Laporan dan statistik (untuk admin)

## Persyaratan Sistem
- XAMPP (PHP 7.4+ dan MySQL 5.7+)
- Web Browser (Chrome, Firefox, dll.)
- Text Editor (Visual Studio Code, Sublime Text, dll.)

## Cara Instalasi

### 1. Persiapkan XAMPP
- Pastikan XAMPP sudah terinstall di komputer Anda
- Jalankan Apache dan MySQL melalui XAMPP Control Panel

### 2. Letakkan File Proyek
- Copy folder `paytrik` ke dalam direktori `htdocs` XAMPP
  - Biasanya terletak di: `C:\xampp\htdocs\paytrik`

### 3. Buat Database
1. Buka browser dan akses `http://localhost/phpmyadmin`
2. Buat database baru dengan nama `paytrik`
3. Import file SQL yang tersedia di folder `database` 

### 4. Konfigurasi Koneksi Database
- Buka file `config.php` (atau file konfigurasi yang sesuai)
- Sesuaikan konfigurasi database:
  ```php
  $host = 'localhost';
  $dbname = 'paytrik';
  $username = 'root';  // default XAMPP
  $password = '';      // default XAMPP (kosong)
  ```

### 5. Akses Aplikasi
- Buka browser dan akses: `http://localhost/paytrik`

## Kontribusi
Jika Anda ingin berkontribusi pada proyek ini, silakan buat pull request.

## Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).