# E-Antrian Lapas Sumbawa

## Project Snapshot

Aplikasi antrian kunjungan online untuk Lapas Kelas IIB Sumbawa. Dibangun dengan Laravel 11 + Livewire 3 + Tailwind CSS. Monolithic PHP application dengan arsitektur Livewire untuk interaktivitas real-time.

## Setup Commands

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database (MySQL required)
php artisan migrate
php artisan db:seed

# Storage & assets
php artisan storage:link
npm run build

# Development
php artisan serve
npm run dev
```

## Universal Conventions

- **PHP Style**: PSR-12 compliance, `pint` untuk formatting
- **Naming**: PascalCase untuk class, camelCase untuk methods/properties
- **Database**: Timestamp prefixes untuk migrations (`YYYY_MM_DD_HHMMSS`)
- **Git**: Commit messages dalam Bahasa Indonesia yang jelas
- **Security**: NEVER commit `.env`, gunakan `env()` untuk secrets

## Security & Secrets

- **NEVER** commit file `.env` atau credentials
- Gunakan `env()` helper untuk semua konfigurasi sensitif
- Upload files hanya ke `storage/app/` dengan symlink ke `public/`
- Validate semua input di Livewire components dan Controllers

## JIT Index

### Package Structure
- **Backend Logic**: `app/` → [see app/AGENTS.md](app/AGENTS.md)
- **Views/Frontend**: `resources/` → [see resources/AGENTS.md](resources/AGENTS.md)
- **Database**: `database/` → [see database/AGENTS.md](database/AGENTS.md)

### Quick Find Commands

```bash
# Cari Livewire component
rg -n "class.*extends Component" app/Livewire

# Cari Model
rg -n "class.*extends Model" app/Models

# Cari Blade view
find resources/views -name "*.blade.php" | head -20

# Cari migration
ls -la database/migrations/ | tail -10

# Cari route
rg -n "Route::" routes/
```

## Definition of Done

- [ ] Fitur berjalan sesuai requirement
- [ ] Tidak ada error di `storage/logs/laravel.log`
- [ ] Code melewati `composer pint`
- [ ] Migrasi berjalan tanpa error
- [ ] Assets compiled dengan `npm run build`
