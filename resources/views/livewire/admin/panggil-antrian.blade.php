<div class="space-y-6" x-data="queueCalling()" x-init="initAudio()">
    {{-- CSS Animation for Toast Progress --}}
    <style>
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>

    {{-- Toast Notification - Compact (top-right) --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         class="fixed top-4 right-4 z-50 max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-3 rounded-lg shadow-2xl flex items-center gap-3 border border-blue-400">
            <div class="flex-shrink-0 animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm truncate" x-text="toastMessage"></p>
            </div>
            {{-- Progress bar --}}
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-400/30 rounded-b-lg overflow-hidden">
                <div class="h-full bg-white/80 animate-[shrink_3s_linear_forwards]"></div>
            </div>
        </div>
    </div>

    {{-- Debug Info --}}
    <div x-show="debugMode" class="bg-gray-100 border border-gray-300 rounded-lg p-3 font-mono text-xs">
        <p><strong>Audio Status:</strong> <span x-text="audioFragmentsAvailable ? 'Local Files Ready' : 'No Audio Files'"></span></p>
        <p><strong>Fragments Count:</strong> <span x-text="Object.keys(audioFragments).length"></span></p>
        <p><strong>Current URL:</strong> <span x-text="window.location.href"></span></p>
        <button @click="testAudio()" class="mt-2 px-2 py-1 bg-blue-500 text-white rounded">Test Audio</button>
    </div>

    {{-- Audio Mode Indicator - Local Only --}}
    <div x-show="audioFragmentsAvailable" 
         class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-semibold text-sm">🎵 Audio lokal aktif ({{ count($audioFragments) }} files)</span>
        </div>
        <button @click="debugMode = !debugMode" class="text-xs text-gray-500 underline">Debug</button>
    </div>
    
    <div x-show="!audioFragmentsAvailable" 
         class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-center justify-between">
        <div class="flex items-center gap-2 text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="font-semibold text-sm">❌ Audio tidak tersedia - Jalankan: php artisan audio:download</span>
        </div>
    </div>

    {{-- Header Controls --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-4 items-center">
                {{-- Date Filter --}}
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input type="date" wire:model="dateFilter" wire:change="loadQueues" 
                           class="bg-transparent border-none focus:ring-0 font-semibold text-gray-700">
                </div>
                
                {{-- Session Badge - Auto Detected --}}
                <div class="flex items-center gap-2 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold text-blue-700">
                        @if($currentSession === 'PAGI')
                            🌅 Pagi (08:00 - 12:00)
                        @else
                            ☀️ Siang (13:00 - 16:00)
                        @endif
                    </span>
                    <span class="text-xs text-blue-500">(Otomatis)</span>
                </div>
                
                {{-- Counter Selection --}}
                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <select wire:model="counter" 
                            class="bg-transparent border-none focus:ring-0 font-bold text-blue-600 min-w-[100px]">
                        @foreach($availableCounters as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Sound Toggle --}}
            <button @click="$wire.toggleSound()" 
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 transition-all font-semibold"
                    :class="$wire.soundEnabled ? 'bg-green-100 border-green-400 text-green-700' : 'bg-gray-100 border-gray-300 text-gray-500'">
                <svg x-show="$wire.soundEnabled" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
                <svg x-show="!$wire.soundEnabled" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                </svg>
                <span x-text="$wire.soundEnabled ? '🔊 Suara Aktif' : '🔇 Suara Mati'"></span>
            </button>
        </div>
    </div>

    {{-- Row 1: Panel Antrian Aktif (Full Width) --}}
    <div class="lg:col-span-12">
        @if($activeQueue)
            @php
                $simpleActiveNumber = explode('-', $activeQueue->nomor_antrian)[0];
            @endphp
            <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 rounded-2xl p-6 lg:p-10 text-center shadow-2xl border-4 border-blue-400 relative overflow-hidden"
                 :class="isFlashing ? 'animate-pulse' : ''">
                {{-- Background Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-60 h-60 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
                </div>
                
                {{-- Loket Badge --}}
                <div class="relative mb-4 lg:mb-6">
                    <span class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-full text-lg font-bold tracking-wider border-2 border-white/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $counter }}
                    </span>
                </div>
                
                {{-- Nomor Antrian Display --}}
                <div class="relative mb-4 lg:mb-6">
                    <p class="text-white/80 text-sm uppercase tracking-[0.3em] mb-3 font-semibold">Nomor Antrian</p>
                    <div class="bg-white rounded-2xl p-6 lg:p-10 shadow-inner inline-block min-w-[300px]">
                        <p class="text-8xl lg:text-[10rem] font-black text-blue-700 tracking-wider leading-none"
                           :class="isFlashing ? 'animate-bounce text-yellow-600' : ''">
                            {{ $simpleActiveNumber }}
                        </p>
                    </div>
                </div>
                
                {{-- Info Pengunjung --}}
                <div class="relative bg-white/10 backdrop-blur-sm rounded-xl p-4 lg:p-6 mb-4 lg:mb-6 border border-white/20 max-w-2xl mx-auto">
                    <p class="text-white font-bold text-xl lg:text-2xl mb-2">{{ $activeQueue->nama_pengunjung }}</p>
                    <div class="flex justify-center gap-6 text-white/90 text-sm flex-wrap">
                        <span class="flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $activeQueue->followers->count() + 1 }} Orang
                        </span>
                        <span class="flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $activeQueue->nama_wbp }}
                        </span>
                    </div>
                </div>
                
                {{-- Recall Badge --}}
                @if($recallCount > 0)
                    <div class="relative mb-4 lg:mb-6">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400 text-yellow-900 rounded-full text-sm font-bold shadow-lg animate-pulse">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            PANGGILAN ULANG KE-{{ $recallCount }}
                        </span>
                    </div>
                @endif
                
                {{-- Control Buttons --}}
                <div class="relative grid grid-cols-2 lg:grid-cols-4 gap-3 max-w-4xl mx-auto">
                    <button wire:click="recallQueue()" 
                            wire:loading.attr="disabled"
                            class="py-4 bg-yellow-400 hover:bg-yellow-300 text-yellow-900 rounded-xl font-bold text-base lg:text-lg transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        PANGGIL ULANG
                    </button>
                    
                    <button wire:click="completeQueue()" 
                            wire:loading.attr="disabled"
                            class="py-4 bg-green-500 hover:bg-green-400 text-white rounded-xl font-bold text-base lg:text-lg transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        SELESAI
                    </button>
                    
                    <button wire:click="skipQueue()" 
                            wire:loading.attr="disabled"
                            class="py-4 bg-orange-500 hover:bg-orange-400 text-white rounded-xl font-bold text-base lg:text-lg transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                        LEWATI
                    </button>
                    
                    <button wire:click="callNext()" 
                            wire:loading.attr="disabled"
                            class="py-4 bg-blue-500 hover:bg-blue-400 text-white rounded-xl font-bold text-base lg:text-lg transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        BERIKUTNYA
                    </button>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-gray-100 rounded-2xl p-8 lg:p-12 text-center shadow-inner border-2 border-dashed border-gray-300">
                <div class="w-24 h-24 lg:w-32 lg:h-32 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4 lg:mb-6">
                    <svg class="w-12 h-12 lg:w-16 lg:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg lg:text-xl font-bold mb-2">Tidak ada antrian aktif</p>
                <p class="text-gray-500 text-sm lg:text-base mb-4 lg:mb-6">Pilih antrian dari daftar untuk memanggil</p>
                
                @if($waitingQueues->count() > 0)
                    <button wire:click="callNext()" 
                            wire:loading.attr="disabled"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-base transition-all flex items-center justify-center gap-2 mx-auto shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Panggil Berikutnya
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Row 2: Daftar Menunggu & Riwayat --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:col-span-12">
        {{-- Left: Daftar Antrian Menunggu (7 cols) --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Antrian Menunggu</h4>
                                <p class="text-sm text-gray-500">{{ $waitingQueues->count() }} orang</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3 space-y-2 max-h-[600px] overflow-y-auto">
                    @forelse($waitingQueues as $queue)
                        @php
                            $simpleNumber = explode('-', $queue->nomor_antrian)[0];
                        @endphp
                        <div class="flex justify-between items-center p-3 bg-gray-50 border-2 rounded-xl hover:border-blue-400 hover:shadow-md transition-all cursor-pointer {{ $activeQueue && $activeQueue->id === $queue->id ? 'ring-2 ring-blue-500 bg-blue-50 border-blue-500' : 'border-gray-200' }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-lg flex items-center justify-center font-bold text-lg shadow">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-900 text-2xl">{{ $simpleNumber }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $queue->nama_pengunjung }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-12 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $queue->followers->count() + 1 }} orang
                                    </span>
                                    <span class="px-2 py-0.5 bg-gray-200 rounded-full text-xs">{{ $queue->hubungan_wbp }}</span>
                                </div>
                            </div>
                            @if(!$activeQueue)
                                <button wire:click="callQueue({{ $queue->id }})" 
                                        wire:loading.attr="disabled"
                                        class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-all flex items-center gap-1 shadow-md hover:shadow-lg whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    Panggil
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium">Tidak ada antrian</p>
                            <p class="text-sm text-gray-500 mt-1">Pilih tanggal lain</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Audio dengan Relative Path --}}
    <script>
        function queueCalling() {
            return {
                showFlash: false,
                flashMessage: '',
                flashQueueNumber: '',
                flashCounter: '',
                isFlashing: false,
                isPlayingAudio: false,
                showToast: false,
                toastMessage: '',
                audioFragmentsAvailable: false,
                audioContext: null,
                audioBuffers: {},
                isLoadingAudio: false,
                audioFragments: @json($audioFragments),
                basePath: '{{ url('') }}',
                
                initAudio() {
                    this.checkAudioFragments();
                    
                    // Initialize Web Audio API for gapless playback
                    if (this.audioFragmentsAvailable) {
                        this.initWebAudio();
                    }
                    
                    window.addEventListener('queue-called', (e) => {
                        this.handleQueueCall(e.detail[0]);
                    });
                    
                    window.addEventListener('queue-recalled', (e) => {
                        this.handleQueueRecall(e.detail[0]);
                    });
                },
                
                initWebAudio() {
                    try {
                        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        console.log('🔊 Web Audio API initialized');
                    } catch (e) {
                        console.error('❌ Web Audio API not supported:', e);
                    }
                },
                
                checkAudioFragments() {
                    const hasLocalFiles = Object.keys(this.audioFragments).length > 10;
                    this.audioFragmentsAvailable = hasLocalFiles;
                    
                    if (!hasLocalFiles) {
                        console.error('❌ Audio files not available. Run: php artisan audio:download');
                    } else {
                        console.log('✅ Audio fragments ready:', Object.keys(this.audioFragments).length, 'files');
                    }
                },
                
                // Load single audio file into buffer
                async loadAudioBuffer(url) {
                    if (this.audioBuffers[url]) {
                        return this.audioBuffers[url];
                    }
                    
                    try {
                        const response = await fetch(url);
                        const arrayBuffer = await response.arrayBuffer();
                        const audioBuffer = await this.audioContext.decodeAudioData(arrayBuffer);
                        this.audioBuffers[url] = audioBuffer;
                        return audioBuffer;
                    } catch (e) {
                        console.error('Failed to load audio:', url, e);
                        return null;
                    }
                },
                
                // Play all buffers in sequence with ZERO gap
                async playBuffersSequentially(urls) {
                    if (!this.audioContext) {
                        console.error('AudioContext not available');
                        this.isPlayingAudio = false;
                        return;
                    }
                    
                    // Resume audio context if suspended (browser policy)
                    if (this.audioContext.state === 'suspended') {
                        await this.audioContext.resume();
                    }
                    
                    // Load all buffers first
                    const buffers = [];
                    for (const url of urls) {
                        const buffer = await this.loadAudioBuffer(url);
                        if (buffer) buffers.push(buffer);
                    }
                    
                    if (buffers.length === 0) {
                        console.error('No audio buffers loaded');
                        this.isPlayingAudio = false;
                        return;
                    }
                    
                    let currentTime = this.audioContext.currentTime;
                    
                    // Schedule all buffers at exact times
                    buffers.forEach((buffer, index) => {
                        const source = this.audioContext.createBufferSource();
                        source.buffer = buffer;
                        source.connect(this.audioContext.destination);
                        
                        // Schedule at current time (first) or immediately after previous ends
                        if (index === 0) {
                            source.start(currentTime);
                        } else {
                            source.start(currentTime);
                        }
                        
                        // Update current time for next buffer (exactly when this one ends)
                        currentTime += buffer.duration;
                    });
                    
                    // Set flag to false when all audio finishes
                    const totalDuration = buffers.reduce((sum, b) => sum + b.duration, 0);
                    setTimeout(() => {
                        this.isPlayingAudio = false;
                    }, totalDuration * 1000);
                    
                    console.log('🎵 Playing', buffers.length, 'buffers, total duration:', totalDuration.toFixed(2), 's');
                },
                
                handleQueueCall(data) {
                    this.flashQueueNumber = data.queueNumber;
                    this.flashCounter = data.counter;
                    this.isFlashing = true;
                    
                    // Show toast notification
                    this.toastMessage = `🔊 Memanggil ${data.queueNumber} ke ${data.counter}`;
                    this.showToast = true;
                    
                    // Auto hide toast after 3 seconds
                    setTimeout(() => { this.showToast = false; }, 3000);
                    
                    // Play audio independently - not tied to toast
                    if (data.soundEnabled) {
                        this.playCallAudio(data.queueNumber, data.counter, false, 0);
                    }
                    
                    // Stop flashing after 6 seconds
                    setTimeout(() => { this.isFlashing = false; }, 6000);
                },
                
                handleQueueRecall(data) {
                    this.flashQueueNumber = data.queueNumber;
                    this.flashCounter = data.counter;
                    this.isFlashing = true;
                    
                    // Show toast notification
                    this.toastMessage = `🔊 Panggilan ulang ${data.queueNumber} ke ${data.counter}`;
                    this.showToast = true;
                    
                    // Auto hide toast after 3 seconds
                    setTimeout(() => { this.showToast = false; }, 3000);
                    
                    // Play audio independently - not tied to toast
                    if (data.soundEnabled) {
                        this.playCallAudio(data.queueNumber, data.counter, true, data.recallCount);
                    }
                    
                    // Stop flashing after 6 seconds
                    setTimeout(() => { this.isFlashing = false; }, 6000);
                },
                
                playCallAudio(queueNumber, counter, isRecall, recallCount) {
                    if (this.audioFragmentsAvailable) {
                        this.playLocalAudio(queueNumber, counter, isRecall, recallCount);
                    } else {
                        console.error('Audio not available - visual only');
                        this.isPlayingAudio = false;
                    }
                },
                
                playLocalAudio(queueNumber, counter, isRecall, recallCount) {
                    if (!this.audioFragmentsAvailable) {
                        console.error('Audio fragments not available');
                        return;
                    }
                    
                    this.isPlayingAudio = true;
                    
                    const playlist = [];
                    const chars = queueNumber.split('');
                    
                    // Helper to get full URL
                    const getUrl = (key) => {
                        const url = this.audioFragments[key];
                        if (!url) return null;
                        // If URL is relative, prepend base path
                        if (url.startsWith('/')) {
                            return this.basePath + url;
                        }
                        return url;
                    };
                    
                    if (isRecall && recallCount > 0) {
                        playlist.push(getUrl('panggilan-ulang'));
                        playlist.push(getUrl('ke'));
                        playlist.push(getUrl('num-' + (recallCount > 9 ? 9 : recallCount)));
                    }
                    
                    // Always add: nomor-antrian + queue number + silakan-menuju + loket + counter
                    playlist.push(getUrl('nomor-antrian'));
                    
                    chars.forEach(char => {
                        if (/[A-Za-z]/.test(char)) {
                            playlist.push(getUrl('letter-' + char.toUpperCase()));
                        } else if (/[0-9]/.test(char)) {
                            playlist.push(getUrl('num-' + char));
                        }
                    });
                    
                    playlist.push(getUrl('silakan-menuju'));
                    playlist.push(getUrl('loket'));
                    
                    const counterNum = counter.replace(/[^0-9]/g, '');
                    if (counterNum) {
                        playlist.push(getUrl('counter-' + counterNum));
                    }
                    
                    // Filter out null values
                    const validPlaylist = playlist.filter(url => url !== null);
                    
                    console.log('Playing playlist:', validPlaylist);
                    
                    if (validPlaylist.length === 0) {
                        console.error('No valid audio files found');
                        this.isPlayingAudio = false;
                        return;
                    }
                    
                    // Use Web Audio API for gapless playback
                    this.playBuffersSequentially(validPlaylist);
                },
                
                playSequentially(urls, index) {
                    // This function is deprecated, use playBuffersSequentially instead
                    console.warn('playSequentially is deprecated, using playBuffersSequentially');
                },
                
                testAudio() {
                    console.log('Testing audio...');
                    console.log('Fragments:', this.audioFragments);
                    console.log('Base Path:', this.basePath);
                    console.log('Audio Available:', this.audioFragmentsAvailable);
                    
                    if (!this.audioFragmentsAvailable) {
                        console.error('❌ Audio files not available');
                        return;
                    }
                    
                    if (!this.audioContext) {
                        this.initWebAudio();
                    }
                    
                    // Test with first available audio
                    const firstKey = Object.keys(this.audioFragments)[0];
                    if (firstKey) {
                        let url = this.audioFragments[firstKey];
                        if (url.startsWith('/')) {
                            url = this.basePath + url;
                        }
                        this.loadAudioBuffer(url).then(buffer => {
                            if (buffer) {
                                // Play single test sound
                                const source = this.audioContext.createBufferSource();
                                source.buffer = buffer;
                                source.connect(this.audioContext.destination);
                                source.start(0);
                                console.log('✅ Test audio played successfully');
                            }
                        });
                    }
                },
                
                closeFlash() {
                    // Only used to stop flashing animation, audio continues independently
                    this.isFlashing = false;
                }
            }
        }
    </script>
</div>
