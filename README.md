# WebGIS TPHP

Proyek Sistem Informasi Geografis (GIS) menggunakan Laravel, Vue.js (Inertia), dan PostgreSQL (PostGIS).

## Cara Setup di Laptop Baru

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda:

### 1. Persyaratan Sistem

Pastikan laptop Anda sudah terinstal:

- PHP >= 8.2
- Composer
- Node.js & NPM
- PostgreSQL dengan ekstensi PostGIS

### 2. Clone Repositori

```bash
git clone https://github.com/pardiyono21/webgis-tphp.git
cd webgis-tphp
```

### 3. Instalasi Otomatis

Proyek ini sudah dilengkapi dengan script setup otomatis. Jalankan perintah berikut:

```bash
composer run setup
```

_Script ini akan melakukan `composer install`, menyalin file `.env`, generate key, migrasi database, dan build asset._

### 4. Konfigurasi Database

Buka file `.env` yang baru saja terbuat dan sesuaikan bagian database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database_anda
DB_USERNAME=username_postgres_anda
DB_PASSWORD=password_postgres_anda
```

### 5. Jalankan Aplikasi

Buka dua terminal terpisah:

**Terminal 1 (Backend):**

```bash
php artisan serve
```

**Terminal 2 (Frontend/Vite):**

```bash
npm run dev
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`.

---

## Catatan

- Pastikan ekstensi **PostGIS** sudah aktif di database PostgreSQL Anda sebelum menjalankan migrasi.
- Jika menggunakan Laravel Wayfinder, route JS akan ter-generate otomatis saat aplikasi dijalankan.
