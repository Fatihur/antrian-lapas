<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Cek Status Antrian</h2>
            <p class="text-gray-600 text-center mb-8">Masukkan NIK untuk melihat status antrian Anda</p>
            
            <form wire:submit.prevent="search" class="mb-8">
                <div class="flex gap-4">
                    <input type="text" wire:model="nik_pendaftar" placeholder="Masukkan NIK"
                           class="flex-1 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                    <button type="submit" 
                            class="px-6 py-2 bg-teal-700 text-white rounded-lg hover:bg-teal-800">
                        Cek Status
                    </button>
                </div>
                @error('nik_pendaftar') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </form>
            
            @if($searched)
                @if(count($queues) > 0)
                    <div class="space-y-4">
                        @foreach($queues as $queue)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-lg font-bold text-teal-700">{{ $queue->nomor_antrian }}</p>
                                        <p class="text-sm text-gray-600">Kode Booking: {{ $queue->kode_booking }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $queue->schedule->tanggal->format('d F Y') }} - {{ $queue->schedule->sesi }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $queue->status_antrian === 'Menunggu Verifikasi' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $queue->status_antrian === 'Disetujui' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $queue->status_antrian === 'Dipanggil' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $queue->status_antrian }}
                                    </span>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $queue->nama_pengunjung }}</p>
                                        <p class="text-sm text-gray-600">{{ $queue->followers->count() }} pengikut</p>
                                    </div>
                                    <button wire:click="downloadPdf({{ $queue->id }})" 
                                            class="text-teal-700 hover:text-teal-800 text-sm font-medium">
                                        Download PDF
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-600">Tidak ditemukan antrian aktif dengan NIK tersebut</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
