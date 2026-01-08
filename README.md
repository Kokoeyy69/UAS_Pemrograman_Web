# UAS_Pemrograman_Web

# 🌐 Modular System App (UAS Pemrograman Web)

> **Sistem Manajemen Konten (CMS) Berbasis PHP Native OOP & Modular Routing**

Project ini dikembangkan sebagai pemenuhan tugas **Ujian Akhir Semester (UAS)** mata kuliah Pemrograman Web. Aplikasi ini dibangun tanpa menggunakan Framework PHP (seperti Laravel/CI), melainkan menggunakan konsep **OOP (Object Oriented Programming)** murni dengan arsitektur **Modular** dan pola desain **MVC (Model-View-Controller)** sederhana.

---

## 🌟 Fitur Unggulan

Aplikasi ini tidak hanya sekadar CRUD biasa, tetapi dilengkapi dengan fitur keamanan dan fungsionalitas modern:

### 1. Fitur Utama (Core)
* **Modular Routing System:** URL yang bersih dan SEO Friendly menggunakan `.htaccess` (Contoh: `/user/profile` bukan `user_profile.php`).
* **Multi-Role Authentication:** Sistem login bertingkat untuk **Administrator** dan **User Biasa**.
* **Responsive Design:** Tampilan antarmuka yang adaptif untuk Desktop dan Mobile (Mobile-First) menggunakan **Bootstrap 5.3**.
* **Database Security:** Menggunakan **PDO (PHP Data Objects)** dengan *Prepared Statements* untuk mencegah serangan *SQL Injection*.

### 2. Fitur Modul Artikel
* **CRUD Lengkap:** Tambah, Baca, Ubah, dan Hapus artikel.
* **Rich Text Editor:** Integrasi **CKEditor 5** untuk penulisan konten yang rapi.
* **Pencarian & Pagination:** Fitur pencarian artikel real-time dan pembagian halaman otomatis.
* **Cetak Laporan:** Fitur khusus Admin untuk mencetak rekapitulasi artikel siap print (CSS `@media print`).

### 3. Fitur Keamanan & User (Extra)
* **Registrasi dengan CAPTCHA:** Mencegah bot spam saat pendaftaran dengan logika matematika sederhana.
* **Simulasi OTP:** Verifikasi akun menggunakan kode OTP (One Time Password) sebelum login.
* **Secure Password:** Enkripsi password menggunakan algoritma `BCRYPT` (Standar industri).
* **Manajemen Profil:** User dapat mengubah biodata, upload foto profil, dan ganti password secara mandiri.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend** | PHP 8.0+ | Konsep OOP & Modular |
| **Database** | MySQL / MariaDB | Driver PDO & MySQLi |
| **Frontend** | Bootstrap v5.3 | Framework CSS Responsif |
| **Icons** | FontAwesome v6.4 | Ikon Vektor |
| **Editor** | CKEditor 5 | WYSIWYG Editor |
| **Server** | Apache | Modul `mod_rewrite` aktif |

---

## 📂 Struktur Direktori & Penjelasan Kode

