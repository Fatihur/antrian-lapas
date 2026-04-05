<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Laporan</h3>
                <p class="text-sm text-gray-500">Rekapan data antrian dan kunjungan</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button wire:click="exportPdf" 
                    class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </button>
            <button wire:click="exportExcel" 
                    class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-semibold shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>
    
    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model="startDate" 
                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-200 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model="endDate" 
                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-200 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select wire:model="statusFilter" 
                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-200 focus:outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Menunggu Dipanggil">Menunggu Dipanggil</option>
                    <option value="Dipanggil">Dipanggil</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sesi</label>
                <select wire:model="sessionFilter" 
                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-200 focus:outline-none bg-white">
                    <option value="">Semua Sesi</option>
                    <option value="PAGI">Pagi</option>
                    <option value="SIANG">Siang</option>
                </select>
            </div>
        </div>
    </div>
    
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Total Antrian</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $summary['total'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Total Pengunjung</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $summary['total_visitors'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Sesi Pagi</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $summary['by_session']['PAGI'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Sesi Siang</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $summary['by_session']['SIANG'] ?? 0 }}</p>
        </div>
    </div>
    
    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Antrian</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pengunjung</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pengikut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($queues as $queue)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-bold text-gray-900">{{ $queue->nomor_antrian }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $queue->nama_pengunjung }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $queue->schedule->tanggal->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold
                                    {{ $queue->schedule->sesi === 'PAGI' ? 'bg-orange-100 text-orange-800' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ $queue->schedule->sesi }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $queue->status_antrian === 'Disetujui' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $queue->status_antrian === 'Menunggu Dipanggil' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $queue->status_antrian === 'Dipanggil' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $queue->status_antrian === 'Selesai' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $queue->status_antrian }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $queue->followers->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium">Tidak ada data laporan</p>
                                <p class="text-sm text-gray-400 mt-1">Silakan sesuaikan filter tanggal</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
