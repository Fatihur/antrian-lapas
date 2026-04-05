<?php

namespace App\Livewire\Admin;

use App\Models\VisitQueue;
use App\Models\VisitSchedule;
use Livewire\Component;

class Dashboard extends Component
{
    public $todayQueues;
    public $statusCounts;
    public $currentCalledQueue = null;
    
    public function mount()
    {
        $this->loadDashboardData();
    }
    
    public function loadDashboardData()
    {
        $this->todayQueues = VisitQueue::with(['schedule', 'followers'])
            ->whereHas('schedule', function($q) {
                $q->whereDate('tanggal', today());
            })
            ->orderBy('waktu_daftar', 'desc')
            ->take(10)
            ->get();
            
        $this->statusCounts = VisitQueue::whereHas('schedule', function($q) {
                $q->whereDate('tanggal', today());
            })
            ->selectRaw('status_antrian, count(*) as total')
            ->groupBy('status_antrian')
            ->pluck('total', 'status_antrian')
            ->toArray();
            
        $this->currentCalledQueue = VisitQueue::with(['schedule', 'followers'])
            ->where('status_antrian', 'Dipanggil')
            ->whereHas('schedule', function($q) {
                $q->whereDate('tanggal', today());
            })
            ->first();
    }
    
    public function getTotalQueuesToday()
    {
        return array_sum($this->statusCounts);
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
