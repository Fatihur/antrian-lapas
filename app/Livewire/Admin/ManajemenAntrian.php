<?php

namespace App\Livewire\Admin;

use App\Models\VisitQueue;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ManajemenAntrian extends Component
{
    use WithPagination;

    // View mode: 'calendar' or 'list'
    public $viewMode = 'calendar';

    // Calendar navigation
    public $calendarYear;
    public $calendarMonth;
    public $selectedDate = null;

    // List filters
    public $search = "";
    public $statusFilter = "";
    public $dateFilter = "";

    // Modals
    public $selectedQueue = null;
    public $showDetailModal = false;

    protected $queryString = [
        "viewMode",
        "search",
        "statusFilter",
        "dateFilter",
        "selectedDate",
    ];

    public function mount()
    {
        $this->calendarYear = now()->year;
        $this->calendarMonth = now()->month;
    }

    // ── Calendar navigation ─────────────────────────────────────────────────

    public function previousMonth()
    {
        $date = Carbon::create(
            $this->calendarYear,
            $this->calendarMonth,
            1,
        )->subMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
        $this->selectedDate = null;
    }

    public function nextMonth()
    {
        $date = Carbon::create(
            $this->calendarYear,
            $this->calendarMonth,
            1,
        )->addMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
        $this->selectedDate = null;
    }

    public function selectDate(string $date)
    {
        $this->selectedDate = $this->selectedDate === $date ? null : $date;
        $this->statusFilter = "";
    }

    public function goToToday()
    {
        $this->calendarYear = now()->year;
        $this->calendarMonth = now()->month;
        $this->selectedDate = now()->format("Y-m-d");
    }

    // ── List helpers ────────────────────────────────────────────────────────

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ── Modals ──────────────────────────────────────────────────────────────

    public function showDetail(int $queueId)
    {
        $this->selectedQueue = VisitQueue::with([
            "schedule",
            "followers",
            "statusLogs.admin",
        ])->find($queueId);
        $this->showDetailModal = true;
    }

    // ── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        // ── Calendar bounds ─────────────────────────────────────────────────
        $currentMonth = Carbon::create(
            $this->calendarYear,
            $this->calendarMonth,
            1,
        );
        $startOfCalendar = $currentMonth
            ->copy()
            ->startOfMonth()
            ->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $currentMonth
            ->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SUNDAY);

        // ── Per-date visitor counts for the displayed month ─────────────────
        $calendarData = VisitQueue::with("schedule")
            ->whereHas("schedule", function ($q) {
                $q->whereYear("tanggal", $this->calendarYear)->whereMonth(
                    "tanggal",
                    $this->calendarMonth,
                );
            })
            ->get()
            ->groupBy(fn($queue) => $queue->schedule->tanggal->format("Y-m-d"))
            ->map(
                fn($group) => [
                    "total" => $group->count(),
                    "disetujui" => $group
                        ->whereIn("status_antrian", [
                            "Disetujui",
                            "Menunggu Dipanggil",
                            "Dipanggil",
                        ])
                        ->count(),
                    "selesai" => $group
                        ->where("status_antrian", "Selesai")
                        ->count(),
                ],
            );

        // ── Queues for the selected date ─────────────────────────────────────
        $selectedDateQueues = collect();
        if ($this->selectedDate) {
            $selectedDateQueues = VisitQueue::with(["schedule", "followers"])
                ->whereHas(
                    "schedule",
                    fn($q) => $q->whereDate("tanggal", $this->selectedDate),
                )
                ->when(
                    $this->statusFilter,
                    fn($q) => $q->where("status_antrian", $this->statusFilter),
                )
                ->orderBy("nomor_antrian")
                ->get();
        }

        // ── Full list (list-view mode) ────────────────────────────────────────
        $queues = VisitQueue::with(["schedule", "followers"])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where(
                        "nomor_antrian",
                        "like",
                        "%" . $this->search . "%",
                    )
                        ->orWhere(
                            "nama_pengunjung",
                            "like",
                            "%" . $this->search . "%",
                        )
                        ->orWhere(
                            "nik_pendaftar",
                            "like",
                            "%" . $this->search . "%",
                        );
                });
            })
            ->when(
                $this->statusFilter,
                fn($q) => $q->where("status_antrian", $this->statusFilter),
            )
            ->when(
                $this->dateFilter,
                fn($q) => $q->whereHas(
                    "schedule",
                    fn($sq) => $sq->whereDate("tanggal", $this->dateFilter),
                ),
            )
            ->orderBy("waktu_daftar", "desc")
            ->paginate(15);

        $statuses = [
            'Disetujui',
            'Menunggu Dipanggil',
            'Dipanggil',
            'Selesai',
            'Kedaluwarsa',
        ];

        return view(
            "livewire.admin.manajemen-antrian",
            compact(
                "queues",
                "currentMonth",
                "startOfCalendar",
                "endOfCalendar",
                "calendarData",
                "selectedDateQueues",
                "statuses",
            ),
        )->layout("layouts.admin", ["title" => "Data Antrian"]);
    }
}
