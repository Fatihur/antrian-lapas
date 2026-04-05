<img src="https://r2cdn.perplexity.ai/pplx-full-logo-primary-dark%402x.png" style="height:64px;margin-right:32px"/>

# sekarang saya ingin anda buatkan PRD lengkap nya, lengkap dengan fitur, library yang kita pakai, sampai dengan layout dan desain nya, buat desain modern dan clean. saya ingin menggunakan laravel + livewire, dan mysql, untuk auth nya custom, jangan pakai breeze/jetstream

Berikut PRD lengkap yang sudah disesuaikan untuk **Laravel + Livewire + MySQL**, auth custom tanpa Breeze/Jetstream, alur tanpa QR, PDF bukti antrean, pengikut multi-orang, dan fitur panggil antrean admin. Livewire memang cocok untuk form interaktif dan upload file, termasuk validasi upload seperti form Laravel biasa, sehingga stack ini pas untuk aplikasi antrean yang banyak interaksi form dan status real-time ringan.[^1][^2]

## Gambaran produk

Sistem ini adalah aplikasi web **E-Antrian Kunjungan Lapas Sumbawa** untuk pendaftaran antrean kunjungan, pengelolaan antrean oleh admin, pemanggilan antrean, serta cetak dan unduh ulang bukti antrean PDF. Proses dasarnya mengikuti pola layanan kunjungan WBP: pengunjung mendaftar dengan identitas, mendapat nomor antrean, lalu admin memverifikasi dan memanggil antrean sesuai sesi kunjungan.[^3][^4][^5]

## Tujuan

Tujuan produk:

- Memindahkan proses ambil antrean dari manual ke online.[^5]
- Mengurangi penumpukan di loket pendaftaran kunjungan.[^5]
- Memudahkan admin memverifikasi data pengunjung dan pengikut.[^6][^3]
- Menyediakan bukti antrean PDF yang bisa diunduh ulang melalui halaman cek status.[^3]
- Mendukung pemanggilan antrean oleh admin lengkap dengan detail pengunjung yang dipanggil.[^4]


## Aktor

| Aktor | Hak akses |
| :-- | :-- |
| Pengunjung | Ambil antrean, cek status antrean, download PDF. |
| Admin / Operator | Login, dashboard, verifikasi antrean, panggil antrean, kelola jadwal \& kuota, laporan, manajemen admin. |

## Scope fitur

### 1. Fitur pengunjung

#### A. Ambil antrian

Pengunjung mengisi data utama, memilih tanggal dan sesi, menambahkan pengikut bila ada, lalu submit pendaftaran. Sistem memvalidasi kuota sesi, menyimpan data, membuat nomor antrean, lalu menghasilkan PDF bukti antrean untuk langsung diunduh.[^6][^3][^5]

#### B. Cek status antrian

Pengunjung memasukkan NIK / nomor identitas pendaftar utama untuk melihat data antrean aktif. Sistem menampilkan detail antrean, status, dan tombol download ulang PDF.[^3]

### 2. Fitur admin

#### A. Login custom

Sistem memakai auth custom berbasis session Laravel dan tabel admin internal, tanpa Breeze atau Jetstream. Laravel sendiri mendukung autentikasi manual melalui guard, middleware, hashing password, dan session management sehingga implementasi custom tetap standar dan aman bila memakai komponen inti framework.[^2][^1]

#### B. Dashboard

Dashboard menampilkan ringkasan antrean hari ini, jumlah antrean per status, antrean per sesi, dan antrean yang sedang dipanggil.[^4]

#### C. Manajemen antrean

Admin dapat melihat detail antrean, memverifikasi data, melihat pengikut, menyetujui atau menolak pendaftaran, dan mengubah status antrean.[^6][^3]

#### D. Panggil antrean

Admin dapat menekan tombol **Panggil** pada antrean yang sudah siap. Saat dipanggil, sistem menampilkan nomor antrean dan seluruh data pengunjung utama serta pengikut pada panel detail panggilan.[^4]

#### E. Jadwal dan kuota

Admin mengatur kuota per tanggal dan sesi kunjungan agar pendaftaran otomatis menutup saat kapasitas penuh.[^7][^5]

