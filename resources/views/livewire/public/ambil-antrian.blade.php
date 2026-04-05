<div class="min-h-screen bg-gradient-to-br from-[#0f2744] to-[#1e3a5f] py-4 sm:py-6 md:py-8 px-3 sm:px-4">
    <div class="w-full max-w-4xl mx-auto">
        @if($submitted)
            {{-- Success View - Mobile Optimized --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 text-center border border-[#c9a227]/30">
                <div class="w-16 h-16 sm:w-20 md:w-24 sm:h-20 md:h-24 bg-gradient-to-br from-[#c9a227] to-[#a88420] rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                    <svg class="w-8 h-8 sm:w-10 md:w-12 sm:h-10 md:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e3a5f] mb-2">Pendaftaran Berhasil!</h2>
                <p class="text-gray-600 mb-4 sm:mb-6 md:mb-8 text-base sm:text-lg">Antrian kunjungan Anda telah terdaftar.</p>
                
                <div class="bg-gradient-to-r from-[#1e3a5f]/5 to-[#1e3a5f]/10 border-2 border-[#1e3a5f]/30 rounded-lg sm:rounded-xl p-4 sm:p-6 md:p-8 mb-4 sm:mb-6 md:mb-8">
                    <p class="text-xs sm:text-sm text-gray-600 mb-2 uppercase tracking-wide font-semibold">Nomor Antrian</p>
                    <p class="text-3xl sm:text-4xl md:text-5xl font-black text-[#1e3a5f] tracking-wider">{{ $queueData->nomor_antrian }}</p>
                    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-[#1e3a5f]/20">
                        <p class="text-xs sm:text-sm text-gray-600">Kode Booking</p>
                        <p class="text-xl sm:text-2xl font-bold text-[#c9a227] font-mono">{{ $queueData->kode_booking }}</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="{{ route('download-pdf', $queueData->id) }}" 
                       class="inline-flex items-center justify-center px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-[#1e3a5f] text-white rounded-lg sm:rounded-xl hover:bg-[#152a45] shadow-lg transition-all transform hover:scale-105 font-semibold text-base sm:text-lg">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="truncate">Download PDF</span>
                    </a>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center justify-center px-4 sm:px-6 md:px-8 py-3 sm:py-4 border-2 border-[#c9a227] text-[#1e3a5f] rounded-lg sm:rounded-xl hover:bg-[#c9a227]/10 transition-all font-semibold text-base sm:text-lg">
                        <span class="truncate">Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        @else
            {{-- Form - Mobile First --}}
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden border border-[#1e3a5f]/20">
                <div class="p-4 sm:p-6 md:p-8 border-b border-gray-200 bg-gradient-to-r from-[#1e3a5f] to-[#152a45] text-white">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-1 sm:mb-2">Ambil Antrian Kunjungan</h2>
                    <p class="text-[#c9a227] text-sm sm:text-base">Langkah {{ $currentStep }} dari {{ $totalSteps }}</p>
                </div>
                
                {{-- Progress Steps - Mobile Optimized --}}
                <div class="px-3 sm:px-6 py-4 sm:py-6 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-center">
                        @foreach([1, 2, 3] as $step)
                            <div class="flex items-center">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-md
                                    {{ $currentStep == $step ? 'bg-[#1e3a5f] text-white ring-2 sm:ring-4 ring-[#1e3a5f]/20' : '' }}
                                    {{ $currentStep > $step ? 'bg-[#c9a227] text-white' : '' }}
                                    {{ $currentStep < $step ? 'bg-gray-300 text-gray-600' : '' }}">
                                    @if($currentStep > $step)
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        {{ $step }}
                                    @endif
                                </div>
                                @if($step < 3)
                                    <div class="w-8 sm:w-16 md:w-32 h-1.5 sm:h-2 mx-1 sm:mx-2 rounded-full {{ $currentStep > $step ? 'bg-[#c9a227]' : 'bg-gray-300' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-center mt-2 sm:mt-3 text-xs sm:text-sm text-gray-600">
                        <span class="w-1/3 text-center px-1 truncate">Data Pengunjung</span>
                        <span class="w-1/3 text-center px-1 truncate">Data Pengikut</span>
                        <span class="w-1/3 text-center px-1 truncate">Jadwal</span>
                    </div>
                </div>
                
                <form wire:submit.prevent="{{ $currentStep === 3 ? 'submit' : 'nextStep' }}" class="p-3 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
                    @error('submit')
                        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 text-red-700 font-medium">{{ $message }}</div>
                    @enderror
                    
                    @if($currentStep === 1)
                        {{-- Step 1: Data Pengunjung --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-8 h-8 bg-[#1e3a5f]/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-[#1e3a5f]">Data Pengunjung Utama</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="form-label">NIK / Nomor Identitas <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nik_pendaftar" 
                                           placeholder="Masukkan 16 digit NIK"
                                           maxlength="25"
                                           class="form-input @error('nik_pendaftar') error @enderror"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('nik_pendaftar') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="form-label">Jenis Identitas <span class="text-red-500">*</span></label>
                                    <select wire:model="jenis_identitas" 
                                            class="form-input @error('jenis_identitas') error @enderror bg-white">
                                        <option value="">-- Pilih Jenis Identitas --</option>
                                        <option value="KTP">KTP</option>
                                        <option value="SIM">SIM</option>
                                        <option value="Paspor">Paspor</option>
                                        <option value="KK">Kartu Keluarga</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('jenis_identitas') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2 space-y-2">
                                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nama_pengunjung" 
                                           placeholder="Masukkan nama lengkap sesuai identitas"
                                           class="form-input @error('nama_pengunjung') error @enderror">
                                    @error('nama_pengunjung') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="form-label">Nomor HP / WhatsApp <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 text-gray-500 font-semibold">+62</span>
                                        <input type="tel" wire:model="no_hp" 
                                               placeholder="8xxxxxxxxxx"
                                               class="form-input @error('no_hp') error @enderror pl-14"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 15)"
                                               pattern="[0-9]{10,15}"
                                               title="Masukkan 10-15 digit nomor HP">
                                    </div>
                                    <p class="text-xs text-gray-500">Contoh: 81234567890</p>
                                    @error('no_hp') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="form-label">Hubungan dengan WBP <span class="text-red-500">*</span></label>
                                    <select wire:model="hubungan_wbp" 
                                            class="form-input @error('hubungan_wbp') error @enderror bg-white">
                                        <option value="">-- Pilih Hubungan --</option>
                                        <option value="Suami/Istri">Suami/Istri</option>
                                        <option value="Anak">Anak</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Saudara">Saudara</option>
                                        <option value="Keluarga Lain">Keluarga Lain</option>
                                        <option value="Teman">Teman</option>
                                        <option value="Pengacara">Pengacara</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('hubungan_wbp') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2 space-y-2">
                                    <label class="form-label">Nama WBP (Warga Binaan Pemasyarakatan) <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nama_wbp" 
                                           placeholder="Masukkan nama WBP yang akan dikunjungi"
                                           class="form-input @error('nama_wbp') error @enderror">
                                    @error('nama_wbp') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2 space-y-2">
                                    <label class="form-label">Foto Identitas <span class="text-red-500">*</span></label>
                                    <div class="border-2 border-dashed border-[#1e3a5f]/30 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <input type="file" wire:model="foto_identitas" accept="image/jpeg,image/jpg,image/png"
                                               class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-2 file:border-[#1e3a5f] file:text-sm file:font-semibold file:bg-[#1e3a5f]/10 file:text-[#1e3a5f] hover:file:bg-[#1e3a5f]/20">
                                        <p class="text-xs text-gray-500 mt-2">Format: JPG, JPEG, PNG. Maksimal 2MB</p>
                                    </div>
                                    <div wire:loading wire:target="foto_identitas" class="text-sm text-[#1e3a5f] font-medium flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mengupload foto...
                                    </div>
                                    @error('foto_identitas') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($currentStep === 2)
                        {{-- Step 2: Data Pengikut --}}
                        <div class="space-y-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-[#1e3a5f]/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-[#1e3a5f]">Data Pengikut (Maksimal 10 orang)</h3>
                                </div>
                                @if(count($followers) < 10)
                                    <button type="button" wire:click="addFollower" 
                                            class="px-4 py-2 bg-[#1e3a5f]/10 text-[#1e3a5f] rounded-lg hover:bg-[#1e3a5f]/20 font-semibold transition-colors flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Pengikut
                                    </button>
                                @endif
                            </div>
                            
                            @forelse($followers as $index => $follower)
                                <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-5 space-y-4 relative">
                                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                        <span class="font-bold text-[#1e3a5f] flex items-center gap-2">
                                            <span class="w-6 h-6 bg-[#1e3a5f] text-white rounded-full flex items-center justify-center text-sm">{{ $index + 1 }}</span>
                                            Pengikut
                                        </span>
                                        <button type="button" wire:click="removeFollower({{ $index }})" 
                                                class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1 px-3 py-1 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="followers.{{ $index }}.nama_pengikut" 
                                                   placeholder="Nama lengkap"
                                                   class="form-input @error('followers.'.$index.'.nama_pengikut') error @enderror">
                                            @error('followers.'.$index.'.nama_pengikut') <span class="form-error">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-gray-700">No. Identitas <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="followers.{{ $index }}.nomor_identitas_pengikut" 
                                                   placeholder="Nomor KTP/SIM"
                                                   class="form-input @error('followers.'.$index.'.nomor_identitas_pengikut') error @enderror"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            @error('followers.'.$index.'.nomor_identitas_pengikut') <span class="form-error">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                                            <select wire:model="followers.{{ $index }}.jenis_kelamin_pengikut"
                                                    class="form-input @error('followers.'.$index.'.jenis_kelamin_pengikut') error @enderror bg-white">
                                                <option value="">-- Pilih --</option>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                            @error('followers.'.$index.'.jenis_kelamin_pengikut') <span class="form-error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-gray-600 mb-4">Belum ada pengikut yang didaftarkan</p>
                                    <button type="button" wire:click="addFollower" 
                                            class="px-6 py-3 bg-[#1e3a5f] text-white rounded-lg hover:bg-[#152a45] font-semibold transition-colors">
                                        + Tambah Pengikut
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    @endif
                    
                    @if($currentStep === 3)
                        {{-- Step 3: Jadwal --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-8 h-8 bg-[#1e3a5f]/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-[#1e3a5f]">Pilih Jadwal Kunjungan</h3>
                            </div>
                            
                            {{-- Pilih Tanggal --}}
                            <div class="space-y-2">
                                <label class="form-label">1. Pilih Tanggal Kunjungan <span class="text-red-500">*</span></label>
                                <p class="text-sm text-gray-600 mb-3">Pilih tanggal yang tersedia dari daftar di bawah.</p>
                                
                                @if(count($availableSchedules) > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                        @foreach($availableSchedules as $dateKey => $schedule)
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model.live="tanggal_kunjungan" value="{{ $dateKey }}" class="hidden peer">
                                                <div class="p-3 rounded-lg border-2 transition-all peer-checked:border-[#1e3a5f] peer-checked:bg-[#1e3a5f]/5 peer-checked:shadow-md hover:border-gray-400 {{ $tanggal_kunjungan === $dateKey ? 'border-[#1e3a5f] bg-[#1e3a5f]/5 shadow-md' : 'border-gray-200 bg-white' }}">
                                                    <p class="text-xs text-gray-500 font-medium">{{ $schedule['hari'] }}</p>
                                                    <p class="font-bold text-[#1e3a5f]">{{ \Carbon\Carbon::parse($dateKey)->format('d M Y') }}</p>
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        <p class="text-xs {{ $schedule['total_sisa_kuota'] < 10 ? 'text-red-600 font-bold' : 'text-[#c9a227]' }}">
                                                            {{ $schedule['total_sisa_kuota'] }} kuota tersisa
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-5">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <p class="text-yellow-800 font-medium">Tidak ada tanggal kunjungan yang tersedia saat ini</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @error('tanggal_kunjungan') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Pilih Sesi (muncul setelah tanggal dipilih) --}}
                            @if($tanggal_kunjungan && isset($availableSchedules[$tanggal_kunjungan]))
                                @php
                                    $selectedSchedule = $availableSchedules[$tanggal_kunjungan];
                                @endphp
                                <div class="space-y-3 pt-4 border-t border-gray-200">
                                    <label class="form-label">2. Pilih Sesi Kunjungan <span class="text-red-500">*</span></label>
                                    <p class="text-sm text-gray-600 mb-3">Pilih sesi yang masih memiliki kuota tersedia.</p>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($selectedSchedule['sesi'] as $sesi)
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="sesi_kunjungan" value="{{ $sesi['kode'] }}" class="hidden peer">
                                                <div class="p-4 rounded-lg border-2 transition-all peer-checked:border-[#1e3a5f] peer-checked:bg-[#1e3a5f]/5 peer-checked:shadow-md hover:border-gray-400 {{ $sesi_kunjungan === $sesi['kode'] ? 'border-[#1e3a5f] bg-[#1e3a5f]/5 shadow-md' : 'border-gray-200 bg-white' }}">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="font-bold text-[#1e3a5f]">{{ $sesi['nama'] }}</span>
                                                        <span class="px-2 py-1 bg-[#1e3a5f]/10 text-[#1e3a5f] rounded text-xs font-semibold">
                                                            {{ $sesi['sisa_kuota'] }} tersisa
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mb-1">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $sesi['jam'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">Kuota: {{ $sesi['kuota_maksimal'] }} orang/sesi</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    @error('sesi_kunjungan') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Summary --}}
                                @if($tanggal_kunjungan && $sesi_kunjungan)
                                    @php
                                        $selectedSesi = collect($selectedSchedule['sesi'])->firstWhere('kode', $sesi_kunjungan);
                                    @endphp
                                    @if($selectedSesi)
                                        <div class="bg-[#c9a227]/10 border-l-4 border-[#c9a227] rounded-lg p-5 mt-4">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="w-6 h-6 text-[#c9a227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-[#1e3a5f] font-bold text-lg">Jadwal Tersedia!</p>
                                            </div>
                                            <div class="space-y-1 text-sm">
                                                <p class="text-[#1e3a5f]">
                                                    <span class="font-semibold">Tanggal:</span> 
                                                    {{ \Carbon\Carbon::parse($tanggal_kunjungan)->format('d F Y') }} 
                                                    ({{ \Carbon\Carbon::parse($tanggal_kunjungan)->locale('id')->dayName }})
                                                </p>
                                                <p class="text-[#1e3a5f]">
                                                    <span class="font-semibold">Sesi:</span> {{ $selectedSesi['nama'] }}
                                                </p>
                                                <p class="text-[#1e3a5f]">
                                                    <span class="font-semibold">Jam:</span> {{ $selectedSesi['jam'] }}
                                                </p>
                                                <p class="text-[#1e3a5f]">
                                                    <span class="font-semibold">Sisa Kuota:</span> 
                                                    <span class="font-bold text-lg">{{ $selectedSesi['sisa_kuota'] }}</span> orang
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    @endif
                    
                    {{-- Buttons - Mobile Optimized --}}
                    <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 sm:gap-4 pt-6 sm:pt-8 border-t-2 border-gray-200">
                        @if($currentStep > 1)
                            <button type="button" wire:click="previousStep" 
                                    class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-3 sm:py-4 border-2 border-gray-300 text-gray-700 rounded-lg sm:rounded-xl hover:bg-gray-50 font-semibold transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="truncate">Kembali</span>
                            </button>
                        @else
                            <a href="{{ route('home') }}" 
                               class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-3 sm:py-4 border-2 border-[#c9a227] text-[#1e3a5f] rounded-lg sm:rounded-xl hover:bg-[#c9a227]/10 font-semibold transition-colors text-center">
                                <span class="truncate">Batal</span>
                            </a>
                        @endif
                        
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-gradient-to-r from-[#1e3a5f] to-[#152a45] text-white rounded-lg sm:rounded-xl hover:from-[#152a45] hover:to-[#0f2744] font-semibold transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2 min-h-[48px]">
                            @if($currentStep === 3)
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="truncate">Daftar Sekarang</span>
                            @else
                                <span class="truncate">Lanjutkan</span>
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>