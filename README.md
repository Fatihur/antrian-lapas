# E-Antrian Kunjungan Lapas Sumbawa

Aplikasi antrian kunjungan online untuk Lembaga Pemasyarakatan (Lapas) Kelas IIA Sumbawa. Dibangun dengan Laravel 11, Livewire 3, dan MySQL.

## Fitur Utama

### Untuk Pengunjung
- **Ambil Antrian Online**: Pendaftaran antrian dengan form multi-step
- **Multi Pengikut**: Daftarkan hingga 10 pengikut dalam satu antrian
- **Upload Identitas**: Upload foto KTP/identitas untuk verifikasi
- **Cek Status**: Pantau status antrian real-time dengan NIK
- **PDF Ticket**: Generate dan download bukti antrian PDF

### Untuk Admin
- **Dashboard**: Ringkasan statistik antrian harian
- **Manajemen Antrian**: Verifikasi, approve/reject, lihat detail
- **Panggil Antrian**: Sistem panggilan dengan layout 2 kolom
- **Jadwal & Kuota**: Atur jadwal kunjungan dan kuota per sesi
- **Laporan**: Export PDF dan Excel untuk rekapan
- **Manajemen Admin**: CRUD admin dengan role (Super Admin & Operator)

## Teknologi

- **Framework**: Laravel 11
- **Frontend**: Livewire 3 + Tailwind CSS
- **Database**: MySQL 8
- **PDF**: DomPDF
- **Excel**: Maatwebsite Excel

## Instalasi

### Persyaratan
- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js >= 18

### Langkah Instalasi

1. **Install dependencies**
```bash
composer install
npm install
```

2. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Konfigurasi database** (Edit .env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antrian_lapas
DB_USERNAME=root
DB_PASSWORD=
```

4. **Run migrations dan seeders**
```bash
php artisan migrate
php artisan db:seed
```

5. **Create storage symlink**
```bash
php artisan storage:link
```

6. **Compile assets**
```bash
npm run build
```

7. **Start server**
```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Login Default

| Role | Username | Password |
|------|----------|----------|
| Super Admin | admin | password123 |
| Operator | operator1 | password123 |

## Struktur Database

### Tabel Utama
1. **admins**: Data admin/operator
2. **visit_schedules**: Jadwal dan kuota kunjungan
3. **visit_queues**: Data antrian kunjungan
4. **visit_followers**: Data pengikut per antrian
5. **queue_status_logs**: Log perubahan status (audit)
6. **queue_calls**: Log pemanggilan antrian

## Routes

### Public
- `GET /` - Home page
- `GET /ambil-antrian` - Form pendaftaran antrian
- `GET /cek-status` - Cek status antrian

### Admin (Prefix: /admin)
- `GET /login` - Login page
- `GET /dashboard` - Dashboard admin
- `GET /antrian` - Manajemen antrian
- `GET /panggil` - Panggil antrian
- `GET /jadwal` - Jadwal & kuota
- `GET /laporan` - Laporan
- `GET /pengguna` - Manajemen admin (Super Admin only)

## Status Antrian

- `Menunggu Verifikasi` - Baru daftar
- `Disetujui` - Lolos verifikasi
- `Ditolak` - Ditolak dengan alasan
- `Menunggu Dipanggil` - Siap dipanggil
- `Dipanggil` - Sedang dipanggil
- `Selesai` - Kunjungan selesai
- `Kedaluwarsa` - Tidak hadir melewati jadwal

## Fitur Keamanan

- Custom authentication (tanpa Breeze/Jetstream)
- Rate limiting pada login (5 attempts)
- Session-based auth dengan guard custom
- Middleware protection untuk admin routes
- CSRF protection
- Password hashing dengan Bcrypt

## Development

### Run Development Server
```bash
php artisan serve
```

### Testing
```bash
php artisan test
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Troubleshooting

Jika terjadi error, coba:
```bash
composer dump-autoload
php artisan optimize:clear
```

## Lisensi

MIT License

## Kontak

Lembaga Pemasyarakatan Kelas IIA Sumbawa  
Jl. Lintas Sumbawa No. 123, Sumbawa Besar
