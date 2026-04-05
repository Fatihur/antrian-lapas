# Resources Directory - Frontend & Views

## Package Identity

Frontend layer: Blade templates untuk Livewire components, layouts, PDF templates, CSS (Tailwind), dan JavaScript (Vite + Alpine via Livewire).

## Setup & Run

```bash
# Install dependencies
npm install

# Development build (with HMR)
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

## Patterns & Conventions

### Blade Templates

**Organisasi**:
- Livewire components: `resources/views/livewire/[Public|Admin]/*.blade.php`
- Layouts: `resources/views/layouts/*.blade.php`
- Static pages: `resources/views/public/*.blade.php`
- Auth pages: `resources/views/admin/auth/*.blade.php`
- PDF templates: `resources/views/pdf/*.blade.php`

**Naming Convention**:
- Component views: `kebab-case.blade.php`
- Layouts: `layout-name.blade.php` (contoh: `admin.blade.php`)
- Folders: `PascalCase` untuk domain (Public, Admin)

**DO Pattern** (Livewire Component View):
```blade
{{-- resources/views/livewire/Public/ambil-antrian.blade.php --}}
<div>
    {{-- Multi-step indicator --}}
    <div class="flex justify-between mb-8">
        @foreach(['Data Pemohon', 'Pengikut', 'Jadwal', 'Konfirmasi'] as $index => $label)
            <div class="flex flex-col items-center {{ $currentStep > $index + 1 ? 'text-green-600' : ($currentStep === $index + 1 ? 'text-blue-600' : 'text-gray-400') }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $currentStep > $index + 1 ? 'bg-green-100' : ($currentStep === $index + 1 ? 'bg-blue-100' : 'bg-gray-100') }}">
                    {{ $index + 1 }}
                </div>
                <span class="text-xs mt-1">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    {{-- Step content --}}
    <div class="bg-white rounded-lg shadow p-6">
        @if($currentStep === 1)
            @include('livewire.Public.partials.step1-data-pemohon')
        @elseif($currentStep === 2)
            @livewire('public.ambil-antrian-pengikut', ['parentId' => $queueId])
        @endif
    </div>

    {{-- Navigation buttons --}}
    <div class="flex justify-between mt-6">
        @if($currentStep > 1)
            <button wire:click="prevStep" class="btn-secondary">
                Kembali
            </button>
        @else
            <div></div>
        @endif
        
        @if($currentStep < $totalSteps)
            <button wire:click="nextStep" class="btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Selanjutnya</span>
                <span wire:loading>Loading...</span>
            </button>
        @else
            <button wire:click="submit" class="btn-success">
                Submit
            </button>
        @endif
    </div>
</div>
```

**DON'T**:
```blade
{{-- JANGAN: Logic kompleks di view --}}
@if($queue->status === 'Menunggu Verifikasi' && $queue->created_at->diffInHours(now()) < 24 && !$queue->isExpired())
    {{-- ... --}}
@endif

{{-- JANGAN: Inline styles --}}
<div style="background-color: #f3f4f6; padding: 20px;">

{{-- JANGAN: Direct DB queries --}}
@php
    $count = DB::table('visit_queues')->where('status', 'waiting')->count();
@endphp
```

### Layouts

**Base Layout Pattern** (`resources/views/layouts/app.blade.php`):
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Antrian Lapas Sumbawa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    @include('partials.navbar')
    
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>
    
    @include('partials.footer')
    @livewireScripts
</body>
</html>
```

**Admin Layout Pattern** (`resources/views/layouts/admin.blade.php`):
- Sidebar navigation
- Header dengan user info
- Content area yang extensible

### Tailwind CSS

**Configuration**: `tailwind.config.js` sudah configured dengan:
- Custom colors (primary: blue, success: green, danger: red)
- Custom fonts
- Content paths untuk Blade files

**Common Utility Classes**:
```blade
{{-- Buttons --}}
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
<button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
<button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">

{{-- Cards --}}
<div class="bg-white rounded-lg shadow-md p-6">

{{-- Forms --}}
<input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

{{-- Status badges --}}
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
```

### PDF Templates

**Location**: `resources/views/pdf/*.blade.php`

**Pattern**: Simple HTML table layout untuk DomPDF
```blade
{{-- resources/views/pdf/ticket.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .ticket { border: 2px solid #000; padding: 20px; }
        .queue-number { font-size: 48px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>E-Antrian Lapas Sumbawa</h2>
        <div class="queue-number">{{ $queue->nomor_antrian }}</div>
        <p>NIK: {{ $queue->nik }}</p>
        <p>Nama: {{ $queue->nama }}</p>
        <p>Tanggal: {{ $queue->created_at->format('d M Y') }}</p>
    </div>
</body>
</html>
```

### Partials

**Pattern**: Extract reusable UI components ke partials
```
resources/views/partials/
├── navbar.blade.php
├── footer.blade.php
├── status-badge.blade.php
└── queue-card.blade.php
```

## Key Files

- **Public Layout**: `resources/views/layouts/app.blade.php`
- **Admin Layout**: `resources/views/layouts/admin.blade.php`
- **Public Home**: `resources/views/public/home.blade.php`
- **Ambil Antrian**: `resources/views/livewire/Public/ambil-antrian.blade.php`
- **Cek Status**: `resources/views/livewire/Public/cek-status-antrian.blade.php`
- **Admin Dashboard**: `resources/views/livewire/Admin/dashboard.blade.php`
- **Admin Antrian**: `resources/views/livewire/Admin/manajemen-antrian.blade.php`
- **Admin Panggil**: `resources/views/livewire/Admin/panggil-antrian.blade.php`
- **PDF Ticket**: `resources/views/pdf/ticket.blade.php`
- **CSS**: `resources/css/app.css`
- **JS**: `resources/js/app.js`
- **Tailwind Config**: `tailwind.config.js`

## JIT Index Hints

```bash
# Cari semua Blade views
find resources/views -name "*.blade.php" | sort

# Cari component Livewire
rg -n "wire:" resources/views

# Cari include partials
rg -n "@include" resources/views

# Cari livewire directives
rg -n "@livewire\(" resources/views

# Cari livewire attributes
rg -n "wire:click|wire:model|wire:loading" resources/views
```

## Common Gotchas

1. **File naming**: Pastikan nama file Blade sesuai dengan nama component Livewire (kebab-case)
2. **wire:key**: Gunakan untuk list items yang dinamis
3. **wire:loading**: Selalu gunakan untuk feedback saat processing
4. **Vite**: Jangan lupa `npm run build` sebelum deploy
5. **PDF**: DomPDF tidak support semua CSS modern, test PDF setelah styling

## Pre-PR Checks

```bash
npm run build && composer pint
```
