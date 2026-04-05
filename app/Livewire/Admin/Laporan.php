<?php

namespace App\Livewire\Admin;

use App\Models\VisitQueue;
use App\Services\PdfTicketService;
use Livewire\Component;

class Laporan extends Component
{
    public $startDate;
    public $endDate;
    public $statusFilter = '';
    public $sessionFilter = '';
    
    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }
    
    public function getReportData()
    {
        $query = VisitQueue::with(['schedule', 'followers'])
            ->whereHas('schedule', function($q) {
                $q->whereBetween('tanggal', [$this->startDate, $this->endDate]);
            })
            ->when($this->statusFilter, function($q) {
                $q->where('status_antrian', $this->statusFilter);
            })
            ->when($this->sessionFilter, function($q) {
                $q->whereHas('schedule', function($sq) {
                    $sq->where('sesi', $this->sessionFilter);
                });
            });
            
        $queues = $query->orderBy('waktu_daftar', 'desc')->get();
        
        $summary = [
            'total' => $queues->count(),
            'by_status' => $queues->groupBy('status_antrian')->map->count(),
            'by_session' => $queues->groupBy(function($q) {
                return $q->schedule->sesi;
            })->map->count(),
            'total_visitors' => $queues->sum(function($q) {
                return $q->followers->count() + 1;
            }),
        ];
        
        return [
            'queues' => $queues,
            'summary' => $summary,
        ];
    }
    
    public function exportPdf()
    {
        $data = $this->getReportData();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', [
            'queues' => $data['queues'],
            'summary' => $data['summary'],
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Antrian_' . now()->format('Y-m-d') . '.pdf');
    }
    
    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\QueueReportExport($this->startDate, $this->endDate, $this->statusFilter, $this->sessionFilter),
            'Laporan_Antrian_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function render()
    {
        $data = $this->getReportData();
        
        return view('livewire.admin.laporan', [
            'queues' => $data['queues'],
            'summary' => $data['summary'],
        ])->layout('layouts.admin', ['title' => 'Laporan']);
    }
}
