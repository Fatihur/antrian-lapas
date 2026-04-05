<?php

namespace App\Livewire\Admin;

use App\Models\QueueCall;
use App\Models\VisitQueue;
use App\Models\VisitSchedule;
use App\Services\AudioFragmentService;
use Illuminate\Support\Collection;
use Livewire\Component;

class PanggilAntrian extends Component
{
    public string $dateFilter;

    public string $counter = 'LOKET 1';

    public array $availableCounters = ['LOKET 1', 'LOKET 2', 'LOKET 3', 'LOKET 4'];

    public Collection $waitingQueues;

    public ?VisitQueue $activeQueue = null;

    public Collection $callHistory;

    public bool $autoRefresh = true;

    public bool $soundEnabled = true;

    public int $recallCount = 0;

    public string $currentSession = '';

    // Audio fragments untuk preloading
    public array $audioFragments = [];

    protected $listeners = ['echo:queue-calls,QueueCalled' => 'handleQueueCalled'];

    protected AudioFragmentService $audioService;

    public function boot(AudioFragmentService $audioService): void
    {
        $this->audioService = $audioService;
    }

    public function mount(): void
    {
        $this->dateFilter = today()->format('Y-m-d');
        $this->waitingQueues = collect();
        $this->callHistory = collect();
        $this->detectCurrentSession();
        $this->loadQueues();
        $this->loadCallHistory();

        // Load audio fragments untuk preloading di frontend
        $this->audioFragments = $this->audioService->getAllFragmentUrls();
    }

    /**
     * Auto detect current session based on time
     */
    private function detectCurrentSession(): void
    {
        $hour = now()->format('H');

        if ($hour >= 8 && $hour < 12) {
            $this->currentSession = 'PAGI';
        } elseif ($hour >= 13 && $hour < 16) {
            $this->currentSession = 'SIANG';
        } else {
            $this->currentSession = 'PAGI';
        }
    }

    /**
     * Get display format for queue number (simple number only)
     */
    private function getSimpleQueueNumber(string $fullQueueNumber): string
    {
        $parts = explode('-', $fullQueueNumber);

        return $parts[0] ?? $fullQueueNumber;
    }

    public function loadQueues(): void
    {
        $this->detectCurrentSession();

        $schedule = VisitSchedule::where('tanggal', $this->dateFilter)
            ->where('sesi', $this->currentSession)
            ->first();

        if ($schedule) {
            $this->waitingQueues = VisitQueue::with(['followers'])
                ->where('visit_schedule_id', $schedule->id)
                ->whereIn('status_antrian', ['Disetujui', 'Menunggu Dipanggil'])
                ->orderBy('nomor_antrian')
                ->get();

            $this->activeQueue = VisitQueue::with(['followers', 'schedule'])
                ->where('visit_schedule_id', $schedule->id)
                ->where('status_antrian', 'Dipanggil')
                ->first();
        } else {
            $this->waitingQueues = collect();
            $this->activeQueue = null;
        }
    }

    public function loadCallHistory(): void
    {
        $this->callHistory = QueueCall::with(['queue'])
            ->whereHas('queue.schedule', function ($q) {
                $q->where('tanggal', $this->dateFilter)
                    ->where('sesi', $this->currentSession);
            })
            ->whereIn('status_panggilan', ['Dipanggil', 'Selesai', 'Dilewati'])
            ->latest('waktu_panggilan')
            ->limit(10)
            ->get();
    }

    public function updatedDateFilter(): void
    {
        $this->loadQueues();
        $this->loadCallHistory();
    }

    public function updatedCounter(): void
    {
        // Counter berubah, tidak perlu reload data
    }

    public function callNext(): void
    {
        if ($this->activeQueue) {
            $this->completeQueue();
        }

        $nextQueue = $this->waitingQueues->first();

        if (! $nextQueue) {
            $this->dispatch('notify',
                type: 'warning',
                message: 'Tidak ada antrian menunggu'
            );

            return;
        }

        $this->callQueue($nextQueue->id);
    }

    public function callQueue(int $queueId): void
    {
        if ($this->activeQueue && $this->activeQueue->id !== $queueId) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Selesaikan antrian yang sedang dipanggil terlebih dahulu'
            );

