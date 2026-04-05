<?php

namespace App\Livewire\Public;

use App\Models\VisitQueue;
use App\Services\PdfTicketService;
use Livewire\Component;

class CekStatusAntrian extends Component
{
    public $nik_pendaftar = '';

    public $queues = [];

    public $searched = false;

    public $selectedQueue = null;

    public function search()
    {
        $this->validate([
            'nik_pendaftar' => 'required|string|min:10|max:25',
        ], [
            'nik_pendaftar.required' => 'NIK wajib diisi',
            'nik_pendaftar.min' => 'NIK minimal 10 karakter',
        ]);

        $this->queues = VisitQueue::with(['schedule', 'followers'])
            ->where('nik_pendaftar', $this->nik_pendaftar)
            ->whereIn('status_antrian', [
                'Disetujui',
                'Menunggu Dipanggil',
                'Dipanggil',
            ])
            ->orderBy('waktu_daftar', 'desc')
            ->get();

        $this->searched = true;
        $this->selectedQueue = null;
    }

    public function selectQueue($queueId)
    {
        $this->selectedQueue = $this->queues->firstWhere('id', $queueId);
    }

    public function downloadPdf($queueId)
    {
        $queue = VisitQueue::with(['schedule', 'followers'])->findOrFail($queueId);
        $pdfService = app(PdfTicketService::class);

        return $pdfService->download($queue);
    }

    public function getStatusColor($status)
    {
        return match ($status) {
            'Disetujui' => 'green',
            'Menunggu Dipanggil' => 'blue',
            'Dipanggil' => 'indigo',
            'Selesai' => 'gray',
            'Kedaluwarsa' => 'gray',
            default => 'gray',
        };
    }

    public function render()
    {
        return view('livewire.public.cek-status-antrian')
            ->layout('layouts.app', ['title' => 'Cek Status Antrian']);
    }
}
