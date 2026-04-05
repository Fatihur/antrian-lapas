<div class="min-h-screen bg-gradient-to-br from-[#0f2744] to-[#1e3a5f] py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-xl p-8 border border-[#1e3a5f]/20">
            <h2 class="text-2xl font-bold text-[#1e3a5f] text-center mb-2">Cek Status Antrian</h2>
            <p class="text-gray-600 text-center mb-8">Masukkan NIK untuk melihat status antrian Anda</p>
            
            <form wire:submit.prevent="search" class="mb-8">
                <div class="flex gap-4">
                    <input type="text" wire:model="nik_pendaftar" placeholder="Masukkan NIK"
                           class="flex-1 rounded-lg border-gray-300 focus:border-[#1e3a5f] focus:ring-[#1e3a5f]">
                    <button type="submit" 
                            class="px-6 py-2 bg-[#1e3a5f] text-white rounded-lg hover:bg-[#152a45] transition-colors">
                        Cek Status
                    </button>
                </div>
                @error('nik_pendaftar') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </form>
            
            @if($searched)
                @if(count($queues) > 0)
                    <div class="space-y-4">
                        @foreach($queues as $queue)
                            <div class="border-2 border-[#1e3a5f]/20 rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-lg font-bold text-[#1e3a5f]">{{ $queue->nomor_antrian }}</p>
                                        <p class="text-sm text-gray-600">Kode Booking: <span class="font-mono text-[#c9a227]">{{ $queue->kode_booking }}</span></p>
                                        <p class="text-sm text-gray-600">
                                            {{ $queue->tanggal_kunjungan->format('d F Y') }} - {{ $queue->session->nama }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $queue->status_antrian === 'Menunggu Verifikasi' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $queue->status_antrian === 'Disetujui' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $queue->status_antrian === 'Dipanggil' ? 'bg-[#1e3a5f] text-white' : '' }}">
                                        {{ $queue->status_antrian }}
                                    </span>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-[#1e3a5f]">{{ $queue->nama_pengunjung }}</p>
                                        <p class="text-sm text-gray-600">{{ $queue->followers->count() }} pengikut</p>
                                    </div>
                                    <button wire:click="downloadPdf({{ $queue->id }})" 
                                            class="text-[#c9a227] hover:text-[#a88420] text-sm font-medium">
                                        Download PDF
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-[#1e3a5f]/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-600">Tidak ditemukan antrian aktif dengan NIK tersebut</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
