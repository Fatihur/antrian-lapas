<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Atur Jadwal</h3>
                <p class="text-sm text-gray-500">Kelola sesi kunjungan, hari libur, dan pengaturan operasional</p>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <button wire:click="setTab('kelola-sesi')" 
                    class="flex-1 min-w-[140px] px-4 py-4 text-sm font-medium transition-all flex items-center justify-center gap-2 whitespace-nowrap
                    {{ $activeTab === 'kelola-sesi' ? 'bg-teal-50 text-teal-700 border-b-2 border-teal-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kelola Sesi
            </button>
            <button wire:click="setTab('hari-libur')" 
                    class="flex-1 min-w-[140px] px-4 py-4 text-sm font-medium transition-all flex items-center justify-center gap-2 whitespace-nowrap
                    {{ $activeTab === 'hari-libur' ? 'bg-teal-50 text-teal-700 border-b-2 border-teal-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                Hari Libur
            </button>
            <button wire:click="setTab('pengaturan')" 
                    class="flex-1 min-w-[140px] px-4 py-4 text-sm font-medium transition-all flex items-center justify-center gap-2 whitespace-nowrap
                    {{ $activeTab === 'pengaturan' ? 'bg-teal-50 text-teal-700 border-b-2 border-teal-500' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan Umum
            </button>
        </div>

        {{-- Tab Content: Kelola Sesi --}}
        @if($activeTab === 'kelola-sesi')
            <div class="p-6 space-y-6">
                {{-- Info Card --}}
                <div class="flex items-start gap-4 bg-blue-50 p-4 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Kelola Sesi Kunjungan</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Tambah, edit, atau hapus sesi kunjungan. Setiap sesi memiliki jam operasional dan kuota tersendiri. 
                            Pengunjung dapat memilih sesi yang tersedia saat mengambil antrian.
                        </p>
                    </div>
                </div>

                {{-- Add Session Button --}}
                <div class="flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-900">Daftar Sesi</h4>
                    <button wire:click="openCreateSessionModal" 
                            class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-semibold shadow-md transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Sesi Baru
                    </button>
                </div>

                {{-- Sessions Table --}}
                @if(count($sessions) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Urutan</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Sesi</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jam Operasional</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kuota</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($sessions as $index => $session)
                                        <tr class="hover:bg-gray-50 {{ !$session['is_active'] ? 'opacity-60' : '' }}">
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-700 rounded-full font-semibold text-sm">
                                                    {{ $session['urutan'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-gray-900">{{ $session['nama_sesi'] }}</span>
                                                @if($session['keterangan'])
                                                    <p class="text-xs text-gray-500 mt-1">{{ $session['keterangan'] }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">
                                                    {{ $session['kode_sesi'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-700">
                                                    <span class="font-medium">{{ $session['jam_buka'] }} - {{ $session['jam_tutup'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $session['kuota_sesi'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <button wire:click="toggleSessionStatus({{ $session['id'] }})" 
                                                        class="px-3 py-1.5 rounded-full text-xs font-bold transition-all
                                                        {{ $session['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                                    {{ $session['is_active'] ? '🟢 Aktif' : '🔴 Nonaktif' }}
                                                </button>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex gap-2">
                                                    <button wire:click="openEditSessionModal({{ $session['id'] }})" 
                                                            class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors"
                                                            title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deleteSession({{ $session['id'] }})" 
                                                            wire:confirm="Apakah Anda yakin ingin menghapus sesi ini?"
                                                            class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors"
                                                            title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada sesi kunjungan</p>
                        <p class="text-sm text-gray-400 mt-1">Klik "Tambah Sesi Baru" untuk membuat sesi pertama</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Tab Content: Hari Libur --}}
        @if($activeTab === 'hari-libur')
            <div class="p-6 space-y-6">
                {{-- Hari Libur Mingguan --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Hari Libur Mingguan
                    </h4>
                    <p class="text-sm text-gray-600">Pilih hari yang akan dijadikan libur rutin setiap minggu</p>
                    
                    <div class="flex flex-wrap gap-3">
                        @foreach($allHari as $hari)
                            <button wire:click="toggleHariLiburMingguan('{{ $hari }}')"
                                    class="px-5 py-3 rounded-xl font-medium transition-all flex items-center gap-2 border-2
                                    {{ in_array($hari, $hari_libur_mingguan) 
                                        ? 'bg-red-50 text-red-700 border-red-300' 
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-gray-400' }}">
                                @if(in_array($hari, $hari_libur_mingguan))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                @endif
                                {{ $hari }}
                            </button>
                        @endforeach
                    </div>

                    @if(count($hari_libur_mingguan) > 0)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-800">
                                <span class="font-semibold">Hari libur mingguan:</span> 
                                {{ implode(', ', $hari_libur_mingguan) }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Tanggal Libur Khusus --}}
                <div class="border-t border-gray-200 pt-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Tanggal Libur Khusus
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">Tambahkan tanggal libur khusus seperti hari raya atau cuti bersama</p>
                            </div>
                            <button wire:click="openAddHolidayModal" 
                                    class="px-5 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 font-semibold shadow-md transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="hidden sm:inline">Tambah Libur</span>
                                <span class="sm:hidden">Tambah</span>
                            </button>
                        </div>

                        {{-- Holiday List --}}
                        @if(count($tanggal_libur_khusus) > 0)
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Hari</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Keterangan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-24">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($tanggal_libur_khusus as $holiday)
                                                @php
                                                    $tanggal = is_array($holiday) ? $holiday['tanggal'] : $holiday;
                                                    $keterangan = is_array($holiday) ? ($holiday['keterangan'] ?? '') : '';
                                                @endphp
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3">
                                                        <span class="font-medium text-gray-900">
                                                            {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-gray-600">
                                                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->dayName }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-gray-600">{{ $keterangan ?: '-' }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <button wire:click="removeHoliday('{{ $tanggal }}')" 
                                                                class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors"
                                                                title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">Belum ada tanggal libur khusus</p>
                                <p class="text-sm text-gray-400 mt-1 mb-4">Klik tombol di atas untuk menambahkan</p>
                                <button wire:click="openAddHolidayModal" 
                                        class="px-5 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition-colors flex items-center gap-2 mx-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Libur
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="loadSettings" 
                            class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                        Reset
                    </button>
                    <button wire:click="saveHariLibur" 
                            class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        @endif

        {{-- Tab Content: Pengaturan Umum --}}
        @if($activeTab === 'pengaturan')
            <div class="p-6 space-y-6">
                <div class="flex items-start gap-4 bg-blue-50 p-4 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Pengaturan Umum</p>
                        <p class="text-sm text-blue-700 mt-1">
                            Atur status operasional sistem. Jika status "Tutup", pengunjung tidak dapat mengambil antrian.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-gray-700">Status Operasional</label>
                    <select wire:model="status_default" 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none bg-white">
                        <option value="buka">🟢 Buka - Menerima pendaftaran antrian</option>
                        <option value="tutup">🔴 Tutup - Tidak menerima pendaftaran</option>
                    </select>
                    <p class="text-sm text-gray-500">
                        Saat status "Tutup", pengunjung tidak akan bisa mengambil antrian meskipun ada sesi yang aktif.
                    </p>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="loadSettings" 
                            class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                        Reset
                    </button>
                    <button wire:click="saveHariLibur" 
                            class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Tambah/Edit Sesi --}}
    @if($showSessionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-teal-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $editSessionMode ? 'Edit Sesi' : 'Tambah Sesi Baru' }}</h4>
                    </div>
                </div>
                <form wire:submit.prevent="saveSession" class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Nama Sesi <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="sesi_nama" placeholder="Contoh: Sesi Pagi"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                            @error('sesi_nama') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Kode Sesi <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="sesi_kode" placeholder="Contoh: PAGI"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none uppercase"
                                   {{ $editSessionMode ? 'readonly' : '' }}>
                            @error('sesi_kode') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Jam Buka <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="sesi_jam_buka"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                            @error('sesi_jam_buka') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Jam Tutup <span class="text-red-500">*</span></label>
                            <input type="time" wire:model="sesi_jam_tutup"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                            @error('sesi_jam_tutup') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Kuota per Sesi <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="sesi_kuota" min="1" max="500"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                            @error('sesi_kuota') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Urutan <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="sesi_urutan" min="1"
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                            @error('sesi_urutan') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
                        <input type="text" wire:model="sesi_keterangan" placeholder="Keterangan tambahan tentang sesi ini"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none">
                        @error('sesi_keterangan') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeSessionModal"
                                class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium shadow-md transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $editSessionMode ? 'Simpan Perubahan' : 'Tambah Sesi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal: Tambah Libur --}}
    @if($showAddHolidayModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full shadow-2xl">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900">Tambah Tanggal Libur</h4>
                    </div>
                </div>
                <form wire:submit.prevent="addHoliday" class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Tanggal Libur <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="tanggal_libur_baru"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 focus:outline-none">
                        @error('tanggal_libur_baru') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
                        <input type="text" wire:model="keterangan_libur" placeholder="Contoh: Hari Raya Idul Fitri, Cuti Bersama, dll"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500 focus:outline-none">
                        @error('keterangan_libur') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeAddHolidayModal"
                                class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium shadow-md transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