#### F. Laporan

Admin dapat melihat rekap harian dan bulanan serta ekspor ke PDF atau Excel.[^4]

#### G. Manajemen admin

Super admin dapat menambah, ubah, nonaktifkan akun admin/operator.

## User flow

### Flow pengunjung

1. Buka halaman Ambil Antrian.
2. Isi data pengunjung utama.
3. Tambah data pengikut satu atau lebih.
4. Pilih tanggal dan sesi.
5. Submit form.
6. Sistem generate nomor antrean dan PDF.
7. Pengunjung download PDF.
8. Jika diperlukan, buka Cek Status Antrian.
9. Masukkan NIK pendaftar utama.
10. Sistem tampilkan detail antrean dan tombol download ulang PDF.[^3][^6]

### Flow admin

1. Login ke panel admin.
2. Lihat dashboard.
3. Buka data antrean masuk.
4. Verifikasi data.
5. Setujui / tolak antrean.
6. Pada hari kunjungan, buka menu panggil antrean.
7. Klik Panggil pada nomor antrean.
8. Sistem menampilkan nomor antrean aktif dan detail pengunjung.
9. Setelah selesai, ubah status ke Selesai.[^4]

## Status bisnis

| Status | Keterangan |
| :-- | :-- |
| Menunggu Verifikasi | Baru daftar, belum diperiksa admin. |
| Disetujui | Lolos verifikasi admin. |
| Ditolak | Ditolak karena data tidak valid, kuota penuh, atau alasan lain. |
| Menunggu Dipanggil | Siap menunggu giliran kunjungan. |
| Dipanggil | Sedang dipanggil petugas.[^4] |
| Selesai | Proses kunjungan selesai. |
| Kedaluwarsa | Tidak hadir atau lewat tanggal/sesi kunjungan. |

## Field dan form

### 1. Form ambil antrian

#### Data pendaftar utama

| Field | Tipe | Wajib | Catatan |
| :-- | :-- | --: | :-- |
| nik_pendaftar | varchar(25) | Ya | Nomor identitas utama.[^3] |
| jenis_identitas | enum | Ya | KTP, SIM, Paspor, KK, lainnya.[^6] |
| nama_pengunjung | varchar(150) | Ya | Nama lengkap. |
| no_hp | varchar(20) | Ya | Nomor HP / WhatsApp. |
| hubungan_wbp | varchar(100) | Ya | Hubungan dengan WBP. |
| nama_wbp | varchar(150) | Ya | Nama WBP tujuan kunjungan. |
| tanggal_kunjungan | date | Ya | Tanggal kunjungan. |
| sesi_kunjungan | enum | Ya | Pagi / Siang. |
| foto_identitas | string/file path | Ya | File identitas utama.[^1][^2] |
| catatan | text | Tidak | Opsional. |

#### Data pengikut

Satu antrean dapat memiliki banyak pengikut.[^6]


| Field | Tipe | Wajib | Catatan |
| :-- | :-- | --: | :-- |
| nama_pengikut | varchar(150) | Ya | Nama lengkap pengikut. |
| nomor_identitas_pengikut | varchar(25) | Ya | Nomor identitas, tanpa foto. |
| jenis_kelamin_pengikut | enum | Ya | Laki-laki / Perempuan. |

#### Data sistem hasil generate

| Field | Tipe | Keterangan |
| :-- | :-- | :-- |
| kode_booking | varchar(50) | Kode unik internal. |
| nomor_antrian | varchar(30) | Nomor otomatis per tanggal + sesi. |
| status_antrian | enum | Default Menunggu Verifikasi. |
| pdf_path | string | Lokasi file PDF. |
| waktu_daftar | datetime | Timestamp pendaftaran. |

### 2. Form cek status antrian

| Field | Tipe | Wajib | Catatan |
| :-- | :-- | --: | :-- |
| nik_pendaftar | varchar(25) | Ya | Digunakan untuk pencarian antrean. |

Output:

- Nomor antrean.
- Tanggal kunjungan.
- Sesi.
- Status.
- Data pendaftar.
- Daftar pengikut.
- Tombol download PDF.


### 3. Form admin login