            return;
        }

        $queue = VisitQueue::with('schedule')->findOrFail($queueId);

        if (! $queue->canBeCalled()) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Antrian tidak dapat dipanggil'
            );

            return;
        }

        $queue->update(['status_antrian' => 'Dipanggil']);

        $call = QueueCall::create([
            'visit_queue_id' => $queueId,
            'called_by' => auth('admin')->id(),
            'loket' => $this->counter,
            'waktu_panggilan' => now(),
            'status_panggilan' => 'Dipanggil',
        ]);

        $simpleQueueNumber = $this->getSimpleQueueNumber($queue->nomor_antrian);

        // Create audio playlist
        $audioPlaylist = [];
        if ($this->soundEnabled) {
            $audioPlaylist = $this->audioService->createCallPlaylist(
                $simpleQueueNumber,
                $this->counter,
                false,
                0
            );
        }

        $this->recallCount = 0;
        $this->loadQueues();
        $this->loadCallHistory();

        // Dispatch event dengan playlist
        $this->dispatch('queue-called', [
            'queueNumber' => $simpleQueueNumber,
            'fullQueueNumber' => $queue->nomor_antrian,
            'counter' => $this->counter,
            'name' => $queue->nama_pengunjung,
            'soundEnabled' => $this->soundEnabled,
            'audioPlaylist' => $audioPlaylist,
            'fragments' => $this->audioFragments, // Untuk preloading
        ]);

        $this->dispatch('notify',
            type: 'success',
            message: "Antrian {$simpleQueueNumber} dipanggil ke {$this->counter}"
        );
    }

    public function recallQueue(): void
    {
        if (! $this->activeQueue) {
            $this->dispatch('notify',
                type: 'warning',
                message: 'Tidak ada antrian yang sedang dipanggil'
            );

            return;
        }

        $this->recallCount++;

        $call = QueueCall::where('visit_queue_id', $this->activeQueue->id)
            ->where('status_panggilan', 'Dipanggil')
            ->latest()
            ->first();

        if ($call) {
            $call->update([
                'waktu_panggilan' => now(),
                'recall_count' => $this->recallCount,
            ]);
        }

        $this->loadCallHistory();

        $simpleQueueNumber = $this->getSimpleQueueNumber($this->activeQueue->nomor_antrian);

        // Create audio playlist untuk recall
        $audioPlaylist = [];
        if ($this->soundEnabled) {
            $audioPlaylist = $this->audioService->createCallPlaylist(
                $simpleQueueNumber,
                $this->counter,
                true,
                $this->recallCount
            );
        }

        $this->dispatch('queue-recalled', [
            'queueNumber' => $simpleQueueNumber,
            'fullQueueNumber' => $this->activeQueue->nomor_antrian,
            'counter' => $this->counter,
            'name' => $this->activeQueue->nama_pengunjung,
            'soundEnabled' => $this->soundEnabled,
            'recallCount' => $this->recallCount,
            'audioPlaylist' => $audioPlaylist,
            'fragments' => $this->audioFragments,
        ]);

        $this->dispatch('notify',
            type: 'info',
            message: "Panggilan ulang ke-{$this->recallCount} untuk {$simpleQueueNumber}"
        );
    }

    public function completeQueue(): void
    {
        if (! $this->activeQueue) {
            return;
        }

        $queue = VisitQueue::findOrFail($this->activeQueue->id);
        $queue->update([
            'status_antrian' => 'Selesai',
            'waktu_selesai' => now(),
        ]);

        $call = QueueCall::where('visit_queue_id', $queue->id)
            ->where('status_panggilan', 'Dipanggil')
            ->latest()
            ->first();

        if ($call) {
            $call->markAsCompleted();
        }

        $this->activeQueue = null;
        $this->recallCount = 0;
        $this->loadQueues();
        $this->loadCallHistory();

        $this->dispatch('notify',
            type: 'success',
            message: 'Antrian selesai'
        );
    }

    public function skipQueue(): void
    {
        if (! $this->activeQueue) {
            return;
        }

        $queue = VisitQueue::findOrFail($this->activeQueue->id);
        $queue->update(['status_antrian' => 'Menunggu Dipanggil']);

        $call = QueueCall::where('visit_queue_id', $queue->id)
            ->where('status_panggilan', 'Dipanggil')
            ->latest()
            ->first();

        if ($call) {
            $call->markAsSkipped();
        }

        $this->activeQueue = null;
        $this->recallCount = 0;
        $this->loadQueues();
        $this->loadCallHistory();

        $this->dispatch('notify',
            type: 'warning',
            message: "Antrian {$queue->nomor_antrian} dilewati"
        );
    }

    public function toggleSound(): void
    {
        $this->soundEnabled = ! $this->soundEnabled;
    }

    public function render()
    {
        return view('livewire.admin.panggil-antrian')
            ->layout('layouts.admin', ['title' => 'Panggil Antrian']);
    }
}
