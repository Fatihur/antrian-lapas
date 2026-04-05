<div class="space-y-6">

    @php
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $hariIndo = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    @endphp

    {{-- ── Toggle View ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
            <button wire:click="$set('viewMode', 'calendar')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all
                       {{ $viewMode === 'calendar' ? 'bg-teal-600 text-white shadow' : 'text-gray-500 hover:bg-gray-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Kalender
            </button>
            <button wire:click="$set('viewMode', 'list')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all
                       {{ $viewMode === 'list' ? 'bg-teal-600 text-white shadow' : 'text-gray-500 hover:bg-gray-100' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <line x1="4" y1="6" x2="20" y2="6"/>
                    <line x1="4" y1="10" x2="20" y2="10"/>
                    <line x1="4" y1="14" x2="20" y2="14"/>
                    <line x1="4" y1="18" x2="20" y2="18"/>
                </svg>
                Daftar
            </button>
        </div>

        @if($viewMode === 'calendar')
            <button wire:click="goToToday"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-200 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Hari Ini
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         CALENDAR VIEW
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($viewMode === 'calendar')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Calendar Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white">
                <button wire:click="previousMonth"
                    class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-teal-50 hover:text-teal-700 border border-gray-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div class="text-center">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $bulanIndo[$calendarMonth] }} {{ $calendarYear }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Klik tanggal untuk melihat daftar antrian</p>
                </div>

                <button wire:click="nextMonth"
                    class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-teal-50 hover:text-teal-700 border border-gray-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-4 px-6 py-3 border-b border-gray-100 bg-gray-50 text-xs">
                <span class="font-semibold text-gray-500 uppercase tracking-wide">Keterangan:</span>
                <span class="flex items-center gap-1.5 text-gray-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Disetujui / Aktif
                </span>
                <span class="flex items-center gap-1.5 text-gray-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Selesai
                </span>
            </div>

            {{-- Day-of-week Headers --}}
            <div class="grid grid-cols-7 bg-gray-100 border-b border-gray-200">
                @foreach($hariIndo as $i => $hari)
                    <div class="py-3 text-center text-xs font-bold uppercase tracking-wider border-r border-gray-200 last:border-r-0
                                {{ $i >= 5 ? 'text-red-500' : 'text-gray-600' }}">
                        {{ $hari }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            @php
                $cursor = $startOfCalendar->copy();
                $today  = \Carbon\Carbon::today()->format('Y-m-d');
            @endphp

            <div class="grid grid-cols-7">
                @while($cursor <= $endOfCalendar)
                    @php
                        $dateKey        = $cursor->format('Y-m-d');
                        $isCurrentMonth = (int)$cursor->month === (int)$calendarMonth;
                        $isToday        = $dateKey === $today;
                        $isSelected     = $selectedDate === $dateKey;
                        $dayData        = $calendarData[$dateKey] ?? null;
                        $hasData        = $dayData && $dayData['total'] > 0;
                        $isWeekend      = $cursor->dayOfWeek === 6 || $cursor->dayOfWeek === 0;
                        $isLastCol      = $cursor->dayOfWeek === 0;
                        $isNewRow       = $cursor->dayOfWeek === 1;
                    @endphp

                    <button
                        wire:click="selectDate('{{ $dateKey }}')"
                        @class([
                            'relative min-h-[110px] p-2 text-left w-full transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-400 border-r border-b border-gray-200',
                            'border-r-0' => $isLastCol,
                            'bg-teal-600 text-white'  => $isSelected,
                            'bg-teal-50'              => !$isSelected && $hasData && $isCurrentMonth,
                            'bg-white hover:bg-gray-50' => !$isSelected && !$hasData && $isCurrentMonth,
                            'bg-gray-50'              => !$isSelected && !$isCurrentMonth,
                        ])>

                        {{-- Day number --}}
                        <div class="flex items-center justify-between mb-2">
                            <span @class([
                                'inline-flex items-center justify-center w-7 h-7 rounded-full text-sm font-bold',
                                'bg-white text-teal-700'      => $isSelected && !$isToday,
                                'bg-yellow-400 text-white'    => $isToday && !$isSelected,
                                'bg-teal-700 text-white'      => $isToday && $isSelected,
                                'text-teal-700'               => !$isSelected && !$isToday && $hasData && $isCurrentMonth,
                                'text-red-400'                => !$isSelected && !$isToday && $isWeekend && $isCurrentMonth && !$hasData,
                                'text-gray-700'               => !$isSelected && !$isToday && !$isWeekend && $isCurrentMonth && !$hasData,
                                'text-gray-400'               => !$isCurrentMonth,
                            ])>
                                {{ $cursor->day }}
                            </span>

                            @if($hasData && $isCurrentMonth)
                                <span @class([
                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                    'bg-white/30 text-white'  => $isSelected,
                                    'bg-teal-600 text-white'  => !$isSelected,
                                ])>
                                    {{ $dayData['total'] }} pengunjung
                                </span>
                            @endif
                        </div>

                        {{-- Status breakdown --}}
                        @if($hasData && $isCurrentMonth)
                            <div class="space-y-1">
                                @if($dayData['disetujui'] > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                                        <span @class([
                                            'text-xs',
                                            'text-green-100' => $isSelected,
                                            'text-green-700' => !$isSelected,
                                        ])>{{ $dayData['disetujui'] }} disetujui</span>
                                    </div>
                                @endif
                                @if($dayData['selesai'] > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-gray-400 flex-shrink-0"></span>
                                        <span @class([
                                            'text-xs',
                                            'text-gray-200' => $isSelected,
                                            'text-gray-500' => !$isSelected,
                                        ])>{{ $dayData['selesai'] }} selesai</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </button>
                    @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>

        {{-- ── Selected Date Detail Panel ──────────────────────────────────── --}}
        @if($selectedDate)
            @php
                $selectedCarbon = \Carbon\Carbon::parse($selectedDate);
                $hariPanjang    = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Panel Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-teal-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                {{ $hariPanjang[$selectedCarbon->dayOfWeek] }},
                                {{ $selectedCarbon->day }} {{ $bulanIndo[$selectedCarbon->month] }} {{ $selectedCarbon->year }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ $selectedDateQueues->count() }} antrian ditemukan
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Filter status --}}
                        <select wire:model.live="statusFilter"
                            class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>

                        <button wire:click="$set('selectedDate', null)"
                            class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 border border-gray-200 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Antrian Table for Selected Date --}}
                @if($selectedDateQueues->isEmpty())
                    <div class="py-16 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Tidak ada antrian pada tanggal ini</p>
                        <p class="text-sm text-gray-400 mt-1">Coba ubah filter status</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Antrian</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pengunjung</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sesi</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengikut</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($selectedDateQueues as $queue)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 font-bold text-gray-900 font-mono text-sm">
                                            {{ $queue->nomor_antrian }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-xs font-bold text-teal-700">{{ substr($queue->nama_pengunjung, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $queue->nama_pengunjung }}</p>
                                                    <p class="text-xs text-gray-400">{{ $queue->nik_pendaftar }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                                {{ $queue->schedule->sesi === 'PAGI' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $queue->schedule->sesi }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-sm text-gray-600">
                                            {{ $queue->followers->count() > 0 ? '+' . $queue->followers->count() . ' orang' : '-' }}
                                        </td>
                                        <td class="px-5 py-3">
                                            @php
                                                $statusColor = match($queue->status_antrian) {
                                                    'Disetujui'           => 'bg-green-100 text-green-800',
                                                    'Menunggu Dipanggil'  => 'bg-blue-100 text-blue-800',
                                                    'Dipanggil'           => 'bg-indigo-100 text-indigo-800',
                                                    'Selesai'             => 'bg-gray-100 text-gray-700',
                                                    default               => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                                {{ $queue->status_antrian }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <button wire:click="showDetail({{ $queue->id }})"
                                                class="px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 rounded-lg text-sm font-medium transition-colors">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    @endif {{-- end calendar view --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         LIST VIEW
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($viewMode === 'list')

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" wire:model.live="search"
                            placeholder="Cari nomor antrian, nama, atau NIK..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm">
                    </div>
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-1 focus:ring-teal-500 bg-white text-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" wire:model.live="dateFilter"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm">
                </div>
                @if($search || $statusFilter || $dateFilter)
                    <div>
                        <button wire:click="$set('search',''); $set('statusFilter',''); $set('dateFilter','')"
                            class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Reset
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Antrian</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pengunjung</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sesi</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($queues as $queue)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 font-bold text-gray-900 font-mono text-sm">{{ $queue->nomor_antrian }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-bold text-teal-700">{{ substr($queue->nama_pengunjung, 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $queue->nama_pengunjung }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-600">
                                    {{ $queue->schedule->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $queue->schedule->sesi === 'PAGI' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $queue->schedule->sesi }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                        @php
                                            $statusColor = match($queue->status_antrian) {
                                                'Disetujui'           => 'bg-green-100 text-green-800',
                                                'Menunggu Dipanggil'  => 'bg-blue-100 text-blue-800',
                                                'Dipanggil'           => 'bg-indigo-100 text-indigo-800',
                                                'Selesai'             => 'bg-gray-100 text-gray-700',
                                                default               => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                        {{ $queue->status_antrian }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <button wire:click="showDetail({{ $queue->id }})"
                                        class="px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 rounded-lg text-sm font-medium transition-colors">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">Tidak ada data antrian</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($queues->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $queues->links() }}
                </div>
            @endif
        </div>

    @endif {{-- end list view --}}


    {{-- ══════════════════════════════════════════════════════════════════════
         DETAIL MODAL
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showDetailModal && $selectedQueue)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">

                {{-- Modal Header --}}
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50 sticky top-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Detail Antrian</h4>
                            <p class="text-sm text-gray-500 font-mono">{{ $selectedQueue->nomor_antrian }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showDetailModal', false)"
                        class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Queue Summary --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Nomor Antrian</p>
                            <p class="text-2xl font-bold text-gray-900 font-mono">{{ $selectedQueue->nomor_antrian }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-500 mb-1">Kode Booking</p>
                            <p class="text-lg font-bold text-gray-900 font-mono">{{ $selectedQueue->kode_booking }}</p>
                        </div>
                    </div>

                    {{-- Schedule Info --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-teal-50 rounded-xl flex items-center gap-3">
                            <svg class="w-4 h-4 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <div>
                                <p class="text-xs text-teal-600">Tanggal Kunjungan</p>
                                <p class="text-sm font-bold text-teal-900">{{ $selectedQueue->schedule->tanggal->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-xl flex items-center gap-3">
                            <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <p class="text-xs text-orange-600">Sesi</p>
                                <p class="text-sm font-bold text-orange-900">{{ $selectedQueue->schedule->sesi }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Visitor Info --}}
                    <div class="space-y-3">
                        <h5 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Pengunjung
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 text-sm bg-gray-50 rounded-xl p-4">
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">Nama</span>
                                <span class="font-medium text-gray-900">{{ $selectedQueue->nama_pengunjung }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">NIK</span>
                                <span class="font-medium text-gray-900 font-mono">{{ $selectedQueue->nik_pendaftar }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">No. HP</span>
                                <span class="font-medium text-gray-900">{{ $selectedQueue->no_hp }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">Hubungan WBP</span>
                                <span class="font-medium text-gray-900">{{ $selectedQueue->hubungan_wbp }}</span>
                            </div>
                            <div class="flex gap-2 md:col-span-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">Nama WBP</span>
                                <span class="font-medium text-gray-900">{{ $selectedQueue->nama_wbp }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Followers --}}
                    @if($selectedQueue->followers->count() > 0)
                        <div class="space-y-3">
                            <h5 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Pengikut ({{ $selectedQueue->followers->count() }} orang)
                            </h5>
                            <div class="space-y-2">
                                @foreach($selectedQueue->followers as $i => $follower)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">{{ $i + 1 }}</span>
                                            <span class="font-medium text-gray-900 text-sm">{{ $follower->nama_pengikut }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $follower->jenis_kelamin_pengikut }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Status --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600 font-medium">Status Antrian</span>
                        @php
                            $statusColor = match($selectedQueue->status_antrian) {
                                'Disetujui'           => 'bg-green-100 text-green-800',
                                'Menunggu Dipanggil'  => 'bg-blue-100 text-blue-800',
                                'Dipanggil'           => 'bg-indigo-100 text-indigo-800',
                                'Selesai'             => 'bg-gray-200 text-gray-700',
                                default               => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-4 py-1.5 rounded-full text-sm font-bold {{ $statusColor }}">
                            {{ $selectedQueue->status_antrian }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
