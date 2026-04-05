# Database Directory - Migrations & Schema

## Package Identity

Database layer: Migrations (schema versioning), Factories (test data generation), Seeders (initial data), SQLite untuk development.

## Setup & Run

```bash
# Fresh migrate dengan seed
php artisan migrate:fresh --seed

# Single migration
php artisan migrate --path=database/migrations/2026_04_04_000001_create_admins_table.php

# Rollback terakhir
php artisan migrate:rollback

# Reset semua
php artisan migrate:reset

# Status migration
php artisan migrate:status

# Generate factory
php artisan make:factory VisitQueueFactory --model=VisitQueue

# Generate seeder
php artisan make:seeder AdminSeeder
```

## Patterns & Conventions

### Migrations

**Naming Convention**:
- Format: `YYYY_MM_DD_HHMMSS_create_table_name_table.php`
- Contoh: `2026_04_04_000001_create_admins_table.php`
- Order: Sesuaikan dengan foreign key dependencies

**DO Pattern**:
```php
// database/migrations/2026_04_04_000003_create_visit_queues_table.php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_queues', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrian', 20)->unique();
            $table->string('nik', 16)->index();
            $table->string('nama', 100);
            $table->string('alamat', 255)->nullable();
            $table->string('no_hp', 15);
            $table->string('hubungan', 50);
            $table->string('foto_ktp_path', 255);
            $table->foreignId('visit_schedule_id')
                  ->constrained()
                  ->onDelete('restrict');
            $table->enum('status', [
                'Menunggu Verifikasi',
                'Disetujui',
                'Ditolak',
                'Menunggu Dipanggil',
                'Dipanggil',
                'Selesai',
                'Kedaluwarsa',
            ])->default('Menunggu Verifikasi');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes untuk performance
            $table->index(['status', 'created_at']);
            $table->index('nomor_antrian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_queues');
    }
};
```

**DON'T**:
```php
// JANGAN: Foreign key tanpa constrained()
$table->foreignId('schedule_id');  // Tanpa constraint

// JANGAN: String tanpa length limit
$table->string('nama');  // Bisa 255 char, berikan limit

// JANGAN: Nullable tanpa reason
$table->string('alamat');  // Kalau memang opsional, nullable()
```

### Factories

**Location**: `database/factories/*.php`

**DO Pattern**:
```php
// database/factories/VisitQueueFactory.php
class VisitQueueFactory extends Factory
{
    protected $model = VisitQueue::class;

    public function definition(): array
    {
        return [
            'nomor_antrian' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'nik' => $this->faker->nik(),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'no_hp' => $this->faker->phoneNumber(),
            'hubungan' => $this->faker->randomElement(['Keluarga', 'Saudara', 'Teman']),
            'foto_ktp_path' => 'ktp/sample.jpg',
            'visit_schedule_id' => VisitSchedule::factory(),
            'status' => 'Menunggu Verifikasi',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Disetujui',
            'approved_at' => now(),
        ]);
    }
}
```

### Seeders

**Location**: `database/seeders/*.php`

**DO Pattern**:
```php
// database/seeders/AdminSeeder.php
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'nama' => 'Super Admin',
            'role' => 'super_admin',
        ]);
        
        Admin::create([
            'username' => 'operator1',
            'password' => Hash::make('password123'),
            'nama' => 'Operator 1',
            'role' => 'operator',
        ]);
    }
}

// database/seeders/DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);
        
        // Factories untuk testing
        VisitSchedule::factory(10)->create();
        VisitQueue::factory(50)->create();
    }
}
```

### Schema Overview

**Tables**:
1. **admins** - Data admin/operator (auth guard: admin)
2. **visit_schedules** - Jadwal kunjungan dan kuota per sesi
3. **visit_queues** - Data antrian kunjungan utama
4. **visit_followers** - Data pengikut per antrian (1:N)
5. **queue_status_logs** - Audit log perubahan status
6. **queue_calls** - Log pemanggilan antrian
7. **users** - Laravel default (tidak digunakan)
8. **cache, jobs** - Laravel system tables

**Relationships**:
```
visit_queues
├── belongsTo: visit_schedules
├── hasMany: visit_followers
├── hasMany: queue_status_logs
└── hasMany: queue_calls

visit_schedules
└── hasMany: visit_queues
```

**Status Flow**:
```
[Menunggu Verifikasi] 
       ↓
   [Disetujui] ←→ [Ditolak]
       ↓
[Menunggu Dipanggil]
       ↓
   [Dipanggil]
       ↓
    [Selesai]
       ↓
  [Kedaluwarsa] (timeout)
```

## Key Files

- **Admins**: `database/migrations/2026_04_04_000001_create_admins_table.php`
- **Schedules**: `database/migrations/2026_04_04_000002_create_visit_schedules_table.php`
- **Queues**: `database/migrations/2026_04_04_000003_create_visit_queues_table.php`
- **Followers**: `database/migrations/2026_04_04_000004_create_visit_followers_table.php`
- **Status Logs**: `database/migrations/2026_04_04_000005_create_queue_status_logs_table.php`
- **Calls**: `database/migrations/2026_04_04_000006_create_queue_calls_table.php`
- **Factories**: `database/factories/VisitQueueFactory.php`, `VisitFollowerFactory.php`, dll
- **Seeders**: `database/seeders/AdminSeeder.php`, `DatabaseSeeder.php`

## JIT Index Hints

```bash
# List semua migrations
ls -la database/migrations/

# Cari migration berdasarkan nama table
rg -n "create.*table" database/migrations

# Cari foreign key
rg -n "foreignId|constrained" database/migrations

# Cari indexes
rg -n "->index\(" database/migrations

# Cari enum/status columns
rg -n "->enum\(" database/migrations
```

## Common Gotchas

1. **Migration order**: Foreign key tables harus dibuat SEBELUM table yang mereferensinya
2. **constrained()**: Selalu gunakan untuk foreign key constraints
3. **Indexes**: Tambahkan index untuk kolom yang sering di-query (nik, status, created_at)
4. **Timestamps**: Gunakan `$table->timestamps()` untuk created_at/updated_at
5. **Soft deletes**: Pertimbangkan `$table->softDeletes()` untuk data penting
6. **Rollback**: Test `migrate:rollback` sebelum commit migration

## Pre-PR Checks

```bash
php artisan migrate:fresh --seed && php artisan test
```

## IMPORTANT

**NEVER** edit existing migrations yang sudah di-push ke production. 
Gunakan migration baru untuk perubahan schema.
