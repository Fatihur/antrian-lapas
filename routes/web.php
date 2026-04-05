<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\JadwalDanKuota;
use App\Livewire\Admin\Laporan;
use App\Livewire\Admin\ManajemenAdmin;
use App\Livewire\Admin\ManajemenAntrian;
use App\Livewire\Admin\PanggilAntrian;
use App\Livewire\Public\AmbilAntrian;
use App\Livewire\Public\CekStatusAntrian;
use App\Models\VisitQueue;
use App\Services\PdfTicketService;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    // Get today's active queue number
    $today = now()->format('Y-m-d');
    $currentSession = now()->format('H') < 12 ? 'PAGI' : 'SIANG';

    // Get latest queue number for today
    $latestQueue = VisitQueue::whereHas('schedule', function ($q) use ($today, $currentSession) {
        $q->where('tanggal', $today)
            ->where('sesi', $currentSession);
    })->latest()->first();

    $currentQueueNumber = $latestQueue ? explode('-', $latestQueue->nomor_antrian)[0] : '-';

    // Service hours
    $serviceHours = [
        'pagi' => ['08:00', '12:00'],
        'siang' => ['13:00', '16:00'],
    ];

    return view('public.home', compact('currentQueueNumber', 'serviceHours', 'currentSession'));
})->name('home');

Route::get('/ambil-antrian', AmbilAntrian::class)->name('ambil-antrian');
Route::get('/cek-status', CekStatusAntrian::class)->name('cek-status');

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// Protected Admin Routes
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/antrian', ManajemenAntrian::class)->name('admin.queues.index');
    Route::get('/panggil', PanggilAntrian::class)->name('admin.queues.call');
    Route::get('/jadwal', JadwalDanKuota::class)->name('admin.schedules.index');
    Route::get('/laporan', Laporan::class)->name('admin.reports.index');
    Route::get('/pengguna', ManajemenAdmin::class)->name('admin.admins.index');
});

// PDF Download Routes
Route::get('/download-pdf/{queue}', function ($queueId) {
    $queue = VisitQueue::findOrFail($queueId);
    $pdfService = app(PdfTicketService::class);

    return $pdfService->download($queue);
})->name('download-pdf');
