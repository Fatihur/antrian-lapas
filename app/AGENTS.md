# App Directory - Laravel & Livewire

## Package Identity

Backend logic layer: Models (Eloquent), Livewire Components (interactive UI), Services (business logic), Controllers (HTTP), Middleware (auth & security).

## Setup & Run

```bash
# Code quality
composer pint                    # Format PHP code
php artisan cache:clear          # Clear all caches

# Testing
php artisan test

# Single test
php artisan test --filter=TestName
```

## Patterns & Conventions

### Livewire Components
**Organisasi**:
- Public components: `app/Livewire/Public/*.php`
- Admin components: `app/Livewire/Admin/*.php`
- Views: `resources/views/livewire/[Public|Admin]/*.blade.php`

**Naming Convention**:
- Component class: `PascalCase` (contoh: `AmbilAntrian.php`)
- View file: `kebab-case.blade.php` (contoh: `ambil-antrian.blade.php`)
- Component name: lowercase dengan dot notation (contoh: `public.ambil-antrian`)

**DO Pattern** (Multi-step form):
```php
// app/Livewire/Public/AmbilAntrian.php
class AmbilAntrian extends Component
{
    public int $currentStep = 1;
    public array $formData = [];
    
    protected array $rules = [
        'formData.nik' => 'required|digits:16',
        'formData.nama' => 'required|string|max:100',
    ];
    
    public function nextStep(): void
    {
        $this->validate($this->stepRules[$this->currentStep]);
        $this->currentStep++;
    }
    
    public function submit(): void
    {
        $this->validate();
        // Business logic via Service
        $queue = app(QueueNumberGenerator::class)->generate($this->formData);
        $this->dispatch('queue-created', queueId: $queue->id);
    }
}
```

**DON'T**:
```php
// JANGAN: Business logic langsung di component
public function submit() {
    $queue = new VisitQueue();  // Langsung create model
    // ... 50+ baris logic
}

// JANGAN: Public properties tanpa type hint
public $data;  // Tanpa type
```

### Models
**Location**: `app/Models/*.php`

**DO Pattern**:
```php
// app/Models/VisitQueue.php
class VisitQueue extends Model
{
    protected $fillable = [
        'nik',
        'nama', 
        'status',
        'visit_schedule_id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function followers(): HasMany
    {
        return $this->hasMany(VisitFollower::class);
    }
    
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(VisitSchedule::class, 'visit_schedule_id');
    }
    
    // Scopes
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'Menunggu Verifikasi');
    }
    
    // Business logic methods
    public function approve(?string $reason = null): void
    {
        $this->update(['status' => 'Disetujui', 'approved_at' => now()]);
        QueueStatusLog::create([
            'visit_queue_id' => $this->id,
            'from_status' => 'Menunggu Verifikasi',
            'to_status' => 'Disetujui',
            'reason' => $reason,
        ]);
    }
}
```

### Services
**Location**: `app/Services/*.php`

**Pattern**: Encapsulate complex business logic
```php
// app/Services/QueueNumberGenerator.php
class QueueNumberGenerator
{
    public function generate(array $data): VisitQueue
    {
        // Complex logic: cek kuota, generate nomor, dll
        return DB::transaction(function () use ($data) {
            $number = $this->generateNumber($data['schedule_id']);
            return VisitQueue::create([
                ...$data,
                'nomor_antrian' => $number,
                'status' => 'Menunggu Verifikasi',
            ]);
        });
    }
}
```

### Controllers
**Location**: `app/Http/Controllers/*.php`

**Pattern**: Minimal logic, gunakan untuk non-Livewire routes (auth, PDF download)
```php
// app/Http/Controllers/AuthController.php
class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('/admin/dashboard');
        }
        
        return back()->withErrors(['username' => 'Invalid credentials']);
    }
}
```

### Middleware
**Location**: `app/Http/Middleware/*.php`

**DO**: `AdminMiddleware` untuk proteksi route admin

## Key Files

- **Base Model**: `app/Models/User.php`
- **Livewire Public**: `app/Livewire/Public/AmbilAntrian.php`
- **Livewire Public**: `app/Livewire/Public/CekStatusAntrian.php`
- **Livewire Admin**: `app/Livewire/Admin/Dashboard.php`
- **Livewire Admin**: `app/Livewire/Admin/ManajemenAntrian.php`
- **Service**: `app/Services/QueueNumberGenerator.php`
- **Service**: `app/Services/PdfTicketService.php`
- **Auth**: `app/Http/Controllers/AuthController.php`
- **Middleware**: `app/Http/Middleware/AdminMiddleware.php`

## JIT Index Hints

```bash
# Cari semua Livewire components
rg -n "class.*Component" app/Livewire --type php

# Cari semua Models
find app/Models -name "*.php" | xargs basename -s .php

# Cari method dalam component
rg -n "public function" app/Livewire/Admin/ManajemenAntrian.php

# Cari semua Services
ls app/Services/

# Cari validation rules
rg -n "rules.*=" app/Livewire
```

## Common Gotchas

1. **Livewire property types**: SELALU gunakan type hints (PHP 8.2+)
2. **File uploads**: Gunakan `#[Validate]` attribute atau `WithFileUploads` trait
3. **Database transactions**: Gunakan `DB::transaction()` untuk operasi multi-step
4. **Status constants**: Definisikan di Model, jangan hardcode string
5. **Auth guard**: Admin pakai guard `admin`, bukan default `web`

## Pre-PR Checks

```bash
composer pint && php artisan test
```