| Field | Tipe | Wajib |
| :-- | :-- | --: |
| username | varchar(100) | Ya |
| password | password | Ya |

### 4. Data admin

| Field | Tipe | Wajib |
| :-- | :-- | --: |
| nama | varchar(150) | Ya |
| username | varchar(100) | Ya |
| email | varchar(150) | Ya |
| password | varchar(255) | Ya |
| role | enum(super_admin, operator) | Ya |
| is_active | boolean | Ya |
| last_login_at | datetime | Tidak |

### 5. Jadwal dan kuota

| Field | Tipe | Wajib |
| :-- | :-- | --: |
| tanggal | date | Ya |
| sesi | enum | Ya |
| kuota_maksimal | int | Ya |
| status_jadwal | enum(buka,tutup) | Ya |
| jam_mulai | time | Tidak |
| jam_selesai | time | Tidak |
| keterangan | text | Tidak |

## PDF bukti antrean

PDF harus memuat seluruh data kunjungan agar dapat dipakai ulang saat datang ke lapas. Isi PDF:

- Identitas sistem / logo Lapas Sumbawa.
- Judul bukti antrean kunjungan.
- Kode booking.
- Nomor antrean.
- Tanggal daftar.
- Tanggal kunjungan.
- Sesi kunjungan.
- Status antrean.
- Nama pengunjung utama.
- Jenis identitas.
- Nomor identitas.
- Nomor HP.
- Hubungan dengan WBP.
- Nama WBP.
- Tabel pengikut: nama, nomor identitas, jenis kelamin.
- Total pengikut.
- Total orang dalam kunjungan.
- Catatan layanan / instruksi kedatangan.


## Modul panggil antrean

Fitur ini dibuat khusus untuk admin/operator. Komponen utamanya:[^4]

- Filter tanggal.
- Filter sesi.
- Daftar antrean siap panggil.
- Tombol Panggil.
- Panel antrean aktif.
- Panel detail pengunjung aktif.
- Tombol Selesai / Lewati / Panggil Berikutnya.

Saat admin menekan panggil:

- status menjadi `Dipanggil`;
- nomor antrean tampil besar di panel kanan;
- tampil detail pendaftar utama;
- tampil daftar pengikut lengkap;
- tampil sesi dan tanggal kunjungan.[^4]


## Database

Struktur utama tabel:

### 1. `admins`

Menyimpan akun login admin custom.

### 2. `visit_schedules`

Menyimpan tanggal, sesi, kuota, dan status buka/tutup.

### 3. `visit_queues`

Menyimpan data pendaftaran utama, nomor antrean, status, PDF, dan relasi ke jadwal.

### 4. `visit_followers`

Menyimpan data pengikut untuk setiap antrean.

### 5. `queue_status_logs`

Menyimpan histori perubahan status untuk audit.

### 6. `queue_calls`

Menyimpan histori pemanggilan antrean oleh admin.

Relasi:

- `visit_schedules` hasMany `visit_queues`
- `visit_queues` hasMany `visit_followers`
- `visit_queues` hasMany `queue_status_logs`
- `visit_queues` hasMany `queue_calls`
- `admins` hasMany `queue_status_logs`
- `admins` hasMany `queue_calls`


## Stack teknis

### Backend

- Laravel 12, sebagai framework utama aplikasi.[^8][^9]
- PHP 8.2 atau 8.3, agar kompatibel dengan Laravel modern.
- Livewire 3, untuk komponen form dinamis, upload file, validasi realtime, dan interaksi tanpa banyak JavaScript manual.[^2]
- MySQL 8, untuk database utama, indexing, transaksi, dan performa query yang stabil.[^10][^11]


### Frontend

- Blade + Livewire components.
- Tailwind CSS, untuk membangun UI yang cepat, bersih, dan konsisten.
- Alpine.js, jika dibutuhkan untuk interaksi ringan pada modal, dropdown, preview, dan toggles.
- Heroicons atau Lucide untuk ikon.


### File dan PDF

- Livewire Uploads untuk upload identitas utama.[^2]
- Laravel Storage untuk penyimpanan file identitas dan PDF.
- DomPDF atau Snappy PDF untuk generate bukti antrean PDF.
- Intervention Image opsional bila ingin kompres atau normalisasi gambar identitas sebelum simpan.


