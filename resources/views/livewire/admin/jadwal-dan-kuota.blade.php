<div class="space-y-6">
    {{-- Header with Navigation & View Toggle --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Jadwal & Kuota</h3>
                <p class="text-sm text-gray-500">Kelola jadwal kunjungan dan kuota per sesi</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            {{-- View Toggle --}}
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button wire:click="toggleViewMode" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2
                        {{ $viewMode === 'calendar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Kalender
                </button>
                <button wire:click="toggleViewMode" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2
                        {{ $viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    List
                </button>
            </div>
            
            <button wire:click="openBulkModal" 
                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Generate Bulk
            </button>
            <button wire:click="openCreateModal" 
                    class="px-5 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 font-semibold shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Jadwal
            </button>
        </div>
    </div>
    
    {{-- CALENDAR VIEW --}}
    @if($viewMode === 'calendar')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Calendar Header with Month Navigation --}}
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center justify-between">
                    <button wire:click="previousMonth" 
                            class="p-2 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    
                    <h4 class="text-lg font-bold text-gray-900">
                        {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('id')->format('F Y') }}
                    </h4>
                    
                    <button wire:click="nextMonth" 
                            class="p-2 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Calendar Grid --}}
            <div class="p-4">
                {{-- Day Headers --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="text-center py-2 text-xs font-bold text-gray-500 uppercase">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>
                
                {{-- Calendar Days --}}
                <div class="grid grid-cols-7 gap-1">
                    @php
                        $startOfGrid = $startOfMonth->copy()->startOfWeek();
                        $endOfGrid = $endOfMonth->copy()->endOfWeek();
                        $currentDay = $startOfGrid->copy();
                    @endphp
                    
                    @while($currentDay->lte($endOfGrid))
                        @php
                            $dateKey = $currentDay->format('Y-m-d');
                            $isCurrentMonth = $currentDay->month === $currentMonth;
                            $isToday = $currentDay->isToday();
                            $daySchedules = $calendarData[$dateKey] ?? [];
                            
                            // Calculate status
                            $hasSchedules = count($daySchedules) > 0;
                            $allClosed = $hasSchedules && collect($daySchedules)->every(fn($s) => $s->status_jadwal === 'tutup');
                            $allOpen = $hasSchedules && collect($daySchedules)->every(fn($s) => $s->status_jadwal === 'buka');
                            $mixed = $hasSchedules && !$allClosed && !$allOpen;
                            
                            // Calculate quota usage
                            $totalQuota = collect($daySchedules)->sum('kuota_maksimal');
                            $usedQuota = collect($daySchedules)->sum('kuota_terpakai');
                            $quotaPercentage = $totalQuota > 0 ? ($usedQuota / $totalQuota) * 100 : 0;
                        @endphp
                        
                        <div wire:click="selectDate('{{ $dateKey }}')"
                             class="min-h-[100px] p-2 border-2 rounded-lg cursor-pointer transition-all hover:shadow-md
                             {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }}
                             {{ $isToday ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }}
                             {{ !$isCurrentMonth ? 'opacity-50' : '' }}
                             {{ $hasSchedules && !$allClosed ? 'hover:border-teal-400' : 'hover:border-gray-300' }}">
                            {{-- Date Number --}}
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-bold {{ $isToday ? 'text-blue-600' : ($isCurrentMonth ? 'text-gray-900' : 'text-gray-400') }}">
                                    {{ $currentDay->day }}
                                </span>
                                @if($isToday)
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Hari ini</span>
                                @endif
                            </div>
                            
                            {{-- Schedule Indicators --}}
                            @if($hasSchedules)
                                <div class="space-y-1">
                                    {{-- Status Badge --}}
                                    <div class="flex gap-1">
                                        @if($allOpen)
                                            <span class="flex-1 text-xs bg-green-100 text-green-700 px-1 py-0.5 rounded text-center">Buka</span>
                                        @elseif($allClosed)
                                            <span class="flex-1 text-xs bg-red-100 text-red-700 px-1 py-0.5 rounded text-center">Tutup</span>
                                        @else
                                            <span class="flex-1 text-xs bg-yellow-100 text-yellow-700 px-1 py-0.5 rounded text-center">Campur</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Session Count --}}
                                    <div class="flex gap-1">
                                        @foreach($daySchedules as $schedule)
                                            <span class="w-2 h-2 rounded-full {{ $schedule->status_jadwal === 'buka' ? 'bg-green-400' : 'bg-red-400' }}" 
                                                  title="{{ $schedule->sesi }}: {{ $schedule->kuota_terpakai }}/{{ $schedule->kuota_maksimal }}"></span>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Quota Bar (if open) --}}
                                    @if(!$allClosed && $totalQuota > 0)
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="bg-teal-500 h-1.5 rounded-full transition-all" 
                                                 style="width: {{ $quotaPercentage }}%"></div>
                                        </div>
                                        <p class="text-[10px] text-gray-500 text-center">{{ $usedQuota }}/{{ $totalQuota }}</p>
                                    @endif
                                </div>
                            @else
                                @if($isCurrentMonth && $currentDay->gte(now()))
                                    <div class="h-full flex items-center justify-center">
                                        <span class="text-xs text-gray-400">+ Buat</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        @php
                            $currentDay->addDay();
                        @endphp
                    @endwhile
                </div>
            </div>
            
            {{-- Legend --}}
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-green-400"></span>
                        <span class="text-gray-600">Buka</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-red-400"></span>
                        <span class="text-gray-600">Tutup</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-yellow-400"></span>
                        <span class="text-gray-600">Campur</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600">Hari ini</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- LIST VIEW (Original Table) --}}
    @if($viewMode === 'list')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kuota</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $schedule->tanggal->format('d F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-3 py-1 rounded-lg text-xs font-bold
                                    {{ $schedule->sesi === 'PAGI' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $schedule->sesi }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $percentage = $schedule->kuota_maksimal > 0 ? ($schedule->kuota_terpakai / $schedule->kuota_maksimal) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 w-24">
                                        <div class="bg-teal-600 h-2 rounded-full transition-all" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $schedule->kuota_terpakai }}/{{ $schedule->kuota_maksimal }}
                                    </span>
                                    <span class="text-xs text-gray-500">({{ $schedule->sisa_kuota }} tersisa)</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <button wire:click="toggleStatus({{ $schedule->id }})" 
                                        class="px-3 py-1.5 rounded-full text-xs font-bold transition-all
                                            {{ $schedule->status_jadwal === 'buka' ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    {{ $schedule->status_jadwal === 'buka' ? '🟢 Buka' : '🔴 Tutup' }}
                                </button>
                            </td>
                            <td class="px-5 py-3">
                                <button wire:click="openEditModal({{ $schedule->id }})" 
                                        class="px-4 py-2 bg-teal-100 text-teal-700 rounded-lg hover:bg-teal-200 font-medium text-sm transition-colors flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="font-medium">Belum ada jadwal</p>
                                <p class="text-sm text-gray-400 mt-1">Klik "Tambah Jadwal" untuk membuat jadwal baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $schedules->links() }}
        </div>
    </div>
    @endif
    
    {{-- Modal Single --}}
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full shadow-2xl">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-teal-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $editMode ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h4>
                    </div>
                </div>
                <form wire:submit.prevent="save" class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="tanggal" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none"
                               min="{{ now()->format('Y-m-d') }}">
                        @error('tanggal') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Sesi <span class="text-red-500">*</span></label>
                        <select wire:model="sesi" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="">-- Pilih Sesi --</option>
                            <option value="PAGI">Pagi (08:00 - 12:00)</option>
                            <option value="SIANG">Siang (13:00 - 16:00)</option>
                        </select>
                        @error('sesi') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Kuota Maksimal <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="kuota_maksimal" min="1" max="500"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none"
                               placeholder="Masukkan jumlah kuota">
                        @error('kuota_maksimal') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Status Jadwal</label>
                        <select wire:model="status_jadwal" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none bg-white">
                            <option value="buka">🟢 Buka</option>
                            <option value="tutup">🔴 Tutup</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeModal" 
                                class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium shadow-md transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Bulk Generate --}}
    @if($showBulkModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">Generate Jadwal Bulk</h4>
                            <p class="text-sm text-gray-500">Buat banyak jadwal sekaligus untuk rentang tanggal</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    {{-- Form Input --}}
                    @if(!$showPreview)
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="bulk_tanggal_mulai" 
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:outline-none"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('bulk_tanggal_mulai') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="bulk_tanggal_selesai" 
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:outline-none"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('bulk_tanggal_selesai') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Pilih Hari <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border-2 rounded-lg cursor-pointer transition-all
                                                {{ in_array($hari, $bulk_hari) ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-300' }}">
                                        <input type="checkbox" wire:model="bulk_hari" value="{{ $hari }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                        <span class="text-sm font-medium">{{ $hari }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('bulk_hari') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Kuota Default <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="bulk_kuota_maksimal" min="1" max="500"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:outline-none">
                                @error('bulk_kuota_maksimal') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Status Default</label>
                                <select wire:model="bulk_default_status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 focus:outline-none bg-white">
                                    <option value="buka">🟢 Buka</option>
                                    <option value="tutup">🔴 Tutup</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="bulk_skip_holidays" id="skip_holidays" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            <label for="skip_holidays" class="text-sm font-medium text-gray-700">Lewati tanggal merah (libur nasional)</label>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" wire:click="closeBulkModal" 
                                    class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                                Batal
                            </button>
                            <button type="button" wire:click="generatePreview" 
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-md transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Preview
                            </button>
                        </div>
                    @endif

                    {{-- Preview Section --}}
                    @if($showPreview)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="font-bold text-gray-900">Preview Jadwal ({{ count($bulkPreview) }} item)</h5>
                                <button type="button" wire:click="$set('showPreview', false)" 
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    ✏️ Edit Kriteria
                                </button>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto border-2 border-gray-200 rounded-lg">
                                <table class="w-full">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Hari</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Sesi</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($bulkPreview as $item)
                                            <tr class="{{ $item['exists'] ? 'bg-red-50' : 'bg-green-50' }}">
                                                <td class="px-3 py-2 text-sm">{{ $item['tanggal_formatted'] }}</td>
                                                <td class="px-3 py-2 text-sm">{{ $item['hari'] }}</td>
                                                <td class="px-3 py-2">
                                                    <span class="px-2 py-1 rounded text-xs font-bold
                                                        {{ $item['sesi'] === 'PAGI' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ $item['sesi'] }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    @if($item['exists'])
                                                        <span class="text-xs text-red-600 font-medium">⚠️ Sudah ada</span>
                                                    @else
                                                        <span class="text-xs text-green-600 font-medium">✅ Baru</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-800">
                                    <span class="font-bold">Catatan:</span> 
                                    Jadwal yang sudah ada akan dilewati. 
                                    {{ count(array_filter($bulkPreview, fn($i) => !$i['exists'])) }} jadwal baru akan dibuat.
                                </p>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                                <button type="button" wire:click="closeBulkModal" 
                                        class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                                    Batal
                                </button>
                                <button type="button" wire:click="saveBulk" 
                                        class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Generate Jadwal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
