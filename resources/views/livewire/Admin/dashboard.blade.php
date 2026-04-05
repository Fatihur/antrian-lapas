<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Total Antrian Hari Ini</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $this->getTotalQueuesToday() }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Disetujui</p>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $statusCounts['Disetujui'] ?? 0 }}</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Dipanggil</p>
            </div>
            <p class="text-3xl font-bold text-blue-600">{{ $statusCounts['Dipanggil'] ?? 0 }}</p>
        </div>
    </div>
    
    {{-- Current Called Queue --}}
    @if($currentCalledQueue)
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="font-semibold">ANTRIAN SEDANG DIPANGGIL</p>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-5xl font-black tracking-wider">{{ $currentCalledQueue->nomor_antrian }}</p>
                    <p class="text-blue-100 mt-1">{{ $currentCalledQueue->nama_pengunjung }}</p>
                </div>
                <a href="{{ route('admin.queues.call') }}" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-bold hover:bg-blue-50 transition-colors">
                    Lihat Detail
                </a>
            </div>
        </div>
    @endif
    
    {{-- Recent Queues --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h4 class="text-lg font-bold text-gray-900">Antrian Terbaru Hari Ini</h4>
            </div>
            <a href="{{ route('admin.queues.index') }}" class="text-teal-600 hover:text-teal-700 font-medium text-sm">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Antrian</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pengunjung</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sesi</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($todayQueues as $queue)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-bold text-gray-900">{{ $queue->nomor_antrian }}</td>
                            <td class="px-5 py-3 text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $queue->nama_pengunjung }}
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $queue->schedule->sesi }}</td>
                            <td class="px-5 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $queue->status_antrian === 'Disetujui' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $queue->status_antrian === 'Menunggu Dipanggil' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $queue->status_antrian === 'Dipanggil' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $queue->status_antrian === 'Selesai' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $queue->status_antrian }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="font-medium">Belum ada antrian hari ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