# 🚀 Modular System App (UAS Pemrograman Web)

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Database](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

> **Sistem Manajemen Konten (CMS) Berbasis PHP Native OOP, Modular Routing & Keamanan Berlapis.**

Aplikasi ini dikembangkan untuk memenuhi tugas **Ujian Akhir Semester (UAS)** mata kuliah Pemrograman Web. Dibangun dari nol (*scratch*) tanpa framework PHP, aplikasi ini mengimplementasikan konsep **Object-Oriented Programming (OOP)**, pola arsitektur **Modular**, dan **Routing System** kustom menggunakan `.htaccess`.

---

## 📋 Daftar Isi
1. [Fitur Unggulan](#-Fitur-unggulan)
2. [Arsitektur Sistem](#-arsitektur-sistem)
3. [Teknologi & Library](#-teknologi--library)
4. [Skema Database](#-skema-database)
5. [Instalasi & Konfigurasi](#-instalasi--konfigurasi)
6. [Panduan Penggunaan](#-panduan-penggunaan)
7. [Struktur Folder](#-struktur-folder)
8. [Troubleshooting](#-troubleshooting)

---

## 🌟 Fitur Unggulan

Aplikasi ini dirancang memenuhi standar aplikasi modern dengan spesifikasi berikut:

### 🔹 Core & Keamanan
* **Modular Routing:** URL bersih dan SEO-friendly (contoh: `/artikel/baca/judul-berita`) menggunakan teknik *URL Rewriting* di `.htaccess`.
* **Hybrid Database Wrapper:** Class `Database.php` mendukung dua driver sekaligus (**PDO** & **MySQLi**) untuk fleksibilitas dan keamanan maksimal.
* **Security Headers:** Proteksi terhadap serangan XSS dan Clickjacking melalui konfigurasi header HTTP.
* **Secure Auth:** Password di-hash menggunakan **BCRYPT** dan sesi diamankan dengan validasi ketat.

### 🔹 Modul User (Pengguna)
* **Multi-Role System:** Hak akses berbeda untuk **Administrator** (Full) dan **User Biasa** (Terbatas).
* **Registrasi Anti-Bot:** Dilengkapi **CAPTCHA Matematika** sederhana untuk mencegah pendaftaran spam.
* **Simulasi OTP:** Fitur verifikasi akun menggunakan kode OTP (*One Time Password*) sebelum akun aktif.
* **Manajemen Profil:** User dapat mengganti foto profil, update biodata, dan reset password mandiri.

### 🔹 Modul Artikel (Berita)
* **Rich Content CRUD:** Tulis artikel dengan teks berformat (Bold, Italic, List) menggunakan **CKEditor 5**.
* **Smart Search & Pagination:** Pencarian artikel real-time dan pembagian halaman otomatis untuk performa optimal.
* **Cetak Laporan PDF:** Halaman khusus Admin yang didesain otomatis bersih dari elemen navigasi saat dicetak (`window.print`).

### 🔹 Antarmuka (UI/UX)
* **Responsive Mobile-First:** Tampilan adaptif di Smartphone, Tablet, dan Desktop menggunakan **Bootstrap 5.3**.
* **Dynamic Greeting:** Dashboard menyapa user sesuai waktu (Pagi/Siang/Sore/Malam).

---

## 🏗 Arsitektur Sistem

Aplikasi ini tidak menggunakan framework, melainkan membangun kerangka kerja sendiri:

1.  **Front Controller (`index.php`):** Semua request masuk melalui satu pintu. Sistem memparsing URL untuk menentukan modul mana yang dipanggil.
2.  **Modular Pattern:** Fitur dikelompokkan dalam folder `module/` (contoh: `module/user/`, `module/artikel/`) agar kode terorganisir.
3.  **Class Library:** Logika berulang (Koneksi DB, Pembuatan Form) dipisah ke dalam folder `class/` untuk digunakan kembali (Reusability).

---

## 🛠 Teknologi & Library

| Kategori | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Language** | PHP 8.0+ | Full OOP Support |
| **Database** | MySQL / MariaDB | Relational Database Management |
| **Frontend Framework** | Bootstrap 5.3 | CSS & JS Components |
| **Icons** | FontAwesome 6.4 | Scalable Vector Icons |
| **Text Editor** | CKEditor 5 | WYSIWYG Content Editor |
| **Server** | Apache Web Server | Wajib support `mod_rewrite` |

---

## 🗄 Skema Database

Database `latihan_oop` terdiri dari 2 tabel utama:

**1. Tabel `user`**
Menyimpan data otentikasi dan profil pengguna.
* `id` (PK), `username`, `password` (Hash), `nama`, `email`, `role` (enum: admin/user), `is_active`, `otp`.

**2. Tabel `artikel`**
Menyimpan konten berita.
* `id` (PK), `judul`, `slug` (Unique), `isi` (Text), `gambar`, `tanggal`.

---

## 💻 Instalasi & Konfigurasi

Ikuti langkah ini untuk menjalankan di Localhost (XAMPP/Laragon):

### Langkah 1: Persiapan Database
1.  Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Buat database baru dengan nama: `latihan_oop`.
3.  Import file `uas_web/latihan_oop.sql` ke database tersebut.

### Langkah 2: Setup Folder Project
1.  Salin folder project `uas_web` ke dalam direktori server:
    * **XAMPP:** `C:\xampp\htdocs\`
    * **Laragon:** `C:\laragon\www\`
2.  **PENTING:** Pastikan nama folder adalah **`uas_web`**.

### Langkah 3: Konfigurasi Server (.htaccess)
Pastikan file `.htaccess` memiliki konfigurasi `RewriteBase` yang benar sesuai nama folder:
```apache
RewriteEngine On
RewriteBase /uas_web/  <-- Wajib sama dengan nama folder
```
### Langkah 4: Koneksi Database
Edit file `uas_web/config.php` jika password database Anda berbeda:

```php
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',      // Default XAMPP kosong
    'db_name' => 'latihan_oop'
];
```

## 📖 Panduan Penggunaan

Akses aplikasi melalui browser: `http://localhost/uas_web`

## 🔑 Akun Demo

Gunakan akun ini untuk pengujian sistem:

| Role | Username | Password | Fitur Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | CRUD User & Artikel, Cetak Laporan |
| **User Biasa** | `user` | `user123` | Profil Diri, Baca Artikel, Ubah Password |

```(Jika login gagal, silakan daftar akun baru melalui menu Register untuk menguji fitur OTP).```

## 📂 Struktur Folder

```path
uas_web/
├── .htaccess             # Router & Konfigurasi Server
├── config.php            # Setting Database
├── index.php             # Front Controller (Main Entry)
├── latihan_oop.sql       # File Database
├── assets/               # File Statis
│   └── img/              # Upload Gambar User & Artikel
├── class/                # Core Classes
│   ├── Database.php      # Koneksi DB (PDO/MySQLi)
│   └── Form.php          # Form Generator
├── module/               # Logika Bisnis (Modules)
│   ├── artikel/          # Controller Artikel (CRUD, List, Baca)
│   ├── home/             # Controller Dashboard
│   └── user/             # Controller User (Auth, Profil)
└── template/             # Layout Views
    ├── header.php        # Navbar & Head HTML
    ├── sidebar.php       # Sidebar Menu
    └── footer.php        # Footer & Scripts
```
## ❓ Troubleshooting
Jika mengalami kendala, cek solusi berikut:

1. Error 404 / Object Not Found saat klik menu:
   * Pastikan modul mod_rewrite di Apache sudah aktif.
   * Cek file `.htaccess`, pastikan baris `RewriteBase /uas_web/` sesuai dengan nama folder di `htdocs`.
2. Gagal Upload Gambar:
   * Pastikan folder `assets/img/artikel` dan `assets/img/user` ada.
   * Jika di Linux/Mac, berikan permission write: `chmod -R 777 assets/img/`.
3. Database Connection Error:
   * Cek kembali kredensial di `config.php`.
   * Pastikan MySQL server sedang berjalan.

##  Contoh Output