### Auth custom

Gunakan:

- tabel `admins`;
- controller / service login custom;
- `Hash::make()` untuk password;
- middleware admin;
- session guard custom atau guard berbasis session Laravel inti.

Pendekatan ini tetap aman tanpa Breeze/Jetstream selama memanfaatkan hashing, CSRF protection, middleware auth, rate limit login, dan session regeneration setelah login.[^1][^2]

## Library yang disarankan

| Kebutuhan | Library |
| :-- | :-- |
| UI interaktif server-driven | Livewire 3.[^2] |
| Upload file | Livewire uploads.[^2] |
| Styling | Tailwind CSS |
| Komponen ringan | Alpine.js |
| PDF | barryvdh/laravel-dompdf |
| Excel export | maatwebsite/excel |
| Date handling | Carbon |
| Activity / audit log | Spatie activitylog atau tabel log custom |
| Alert/toast | WireUI / custom Tailwind alert opsional |

## Arsitektur halaman

### Halaman publik

- Beranda / landing singkat.
- Ambil Antrian.
- Cek Status Antrian.
- Download PDF ulang.
- Informasi jadwal layanan.


### Halaman admin

- Login.
- Dashboard.
- Data Antrian.
- Detail Antrian.
- Panggil Antrian.
- Jadwal \& Kuota.
- Laporan.
- Manajemen Admin.


## Layout dan desain

Desain yang paling cocok adalah **modern clean admin-public hybrid**, dengan tampilan formal tetapi tetap ringan dipakai masyarakat umum. Pendekatan ini lebih aman untuk konteks lapas dibanding desain yang terlalu dekoratif, karena layanan publik lebih membutuhkan kejelasan, keterbacaan, dan struktur informasi yang tegas.[^5][^4]

### Gaya visual

- Dominan putih atau abu sangat muda sebagai background.
- Warna aksen hijau tua atau biru tua untuk kesan institusional dan tenang.
- Card dengan border halus, shadow ringan, dan radius medium.
- Tipografi sans-serif modern, rapi, dan mudah dibaca.
- Ruang putih cukup luas agar form tidak terasa padat.


### Palet warna

- Primary: hijau tua kebiruan.
- Secondary: slate / abu gelap.
- Success: hijau.
- Warning: amber.
- Danger: merah lembut.
- Background: off-white / gray-50.


### Tipografi

- Font utama: Inter, Public Sans, atau Plus Jakarta Sans.
- Ukuran:
    - Heading halaman: 24–32 px.
    - Subheading: 18–20 px.
    - Body: 14–16 px.
    - Label form: 13–14 px.


### Layout publik

- Header sederhana, logo kiri, menu kanan.
- Hero pendek berisi judul layanan dan 2 tombol utama: Ambil Antrian, Cek Status.
- Form dalam card tunggal lebar sedang.
- Stepper atau section divider: Data Pengunjung, Data Pengikut, Jadwal Kunjungan, Konfirmasi.
- Halaman sukses menampilkan nomor antrean besar dan tombol download PDF.


### Layout admin

- Sidebar kiri tetap.
- Topbar ringkas dengan nama admin dan tombol logout.
- Dashboard menggunakan kartu statistik dan tabel antrean hari ini.
- Halaman panggil antrean memakai layout 2 kolom:
    - kiri: daftar antrean menunggu;
    - kanan: nomor antrean aktif ukuran besar + detail pengunjung.
- Detail antrean menggunakan section card:
    - data pendaftar;
    - data kunjungan;
    - data pengikut;
    - histori status.


## Komponen UI utama

- Button primary, secondary, danger.
- Input text, file upload, select, date picker.
- Repeater pengikut dinamis.
- Status badge.
- Statistik cards.
- Tabel data dengan filter.
- Slide-over / modal untuk detail cepat.
- PDF download action.
- Queue call panel besar.


## Prinsip UX

- Form publik harus pendek per section dan tidak menakutkan.
- Validasi tampil inline, bukan setelah submit penuh.[^2]
- Upload file diberi preview dan batas format/ukuran jelas.[^2]
- Data pengikut dibuat tombol “Tambah Pengikut”.
- Di admin, aksi penting seperti Setujui, Tolak, Panggil harus kontras dan cepat dijangkau.
- Status warna harus konsisten di semua halaman.
- Tabel admin wajib punya pencarian dan filter tanggal/sesi/status.


