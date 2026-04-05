<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#1e3a5f">
    <title>E-Antrian Kunjungan Lapas Sumbawa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Prevent horizontal scroll on mobile */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        /* Improve touch targets */
        @media (max-width: 640px) {
            button, a, input, select, textarea {
                min-height: 44px;
                min-width: 44px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <!-- Header with Logo - Mobile Optimized -->
    <header class="bg-gradient-to-r from-[#1e3a5f] to-[#2d4a6f] border-b-4 border-[#c9a227] sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Logo -->
                    <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-md p-1">
                        <img src="{{ asset('logo.png') }}" alt="Logo Kemenkumham" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-sm sm:text-lg font-bold text-white truncate">E-Antrian Lapas Sumbawa</h1>
                        <p class="text-[10px] sm:text-xs text-[#c9a227] hidden sm:block">Kementerian Hukum dan HAM RI</p>
                    </div>
                </div>
                <a href="{{ route('admin.login') }}" class="text-xs sm:text-sm font-medium text-[#c9a227] hover:text-[#ffd700] px-3 py-2 border border-[#c9a227] rounded-lg hover:bg-[#c9a227]/10 transition-all">
                    Login Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content - Mobile First -->
    <main class="flex-grow flex items-start sm:items-center justify-center py-6 sm:py-10 lg:py-16 px-3 sm:px-4 lg:px-8">
        <div class="w-full max-w-md lg:max-w-lg space-y-5 sm:space-y-8">
            
            <!-- Institution Logo Banner -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-[#1e3a5f]/20 p-6 sm:p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 bg-white rounded-full flex items-center justify-center shadow-xl p-3">
                        <img src="{{ asset('logo.png') }}" alt="Logo Kemenkumham" class="w-full h-full object-contain">
                    </div>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-[#1e3a5f] mb-1">Lapas Kelas IIA Sumbawa</h2>
                <p class="text-sm text-gray-600">Kementerian Hukum dan Hak Asasi Manusia</p>
                <p class="text-xs text-gray-500 mt-1">Republik Indonesia</p>
            </div>
            
            <!-- Current Queue Display - Mobile Optimized -->
            <div class="bg-gradient-to-br from-[#1e3a5f] to-[#2d4a6f] rounded-xl sm:rounded-2xl shadow-xl border-4 border-[#c9a227] p-5 sm:p-8 text-center text-white">
                <p class="text-xs sm:text-sm font-medium text-[#c9a227] uppercase tracking-wide mb-2 sm:mb-3">Antrian Saat Ini</p>
                <div class="text-6xl sm:text-7xl lg:text-8xl font-black text-white mb-2 sm:mb-3 leading-tight drop-shadow-lg">{{ $currentQueueNumber }}</div>
                <p class="text-sm sm:text-base text-white/90">Sesi {{ $currentSession }}</p>
            </div>

            <!-- Take Queue Button - Large Touch Target -->
            <div class="text-center px-1 sm:px-0">
                <a href="{{ route('ambil-antrian') }}" 
                   class="inline-flex items-center justify-center w-full px-6 sm:px-10 py-5 sm:py-6 text-lg sm:text-xl font-bold rounded-xl text-[#1e3a5f] bg-[#c9a227] hover:bg-[#ffd700] transition-all shadow-xl hover:shadow-2xl transform hover:scale-[1.02] active:scale-95 border-4 border-[#1e3a5f]">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="truncate">Ambil Antrian Kunjungan</span>
                </a>
                <p class="mt-3 sm:mt-4 text-sm sm:text-base text-gray-600 px-2">Klik untuk mendaftar antrian kunjungan online</p>
            </div>

            <!-- Service Hours - Mobile Optimized -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-md border-2 border-[#1e3a5f]/20 p-5 sm:p-6">
                <h3 class="text-base sm:text-lg font-bold text-[#1e3a5f] mb-4 sm:mb-5 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-[#c9a227] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">Jam Pelayanan Kunjungan</span>
                </h3>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 sm:p-4 bg-[#c9a227]/10 rounded-lg border-l-4 border-[#c9a227] gap-1 sm:gap-0">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-[#c9a227] rounded-full mr-3 flex-shrink-0"></span>
                            <span class="text-sm font-semibold text-[#1e3a5f]">Sesi Pagi</span>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-[#1e3a5f] pl-6 sm:pl-0">{{ $serviceHours['pagi'][0] }} - {{ $serviceHours['pagi'][1] }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 sm:p-4 bg-[#1e3a5f]/10 rounded-lg border-l-4 border-[#1e3a5f] gap-1 sm:gap-0">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-[#1e3a5f] rounded-full mr-3 flex-shrink-0"></span>
                            <span class="text-sm font-semibold text-[#1e3a5f]">Sesi Siang</span>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-[#1e3a5f] pl-6 sm:pl-0">{{ $serviceHours['siang'][0] }} - {{ $serviceHours['siang'][1] }}</span>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <a href="{{ route('cek-status') }}" class="bg-white rounded-lg shadow-md border-2 border-[#1e3a5f]/20 p-4 text-center hover:shadow-lg hover:border-[#c9a227] transition-all">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 mx-auto mb-2 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-xs sm:text-sm font-semibold text-[#1e3a5f]">Cek Status Antrian</p>
                </a>
                <div class="bg-white rounded-lg shadow-md border-2 border-[#1e3a5f]/20 p-4 text-center">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 mx-auto mb-2 text-[#c9a227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs sm:text-sm font-semibold text-[#1e3a5f]">Panduan Kunjungan</p>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer - Mobile Optimized -->
    <footer class="bg-[#1e3a5f] text-white py-5 sm:py-8 border-t-4 border-[#c9a227]">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
            <div class="text-center">
                <div class="flex justify-center mb-3">
                    <div class="w-12 h-12 bg-white rounded-lg p-1.5">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                </div>
                <p class="text-sm sm:text-base font-semibold text-[#c9a227]">&copy; {{ date('Y') }} Lapas Kelas IIA Sumbawa</p>
                <p class="text-xs sm:text-sm text-white/80 mt-1">Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia</p>
            </div>
        </div>
    </footer>
</body>
</html>