## Validasi utama

- NIK / nomor identitas wajib.
- Nomor HP wajib format valid.
- Foto identitas wajib format jpg, jpeg, png, pdf; maksimal sesuai kebijakan.[^2]
- Tanggal kunjungan tidak boleh melewati jadwal tutup.
- Sesi tidak boleh dipilih jika kuota penuh.
- Minimal 0 pengikut, maksimal sesuai kebijakan lapas.
- Nomor antrean harus unik per tanggal dan sesi.
- Admin tidak bisa memanggil antrean yang belum disetujui.


## Non-fungsional

- Responsive desktop dan mobile.
- Waktu respon form cepat.
- Penyimpanan file aman di storage non-public bila perlu.
- Audit log untuk verifikasi dan pemanggilan.
- Session timeout untuk admin.
- Rate limiting login admin.
- Backup database rutin.


## Acceptance criteria inti

- Pengunjung dapat membuat antrean dan langsung mengunduh PDF.
- Pengunjung dapat cek status antrean memakai NIK.
- Admin dapat memverifikasi antrean.
- Admin dapat memanggil antrean dan melihat detail pengunjung yang dipanggil.
- PDF memuat seluruh data utama dan pengikut.
- Jadwal dan kuota memblokir pendaftaran saat penuh.
- Login admin berjalan dengan auth custom tanpa Breeze/Jetstream.


## Rekomendasi implementasi

Untuk stack ini, arsitektur terbaik adalah:

- Blade + Livewire full-stack untuk hampir semua halaman interaktif;
- auth admin custom berbasis controller + middleware;
- service class untuk nomor antrean, PDF generator, dan status workflow;
- repository atau action class untuk pendaftaran dan pemanggilan agar logika bisnis tidak menumpuk di Livewire component.

Kalau Anda mau, langkah berikut yang paling tepat adalah saya buatkan:

1. struktur menu dan sitemap,
2. database schema lengkap,
3. daftar package composer/npm,
4. task breakdown pengerjaan per modul.
<span style="display:none">[^12][^13][^14][^15][^16][^17][^18][^19][^20]</span>

<div align="center">⁂</div>

[^1]: https://laravel-livewire.com/docs/2.x/file-uploads

[^2]: https://livewire.laravel.com/docs/3.x/uploads

[^3]: https://sdp.ditjenpas.go.id/panduan/PendaftaranKunjungan.html

[^4]: https://sdp.ditjenpas.go.id/panduan/ManajemenPemanggilanAntrian.html

[^5]: https://sulteng.kemenkum.go.id/pemasyarakatan/layanan-keamanan-dan-ketertiban/layanan-kunjungan-wbp

[^6]: https://sdp.ditjenpas.go.id/panduan/MelaluiSubMenuPendaftaranKunjung.html

[^7]: https://sdp.ditjenpas.go.id/panduan/ManajemenRuangKunjungan.html

[^8]: https://www.youtube.com/watch?v=tURlHv8Ndxs

[^9]: https://www.youtube.com/watch?v=zh0k_e6F-So

[^10]: https://planetscale.com/learn/courses/mysql-for-developers/indexes/indexing-json-columns

[^11]: https://docs.oracle.com/cd/E17952_01/mysql-8.0-en/create-table-secondary-indexes.html

[^12]: https://livewire.laravel.com/docs/4.x/uploads

[^13]: https://laravel-news.com/livewire-file-upload

[^14]: https://github.com/livewire/livewire/discussions/8076

[^15]: https://oneuptime.com/blog/post/2026-01-24-mysql-json-data/view

[^16]: https://wontonee.com/file-uploads-in-livewire-3-0/

[^17]: https://www.youtube.com/watch?v=1cJMNxfpK7s

[^18]: https://www.youtube.com/watch?v=Z7gFpuXvgq4

[^19]: https://www.youtube.com/watch?v=ueK0oq3TqCM

[^20]: https://laravel-livewire.com/docs/2.x/input-validation

